<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Proposal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Vote extends Controller
{
    public function form($url_key){

        $dataForm=[];
        $dataForm["url_key"]=$url_key;

        $repository = $this->getDoctrine()->getRepository(Proposal::class);
        $proposal = $repository->findOneBy(['url_key' => $url_key]);
        if(is_null($proposal)){

        }

        $dataForm["proposal"]=$proposal;
        return $this->render('vote/form.html.twig', $dataForm);

    }
}