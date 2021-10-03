<?php

namespace App\CMService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;

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
    public function reorder($repository, $donnees = null, $limit = 0, $offset = 0): array
    {
        $tab = [];
        //si on a pas données un array on va chercher toutes les données du repository
        if (is_null($donnees))
            $donnees = $this->em->getRepository("App:" . ucfirst($repository))->findall();
        if ($donnees) {
            //on récupère le trie enregistré dans la bd
            if ($base = $this->em->getRepository("App:CM\Sortable")->findOneBy(['entite' => ucfirst($repository)])) {
                $tab = $this->sortArrayObjetByArray($donnees, 'getId', $base->getordre());
            } else {
                $tab = $donnees;
            }
        } else return [];
        $retour = $limit ? array_slice($tab, $offset, $limit) : array_slice($tab, $offset);
        return $retour;
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
    /**
     * Method sortArrayObjetByArray order a array of objetcs by another array or string 
     *
     * @param $objets array of objects
     * @param $function  getId, getName...
     * @param $array  array or string with the good range
     *
     * @return void
     */
    public function sortArrayObjetByArray($objets, $function, $array)
    {
        $reste = $objets;
        if (\is_string($array)) {
            $array = explode(',', $array);
        }
        $tab = [];
        foreach ($array as $num) {
            foreach ($objets as $key => $value) {
                if ($value->$function() == intval($num)) {
                    $tab[] = $objets[$key];
                    unset($reste[$key]);
                }
            }
        }
        return array_merge($tab, $reste);
    }
    function uploadForFile($entity, $tmp, $name)
    {
        $slugger = new AsciiSlugger();
        //create directory
        @mkdir('uploads');
        @mkdir('uploads/' . $entity);
        $destName = 'uploads/' . $entity . '/' . uniqid() . '¤' . $slugger->slug($name);
        rename($tmp, $destName);
        return $destName;
    }
}
