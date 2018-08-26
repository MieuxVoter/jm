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

        //si une proposition trouvée
        if(!is_null($proposal)){
            //si données envoyéé en post
            if(count($_POST)) {
                $error = 0;
                $dataForm["author"] = $_POST["author"] ?? "";
                $dataForm["proposal_id"] = $proposal->getId();
            }
        }



        $dataForm["proposal"]=$proposal;
        return $this->render('vote/form.html.twig', $dataForm);

    }
}