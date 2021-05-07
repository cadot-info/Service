<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class FileFunctions
{
    public function extension($file): string
    {
        return (strtolower(pathinfo($file, PATHINFO_EXTENSION)));
    }
    /**
     * pour extraine un string entre 2 string ou charactères
     *
     * @param string $string    chaine
     * @param string $stringdeb chaine de début
     * @param string $stringfin chaine de fin
     * @return string
     */
    public function chaine_extract($string, $stringdeb, $stringfin)
    {
        $deb = strpos($string, $stringdeb);
        $fin = strpos($string, $stringfin, $deb);
        return substr($string, $deb + strlen($stringdeb), $fin - $deb - strlen($stringdeb));
    }
    function copydir($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copydir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    $this->copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    function deletedir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deletedir($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
    function move($origine, $destination)
    {

        //création des réperoitre de destination s'il n'existe pas
        $dir = explode('/', $destination);
        unset($dir[count($dir) - 1]);
        $ddir = '';
        foreach ($dir as $val) {
            $ddir .= $val . '/';
            if (!file_exists($ddir)) mkdir($ddir);
        }
        rename($origine, $destination);
    }
}
