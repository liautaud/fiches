<?php

namespace App\Http\Controllers;

use splitbrain\PHPArchive\Tar;
use splitbrain\PHPArchive\Archive;

use App\Entities\Rendu;
use App\Entities\Utilisateur;
use Illuminate\Http\Request;

use LaravelDoctrine\ORM\Facades\EntityManager as Manager;

class RendusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $utilisateur = $request->user();
        $rendus = collect([]);

        // On affiche uniquement les rendus non encore traités
        // des élèves du chargé de groupe actuellement connecté.
        foreach ($utilisateur->getEleves() as $eleve) {
            foreach ($eleve->getRendus() as $rendu) {
                if ($rendu->getEtat() == Rendu::ETAT_SOUMIS) {
                    $rendus->push($rendu);
                }
            }
        }
        
        $rendus = $rendus->sortBy(function ($rendu) {
        	return $rendu->getDateCreation();
        });

        return view('gestion.rendus.index', compact('rendus'));
    }

    /**
     * Affiche un rendu en utilisant la même interface
     * que celle d'écriture des rendus.
     *
     * @param  Rendu $rendu
     * @return \Illuminate\Http\Response
     */
    public function afficher(Rendu $rendu)
    {
        if ( ! $rendu->getId()) {
            return abort(404);
        }

        $fiche = $rendu->getFiche();
        $peutSoumettre = false;
        $afficherNomPrenom = true;
        $dernierRendu = $rendu;

        return view('rendre', compact('fiche', 'peutSoumettre', 'afficherNomPrenom', 'dernierRendu'));
    }

    /**
     * Génère une archive .tar.gz contenant l'ensemble
     * des réponses d'un rendu à la volée, et retourne
     * son contenu.
     *
     * @param  Rendu $rendu
     * @return \Illuminate\Http\Response
     */
    public function telecharger(Rendu $rendu)
    {
        if ( ! $rendu->getId()) {
            return abort(404);
        }

        // On crée une archive .tar.gz dans un dossier
        // temporaire, que l'on supprimera une fois
        // la réponse bien transmise.
        $archive = new Tar();
        $archive->setCompression(9, Archive::COMPRESS_BZIP);

        $chemin = tempnam(sys_get_temp_dir(), 'fiches_');
        $archive->create($chemin);

        // On ajoute un fichier à l'archive pour chaque
        // réponse du rendu.
        foreach ($rendu->getReponses() as $reponse) {
            if (!empty($reponse->getChemin())) {
                $archive->addFile($reponse->getChemin(), $reponse->getNomFichierJoint());
            } 
            
            if (!empty($reponse->getContenu())) {
                $archive->addData($reponse->getNomFichier(), $reponse->getContenu());
            }
        }

        $archive->close();
        
        // On renvoie l'archive en réponse, avec un nom
        // de fichier bien choisi.
        $nom = $rendu->getFiche()->getTitre() . ' - ' . $rendu->getUtilisateur()->getNomComplet();
        $response = response()->download($chemin, $nom . '.tar.gz');
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * Valide un rendu, en vérifiant que l'utilisateur
     * courant est bien en charge de l'élève ayant
     * envoyé le rendu.
     *
     * @param  Request $request
     * @param  Rendu   $rendu
     * @return \Illuminate\Http\Response
     */
    public function valider(Request $request, Rendu $rendu)
    {
        return $this->changerEtat($request, $rendu, Rendu::ETAT_VALIDE);
    }

    /**
     * Refuse un rendu, en vérifiant que l'utilisateur
     * courant est bien en charge de l'élève ayant
     * envoyé le rendu.
     *
     * @param  Request $request
     * @param  Rendu   $rendu
     * @return \Illuminate\Http\Response
     */
    public function refuser(Request $request, Rendu $rendu)
    {
        return $this->changerEtat($request, $rendu, Rendu::ETAT_REFUSE);
    }

    /**
     * Change l'état d'un rendu donné, en vérifiant que 
     * l'utilisateur courant est bien en charge de l'élève
     * ayant envoyé le rendu.
     *
     * @param  Request $request
     * @param  Rendu   $rendu
     * @param  int     $etat
     * @return \Illuminate\Http\Response
     */
    protected function changerEtat(Request $request, Rendu $rendu, $etat)
    {
        if ( ! $rendu->getId()) {
            return abort(404);
        }

        $utilisateur = $request->user();
        $responsable = $rendu->getUtilisateur()->getResponsable();

        // On vérifie que l'utilisateur courant a bien le
        // droit de changer l'état du rendu.
        if ( ! $utilisateur->isAdministrateur() &&
             ! is_null($responsable) && $responsable->getId() != $utilisateur->getId()) {
            return abort(401);
        }
        
        $rendu->setEtat($etat);
        $rendu->setDateTraitement(new \DateTime());
        Manager::persist($rendu);
        Manager::flush();

        return ['success' => true];
    }
}
