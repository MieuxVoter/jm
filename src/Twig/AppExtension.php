<?php
/**
 * Project : jm
 * File : AppExtension.php
 */

namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction('publicFile', array($this, 'publicFile')),
            new TwigFunction('jsonDecode',  array($this, 'jsonDecode')),
            new TwigFunction('linkParser',  array($this, 'linkParser'))
        );
    }

    public function getFilters()
    {
        return [
            new TwigFilter('linkParser', [$this, 'linkParser']),
        ];
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

    public function jsonDecode($string)
    {
        return json_decode($string,true);
    }

    public function linkParser($string)
    {

        $string=preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '$1 <a href="$2">$3</a>', $string);
        return $string;
    }
}