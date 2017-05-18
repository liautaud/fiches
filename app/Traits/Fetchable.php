<?php

namespace App\Traits;

use \EntityManager as Manager;

use Doctrine\Common\Collections\Criteria;

trait Fetchable
{
    /**
     * Renvoie l'ensemble des entités en base de données.
     *
     * @return array[static]
     */
    public static function all()
    {
        return Manager::getRepository(static::class)->findAll();
    }

    /**
     * Renvoie l'ensemble des entités en base de données
     * avec un identifiant unique strictement positif.
     *
     * @return static[]
     */
    public static function allPositive()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gt('id', 0));

        return Manager::getRepository(static::class)->matching($criteria);
    }
}