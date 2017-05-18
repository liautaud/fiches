<?php

namespace App\Entities;

use App\Traits\Fetchable;
use Illuminate\Contracts\Auth\Authenticatable;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="utilisateurs")
 */
class Utilisateur implements Authenticatable
{
    use Fetchable;

    const STATUT_ELEVE          = 0;
    const STATUT_CHARGE_GROUPE  = 1;
    const STATUT_ADMINISTRATEUR = 2;

    /**
     * L'identifiant unique de l'utilisateur.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Le nom de l'utilisateur.
     *
     * @ORM\Column(type="string")
     */
     protected $nom;

    /**
     * Le prénom de l'utilisateur.
     *
     * @ORM\Column(type="string")
     */
     protected $prenom;

    /**
     * L'adresse e-mail de l'utilisateur.
     *
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * Le mot de passe chiffré de l'utilisateur.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * Le statut de l'utilisateur, qui peut être
     * soit un élève, soit un chargé de groupe.
     *
     * @ORM\Column(type="integer")
     */
    protected $statut;

    /**
     * La promotion de l'utilisateur.
     *
     * @ORM\Column(type="integer")
     */
    protected $promotion;

    /**
     * Le token utilisé pour générer le lien
     * permettant à l'utilisateur de choisir
     * son mot de passe en fin d'inscription.
     *
     * @ORM\Column(name="inscription_token", type="string", nullable=true)
     */
    protected $inscriptionToken;

    /**
     * Le token utilisé pour se souvenir d'un
     * utilisateur aux connexions suivantes.
     *
     * @ORM\Column(name="remember_token", type="string", nullable=true)
     */
    protected $rememberToken;

    /**
     * Les rendus de fiche de l'utilisateur.
     *
     * @ORM\OneToMany(targetEntity="Rendu", mappedBy="utilisateur")
     * @ORM\OrderBy({"dateCreation" = "ASC"})
     */
    protected $rendus;

    /**
     * Le chargé de groupe responsable de l'utilisateur.
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur", inversedBy="eleves")
     * @ORM\JoinColumn(name="responsable_id", referencedColumnName="id")
     * @var Utilisateur
     */
    protected $responsable;

    /**
     * L'ensemble des élèves dont l'utilisateur est responsable.
     *
     * Cet attribut ne sert que dans le cas où l'utilisateur est
     * lui-même un chargé de groupe.
     *
     * @ORM\OneToMany(targetEntity="Utilisateur", mappedBy="responsable")
     */
    protected $eleves;

    /**
     * Initialise un utilisateur.
     */
    public function __construct()
    {
        $this->rendus = new ArrayCollection;
        $this->eleves = new ArrayCollection;
    }

    /**
     * Renvoie l'identifiant unique de l'utilisateur.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Renvoie le nom de l'utilisateur.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Remplace le nom de l'utilisateur.
     *
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * Renvoie le prénom de l'utilisateur.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Remplace le prénom de l'utilisateur.
     *
     * @param string $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * Renvoie le nom complet de l'utilisateur.
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->getPrenom() . ' ' . $this->getNom();
    }

    /**
     * Renvoie l'adresse e-mail de l'utilisateur.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Remplace l'adresse e-mail de l'utilisateur.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Renvoie le mot de passe chiffré de l'utilisateur.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Remplace le mot de passe chiffré de l'utilisateur.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Renvoie le statut de l'utilisateur.
     *
     * @return int
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Modifie le statut de l'utilisateur.
     *
     * @param int $statut
     */
    public function setStatut($statut)
    {
        if ( ! in_array($statut, [static::STATUT_ELEVE, static::STATUT_CHARGE_GROUPE, static::STATUT_ADMINISTRATEUR])) {
            return;
        }

        $this->statut = $statut;
    }

    /**
     * Renvoie la promotion de l'utilisateur.
     *
     * @return int
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Modifie la promotion de l'utilisateur.
     *
     * @param int $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * Renvoie le token utilisé lors de l'inscription.
     *
     * @return string|null
     */
    public function getInscriptionToken()
    {
        return $this->inscriptionToken;
    }


