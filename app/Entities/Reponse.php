<?php

namespace App\Entities;

use App\Traits\Fetchable;
use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="reponses")
 * @Gedmo\Uploadable(
 *    filenameGenerator="SHA1", 
 *    maxSize=5000000,
 *    pathMethod="getCheminUpload")
 */
class Reponse
{
    use Fetchable;

    /**
     * L'identifiant unique de la réponse.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Le numéro de la réponse, qui doit correspondre
     * au numéro de l'exercice auquel on répond.
     *
     * @ORM\Column(type="integer")
     */
    protected $numero;

    /**
     * L'exercice auquel on répond.
     *
     * @ORM\ManyToOne(targetEntity="Exercice", inversedBy="reponses")
     * @ORM\JoinColumn(name="exercice_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $exercice;

    /**
     * Le rendu dont fait partie la réponse.
     *
     * @ORM\ManyToOne(targetEntity="Rendu", inversedBy="reponses")
     */
    protected $rendu;

    /**
     * Le contenu de la réponse.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $contenu;

    /**
     * Le chemin de la pièce jointe liée à la réponse,
     * s'il en existe une.
     *
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\UploadableFilePath
     */
    protected $chemin;

    /**
     * Renvoie l'identifiant unique de la réponse.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Renvoie le numéro de la réponse.
     *
     * @return int
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Remplace le numéro de la réponse.
     *
     * @param int $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * Renvoie l'exercice auquel on répond.
     *
     * @return Exercice
     */
    public function getExercice()
    {
        return $this->exercice;
    }

    /**
     * Remplace l'exercice auquel on répond.
     *
     * @param Exercice $exercice
     */
    public function setExercice($exercice)
    {
        $this->exercice = $exercice;
    }

    /**
     * Renvoie le rendu dont fait partie la réponse.
     *
     * @return Rendu
     */
    public function getRendu()
    {
        return $this->rendu;
    }

    /**
     * Remplace le rendu dont fait partie la réponse.
     *
     * @param Rendu $rendu
     */
    public function setRendu($rendu)
    {
        $this->rendu = $rendu;
    }

    /**
     * Renvoie le contenu de la réponse.
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Remplace le contenu de la réponse.
     *
     * @param string $contenu
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    /**
     * Renvoie le chemin de la pièce jointe liée à la réponse,
     * s'il en existe une.
     *
     * @return string
     */
    public function getChemin()
    {
        return $this->chemin;
    }

    /**
     * Modifie le chemin de la pièce jointe liée à la réponse.
     *
     * @param  string $chemin
     * @return string
     */
    public function setChemin($chemin)
    {
        $this->chemin = $chemin;
    }

    /**
     * Renvoie le nom de fichier utilisé pour désigner
     * la réponse au sein d'une archive de rendu.
     *
     * On déduit l'extension à utiliser du type de
     * l'exercice.
     *
     * @return string
     */
    public function getNomFichier()
    {
        $extensions = config('extensions');

        $nom = $this->getExercice()->getFiche()->getTitre() . '.' . $this->getNumero();
        $type = $this->getExercice()->getType();

        if (isset($extensions[$type])) {
            $extension = '.' . $extensions[$type];
        } else {
            $extension = '';
        }

        return $nom . $extension;
    }

    /**
     * Renvoie le nom de fichier utilisé pour désigner
     * la pièce-jointe au sein d'une archive de rendu.
     *
     * @return string
     */
    public function getNomFichierJoint()
    {
        $nom = $this->getExercice()->getFiche()->getTitre() . '.' . $this->getNumero() . ' - Fichier joint';
        $type = $this->getExercice()->getType();

        if (!empty($this->getChemin())) {
            $info = pathinfo($this->getChemin());
            $extension = '.' . $info['extension'];
        } else {
            $extension = '';
        }

        return $nom . $extension;
    }

    /**
     * Renvoie le chemin qui servira à stocker les fichiers
     * envoyés en pièce-jointe à des réponses.
     */
    public function getCheminUpload()
    {
        return storage_path('uploads');
    }
}