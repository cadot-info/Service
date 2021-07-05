<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class FileFunctions
{
    // pour extraire l'extension d'un fichier
    public function extension($file): string
    {
        return (strtolower(pathinfo($file, PATHINFO_EXTENSION)));
    }

    // for copy dir with recursivity
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
    //remove dir with recursivity
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
    //move directory with recursivity
    function movedir($origine, $destination)
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
