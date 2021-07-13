<?php

namespace App\CMService;

use Symfony\Component\HttpFoundation\Response;

class String_functions
{
    /**
     * pour extraine un string entre 2 string ou charactères
     *
     * @param string $string    chaine
     * @param string $stringdeb chaine de début
     * @param string $stringfin chaine de fin
     * @return string
     */
    function chaine_extract($string, $stringdeb, $stringfin)
    {
        $deb = strpos($string, $stringdeb);
        $fin = strpos($string, $stringfin, $deb);
        return substr($string, $deb + strlen($stringdeb), $fin - $deb - strlen($stringdeb));
    }
    // chaine_remplace($html, 'background-image', ')', 'background-image: url({{file' . $file  . '}})');
    function chaine_remplace($html, $debs, $fins, $chaine, $start = 0)
    {
        $pos = strpos($html, $debs, $start);
        $fin = strpos($html, $fins, $pos + 1);
        $html = substr($html, $start, $pos) . $chaine . substr($html, $fin + 1);
    }
    function extract($str, $pos, $start, $end = '')
    {
        if ($end == '') $end = $start;
        $sub = strpos($str, $start, $pos); //on cherche la position du départ dans la chaine
        $sub += strlen($start); //on ajoute la longueur de la chaine départ
        $size = strpos($str, $end, $sub) - $sub; //on calcule la taille de la chaine-le départ
        return substr($str, $sub, $size); //on retourne la chaine
    }


    function insert($chaine, $strdeb, $insert, $after = true)
    {
        if ($after) $pos = strpos((string)$chaine, $strdeb) + strlen($strdeb);
        else $pos = strpos((string)$chaine, $strdeb);
        return substr_replace((string)$chaine, $insert, $pos, 0);
    }
    /* ------------------------------------------------------------------------------------------------------------------ */
    /*                                                       RÉCUPÈRE UN TEXTE ENTRE UNE BALISE (EXMPLE <H1> TEXTE </H1>) */
    /* ------------------------------------------------------------------------------------------------------------------ */
    function getTextBetweenTags($string, $tagname)
    {
        $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
        preg_match_all($pattern, $string, $matches);
        return $matches;
    }
    /* ------------------------------------------------------------------------------------------------------------------ */
    /*             EXTRAIT LE TEXTE ENTRE 2 BALISE (PAR EXEMPLE 'H1', VA RETOURNER LE TEXTE ENTRE <H1 ET LE PROCHAIN <H1) */
    /* ------------------------------------------------------------------------------------------------------------------ */
    function balise_extract($string, $balise)
    {
        $start = strpos($string, '<' . $balise);
        if ($start !== false) {
            $end = strpos($string, '>', $start);
            $start2 = strpos($string, '<' . $balise, $end);
            return substr($string, $start, $start2 - $start);
        } else
            return false;
    }
    function balise_extract_all($string, $balise, $offset = 0)
    {
        while ($start = strpos($string, '<' . $balise, $offset)) {
            $end = strpos($string, '>', $start);
            $start2 = strpos($string, '<' . $balise, $end);
            $res[] = substr($string, $start, $start2 - $start);
            $offset = strpos($string, '>', $start2);
        }
        return $res;
    }
}