    /**
     * Génère un nouveau token pour l'inscription.
     */
    public function generateInscriptionToken()
    {
        $this->inscriptionToken = str_random(30);
    }

    /**
     * Supprime le token pour l'inscription, de sorte
     * à éviter qu'on puisse ré-utiliser le lien de
     * définition du mot de passe une fois
     * l'inscription terminée.
     */
    public function removeInscriptionToken()
    {
        $this->inscriptionToken = null;
    }

    /**
     * Renvoie si l'utilisateur est un élève.
     *
     * @return bool
     */
    public function isEleve() {
        return $this->getStatut() == static::STATUT_ELEVE;
    }

    /**
     * Renvoie si l'utilisateur est un chargé de groupe.
     *
     * @return bool
     */
    public function isChargeGroupe() {
        return $this->getStatut() == static::STATUT_CHARGE_GROUPE;
    }

    /**
     * Renvoie si l'utilisateur est un administrateur.
     *
     * @return bool
     */
    public function isAdministrateur() {
        return $this->getStatut() == static::STATUT_ADMINISTRATEUR;
    }

    /**
     * Renvoie si l'utilisateur a la permission de gérer le site.
     *
     * @return bool
     */
    public function isGestionnaire() {
        return $this->isChargeGroupe() || $this->isAdministrateur();
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur.
     *
     * @return ArrayCollection[Rendu]
     */
    public function getRendus()
    {
        return $this->rendus;
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur
     * qui ont été soumis.
     *
     * @return ArrayCollection[Rendu]
     */
    public function getRendusSoumis()
    {
        return $this->rendus->filter(function (Rendu $rendu) {
            return $rendu->getEtat() >= Rendu::ETAT_SOUMIS;
        });
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur
     * qui ont été validés.
     *
     * @return ArrayCollection[Rendu]
     */
    public function getRendusValides()
    {
        return $this->rendus->filter(function (Rendu $rendu) {
            return $rendu->getEtat() == Rendu::ETAT_VALIDE;
        });
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur
     * correspondant à une fiche donnée.
     *
     * @param  Fiche $fiche
     * @return ArrayCollection[Rendu]
     */
    public function getRendusForFiche(Fiche $fiche)
    {
        return $this->getRendus()->filter(function (Rendu $rendu) use ($fiche) {
            return $rendu->getFiche()->getId() == $fiche->getId();
        });
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur
     * correspondant à une fiche donnée qui ont été soumis.
     *
     * @param  Fiche $fiche
     * @return ArrayCollection[Rendu]
     */
    public function getRendusSoumisForFiche(Fiche $fiche)
    {
        return $this->getRendusForFiche($fiche)->filter(function (Rendu $rendu) {
            return $rendu->getEtat() >= Rendu::ETAT_SOUMIS;
        });
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur
     * correspondant à toutes les fiches d'un langage donné.
     *
     * @param  Langage $langage
     * @return ArrayCollection[Rendu]
     */
    public function getRendusForLangage(Langage $langage)
    {
        return $this->getRendus()->filter(function (Rendu $rendu) use ($langage) {
            if ($rendu->getEtat() == Rendu::ETAT_BROUILLON) {
                return false;
            }

            return $rendu->getFiche()->getLangage()->getId() == $langage->getId();
        });
    }

    /**
     * Renvoie les rendus de fiche de l'utilisateur
     * correspondant à toutes les fiches d'un langage
     * donné qui ont étés validés.
     *
     * @param  Langage $langage
     * @return ArrayCollection[Rendu]
     */
    public function getRendusValidesForLangage(Langage $langage)
    {
        return $this->getRendusForLangage($langage)->filter(function (Rendu $rendu) {
            return $rendu->getEtat() == Rendu::ETAT_VALIDE;
        });
    }

    /**
     * Renvoie le plus récent rendu de fiche de l'utilisateur
     * correspondant à une fiche en particulier.
     *
     * @param  Fiche $fiche
     * @return Rendu|null
     */
    public function getDernierRenduForFiche(Fiche $fiche)
    {
        return $this->getRendusForFiche($fiche)->last();
    }

    /**
     * Renvoie le chargé de groupe responsable de l'utilisateur.
     *
     * @return Utilisateur
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Modifie le chargé de groupe responsable de l'utilisateur.
     *
     * @param Utilisateur $responsable
     */
    public function setResponsable($responsable)
    {
        if ($responsable && ! $responsable->getStatut() == static::STATUT_CHARGE_GROUPE) {
            return;
        }

        $this->responsable = $responsable;
    }

    /**
     * Renvoie l'ensemble des élèves dont l'utilisateur est responsable.
     *
     * @return ArrayCollection[Utilisateur]
     */
    public function getEleves()
    {
    	if ($this->isAdministrateur()) {
    		return Utilisateur::all();
    	} else {
	        return $this->eleves;
	    }
    }

    /**
     * Renvoie si l'utilisateur donné est autorisé à consulter
     * une fiche donnée.
     *
     * Ce n'est le cas que lorsque toutes les fiches dont dépend
     * celle-ci ont été rendues et ne sont pas refusées.
     *
     * @param  Fiche $fiche
     * @return bool
     */
    public function canAccess(Fiche $fiche)
    {
        return $fiche->getEtatForUtilisateur($this) >= Fiche::ETAT_ACCESSIBLE;
    }

    /**
     * Renvoie si l'utilisateur est autorisé à soumettre un
     * rendu pour une fiche donnée.
     *
     * Ce n'est le cas que lorsqu'aucun rendu n'a encore été
     * soumis pour la fiche, ou lorsque tous les rendus soumis
     * jusque-là ont été refusés.
     *
     * @param  Fiche $fiche
     * @return bool
     */
    public function canSubmit(Fiche $fiche)
    {
        return in_array(
            $fiche->getEtatForUtilisateur($this),
            [Fiche::ETAT_ACCESSIBLE, Fiche::ETAT_REFUSEE]
        );
    }

    /**
     * Renvoie un tableau représentant le graphe de toutes les
     * fiches en base de données, chaque fiche étant légendée
     * selon son état pour l'utilisateur courant.
     *
     * @return array
     */
    public function getGrapheUtilisateur()
    {
        $graphe = Fiche::getGraphe();
        $nodes = [];

        $colors = [
            Fiche::ETAT_INACCESSIBLE => 'grey',
            Fiche::ETAT_ACCESSIBLE   => 'yellow',
            Fiche::ETAT_RENDUE       => 'blue',
            Fiche::ETAT_VALIDEE      => 'green',
            Fiche::ETAT_REFUSEE      => 'red'
        ];

        foreach ($graphe['nodes'] as $i => $node) {
            $fiche = $node['fiche'];
            $etat = $fiche->getEtatForUtilisateur($this);

            $id = $fiche->getId();
            $label = $fiche->getTitre();
            $url = route('rendre', $fiche->getId());

            if ($fiche->getId() > 0) {
                $color = $colors[$etat];
            } else {
                $color = 'zero';
            }

            $nodes[$id] = compact('id', 'label', 'color', 'url');
        }

        return ['nodes' => $nodes, 'edges' => $graphe['edges']];
    }

    /**
     * Renvoie un tableau représentant le graphe de toutes les
     * fiches en base de données, chaque fiche étant légendée
     * selon que les élèves de l'utilisateur courant l'ont
     * validée ou non.
     *
     * @return array
     */
    public function getGrapheGestionnaire()
    {
        $graphe = Fiche::getGraphe();
        $nodes = [];

        foreach ($graphe['nodes'] as $i => $node) {
            $fiche = $node['fiche'];

            // On ne conserve que les rendus des élèves
            // dont est chargé l'utilisateur courant.
            $rendus = $fiche->getRendusSoumis()->filter(function (Rendu $rendu) {
                $responsable = $rendu->getUtilisateur()->getResponsable();

                return $responsable && $responsable->getId() == $this->getId();
            });

            $id = $fiche->getId();

            if ($fiche->getId() > 0) {
                // Si au moins un des rendus reste à traiter,
                // on l'affiche en jaune. Sinon, si au moins
                // un des rendus est validé, on l'affiche en
                // vert, et sinon en gris.
                $color = 'grey';
                $rendus->forAll(function ($index, Rendu $rendu) use (&$color) {
                    if ($rendu->getEtat() == Rendu::ETAT_SOUMIS) {
                        $color = 'yellow';
                    } else if ($color != 'yellow' && $rendu->getEtat() == Rendu::ETAT_VALIDE) {
                        $color = 'green';
                    }
                });
            } else {
                $color = 'zero';
            }

            $label = $fiche->getTitre();
            $url = route('rendre', $fiche->getId());

            $nodes[$id] = compact('id', 'label', 'color', 'url');
        }

        return ['nodes' => $nodes, 'edges' => $graphe['edges']];
    }

    /**
     * Renvoie un tableau du nombre de fiches par état
     * pour l'utilisateur courant.
     *
     * Notons qu'il n'est pas gênant de faire un appel 
     * à getGrapheUtilisateur() puis à un appel à
     * getStatistiquesUtilisateur(), puisque bien que
     * les deux fassent une requête pour récupérer 
     * toutes les fiches en base de données, le cache 
     * d'objets de Doctrine permet d'éviter la deuxième
     * requête.
     *
     * @return array
     */
    public function getStatistiquesUtilisateur()
    {
        $fiches = Fiche::allPositive();

        $rendues = 0;
        $accessibles = 0;
        $validees = 0;
        $refusees = 0;
        $miniProjets = 0;

        foreach ($fiches as $fiche) {
            $etat = $fiche->getEtatForUtilisateur($this);
            
            switch ($etat) {                
                case Fiche::ETAT_REFUSEE:
                    //$rendues++;
                    $refusees++;
                break;

                case Fiche::ETAT_VALIDEE:
                    //$rendues++;
                    $validees++;

                    if ($fiche->isMiniProjet()) {
                        $miniProjets++;
                    }
                break;

                case Fiche::ETAT_RENDUE:
                    $rendues++;
                break;

                case Fiche::ETAT_ACCESSIBLE:
                    $accessibles++;
                break;
            }
        }

        return compact('rendues', 'accessibles', 'validees', 'refusees', 'miniProjets');
    }

    /**
     * Renvoie un tableau des statistiques des
     * fiches traitées et validées par les
     * élèves de l'utilisateur courant.
     *
     * @return array
     */
    public function getStatistiquesGestionnaire()
    {
        $graphe = $this->getGrapheGestionnaire();

        // On se contente ici d'utiliser les données
        // provenant du graphe.
        $traiter = 0;
        $validees = 0;

        foreach ($graphe['nodes'] as $node) {
            switch ($node['color']) {
                case 'yellow':
                    $traiter++;
                break;
                case 'green':
                    $validees++;
                break;
            }
        }

        return compact('traiter', 'validees');
    }

    /**
     * Renvoie le champ utilisé pour identifier un utilisateur
     * lors de la connexion.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Renvoie la valeur du champ utilisé pour identifier
     * l'utilisateur lors de la connexion.
     *
     * @return string
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Renvoie le mot de passe chiffré de l'utilisateur.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    /**
     * Renvoie la valeur du token utilisé pour se
     * souvenir de l'utilisateur.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Modifie la valeur du token utilisé pour se
     * souvenir de l'utilisateur.
     *
     * @param  string $token
     * @return void
     */
    public function setRememberToken($token)
    {
        $this->rememberToken = $token;
    }

    /**
     * Renvoie le nom de la colonne qui contient le
     * token utilisé pour se souvenir de l'utilisateur.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }
}
