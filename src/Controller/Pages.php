<?php
/**
 * Project : jm
 * File : Pages.php
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;

class Pages extends AbstractController
{

    public function home(){
        $data=[];
        return $this->render('pages/home.html.twig', $data);
    }

    public function privacyPolicy(){
        $data=[];
        return $this->render('pages/privacy-policy.html.twig', $data);
    }
}