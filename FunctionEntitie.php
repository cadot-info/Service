<?php

namespace App\Service;

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



    public function reorder($repository): array
    {
        $array = [];
        $objet = $this->em->getRepository("App:" . ucfirst($repository))->findall();
        if ($base = $this->em->getRepository("App:Sortable")->findOneBy(['entite' => ucfirst($repository)])) {
            $sortable = explode(',', $base->getordre()); //tableau des ordres
            foreach ($sortable as $index => $num) {
                $res =  array_filter(
                    $objet,
                    function ($e) use (&$num) {
                        return $e->getId() == $num;
                    }
                );
                $array[$index] = reset($res);
            }
            return $array;
        } else return $objet;
    }
}
