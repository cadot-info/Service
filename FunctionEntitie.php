<?php

namespace App\CMService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/* ------------------------------------------------------------------------------------------------------------------ */
/*                               FUNCTION QUI PERMET DE RÉARRANGÉ LES DONNÉES D'UNE ENTITÉE EN FONCTION DES SORTABLES */
/* ------------------------------------------------------------------------------------------------------------------ */
// exemple dans un controller et avec un trie des données ensuite
//   foreach ($functionEntitie->reorder('modele') as  $modele) {
//             $options = $modele->getOptions();
//             if (in_array('nouveaute', $options) && !in_array('concept', $options)) {
//                 $nouveautes[] = $modele;
//             }
//         }

class FunctionEntitie
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    // renvoie un tableau réorganiser d'un tableau ou d'un repository complet
    // si pas de donnees, on fait un findall 
    public function reorder($repository, $donnees = null): array
    {
        $array = [];
        //si on a pas données un array on va chercher toutes les données du repository
        if (\is_null($donnees))
            $donnees = $this->em->getRepository("App:" . ucfirst($repository))->findall();
        //on récupère le trie enregitsré dans la bd
        if ($base = $this->em->getRepository("App:CM\Sortable")->findOneBy(['entite' => ucfirst($repository)])) {
            $sortable = explode(',', $base->getordre()); //tableau des ordres
            //on liste les tries
            foreach ($sortable as $index => $num) {
                $res =  array_filter(
                    $donnees,
                    function ($e) use (&$num) {
                        return $e->getId() == $num;
                    }
                );
                $array[$index] = reset($res);
            }
            return $array;
        } else return $donnees;
    }
    //funtion qui récupère toutes les données d'un field
    public function getAllOfFields($repository, $field, $removeDoublon = true): string
    {
        $tabres = [];
        foreach ($this->em->getRepository("App:" . ucfirst($repository))->findall() as $entitie) {
            $methode = 'get' . ucfirst($field);
            $tab = explode(",", $entitie->$methode());
            $tabres = array_merge($tabres, $tab);
        }
        if ($removeDoublon) return implode(',', array_unique($tabres));
        else return implode(',', $tabres);
    }
}
