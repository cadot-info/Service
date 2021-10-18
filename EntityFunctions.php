<?php

namespace App\CMService;

use Gumlet\ImageResize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;

/* ------------------------------------------------------------------------------------------------------------------ */
/*                               FUNCTION QUI PERMET DE RÉARRANGÉ LES DONNÉES D'UNE ENTITÉE EN FONCTION DES SORTABLES */
/* ------------------------------------------------------------------------------------------------------------------ */
// exemple dans un controller et avec un trie des données ensuite
//   foreach ($EntityFunctions->reorder('modele') as  $modele) {
//             $options = $modele->getOptions();
//             if (in_array('nouveaute', $options) && !in_array('concept', $options)) {
//                 $nouveautes[] = $modele;
//             }
//         }

class EntityFunctions
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
        if (is_null($donnees)) {
            $donnees = $this->em->getRepository("App:" . ucfirst($repository))->findall();
        }
        if ($donnees) {
            //on récupère le trie enregistré dans la bd
            if ($base = $this->em->getRepository("App:CM\Sortable")->findOneBy(['entite' => ucfirst($repository)])) {
                $tab = $this->sortArrayObjetByArray($donnees, 'getId', $base->getordre());
            } else {
                $tab = $donnees;
            }
        } else {
            return [];
        }
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
        if ($removeDoublon) {
            return implode(',', array_unique($tabres));
        } else {
            return implode(',', $tabres);
        }
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
    public function uploadForFile($entity, $tmp, $name)
    {
        $ff = new FileFunctions();
        //create directory
        @mkdir('uploads');
        @mkdir('uploads/' . $entity);
        $destName = 'uploads/' . $entity . '/' . uniqid() . '_' . $ff->sanitize($name);
        rename($tmp, $destName);
        return $destName;
    }

    public function saveImgFromBlocks($tab)
    {
        $slugger = new AsciiSlugger();
        foreach ($tab->blocks as $key => $value) {
            //pour les types images
            if ($value->type == 'image') {
                $pname = $value->data->url;
                $name = substr($pname, 0, strpos($pname, "?"));
                $headers = @get_headers($value->data->url);
                if ($headers && strpos($headers[0], '200')) {  //verif url
                    $safeFilename = $slugger->slug($name);
                    $extension = pathinfo($name, PATHINFO_EXTENSION) != '' ? pathinfo($name, PATHINFO_EXTENSION) : 'jpg';
                    $fileName = '/embed/' . $safeFilename . '-' . uniqid() . '.' . $extension;
                    if (file_put_contents(getcwd() .  $fileName, file_get_contents($name))) { //try write the file
                        $image = new ImageResize(getcwd() .  $fileName);
                        $image->resizeToBestFit(1000, 1000);
                        $image->$image->quality_jpg = 60;
                        $image->quality_png = 60;
                        $image->save(getcwd() .  $fileName);
                        $value->data->url =  $fileName;
                    }
                }
                //base 64 img
                if (substr($value->data->url, 0, strlen('data:image')) == 'data:image') {
                    $fin = strpos($value->data->url, ';base64');
                    //data:image/png;base64
                    $ext = substr($value->data->url, strlen('data:image/'), $fin - strlen('data:image/'));
                    $fileName = '/embed/' . uniqid() . '.' . $ext;
                    if (file_put_contents(getcwd() . $fileName, base64_decode(explode(',', $value->data->url)[1]))) {
                        $image = new ImageResize(getcwd() .  $fileName);
                        $image->resizeToBestFit(1920, 1080);
                        $image->save(getcwd() .  $fileName);
                        $value->data->url = $fileName;
                    }
                }
            }
        }
        return $tab;
    }
}
