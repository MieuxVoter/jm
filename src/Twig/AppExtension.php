<?php
/**
 * Project : jm
 * File : AppExtension.php
 */

namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction('publicFile', array($this, 'publicFile'))
        );
    }

    /**
     * Renvoi le chemin du fichier en ajoutant une variable ?v= avec le filemtime du fichier
     * @param $path
     * @return string
     */
    public function publicFile($path)
    {
        $filemtime=filemtime("../public".$path);
        return $path."?v=".$filemtime;
    }
}