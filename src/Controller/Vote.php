<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Participation;
use App\Entity\Proposal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Vote extends Controller
{
    public function form($url_key){




        $isSaved=false;

        $redirectSave=$_GET["redirect-save"] ?? "";

        if($redirectSave!=""){
            $isSaved=true;
        }

        $dataForm=[];
        $dataForm["redirect"]=false;
        $dataForm["url_key"]=$url_key;
        $dataForm["author"] = $_POST["author"] ?? "";

        $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
        $proposal = $repositoryProposal->findOneBy(['url_key' => $url_key]);

        //si une proposition trouvée
        if(!is_null($proposal)){
            //si données envoyéé en post
            if(count($_POST)) {
                $isSaved=true;

                //TODO : vérification d'erreurs possibles ?
                $error=0;

                $entityManager = $this->getDoctrine()->getManager();


                $dataForm["proposal_id"] = $proposal->getId();

                $participation= new Participation();
                $participation->setProposal($proposal);
                $participation->setAuthor($dataForm["author"]);
                $date_start=new \DateTime();
                $date_start->setTimestamp(strtotime("now"));
                $participation->setDateCreate($date_start);
                $entityManager->persist($participation);
                $entityManager->flush();

                foreach($proposal->getChoices() as $choice){
                    $vote_value=$_POST["choice_value_".$choice->getId()] ?? "vote_0";
                    $vote= new \App\Entity\Vote();
                    $vote->setProposal($proposal);
                    $vote->setParticipation($participation);
                    $vote->setChoice($choice);
                    $vote->setVoteValue($vote_value);
                    $entityManager->persist($vote);
                    $entityManager->flush();
                }

                $dataForm["redirect"]=true;

            }
        }



        $dataForm["proposal"]=$proposal;

        if($isSaved){
            return $this->render('vote/save.html.twig', $dataForm);
        }else{
            return $this->render('vote/form.html.twig', $dataForm);
        }


    }
}