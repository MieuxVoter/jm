<?php
/**
 * Project : jm
 * File : Clear.php
 */

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Clear extends Controller
{

    public function run(){
        /*$repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
        $proposal = $repositoryProposal->findBy(['']);*/

        return $this->render('clear.html.twig', []);
    }
}