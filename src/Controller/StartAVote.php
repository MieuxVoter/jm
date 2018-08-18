<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StartAVote extends Controller
{
    public function form(){
        return $this->render('start-a-vote/form.html.twig', array());
    }
}