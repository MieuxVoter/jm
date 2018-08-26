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

        date_default_timezone_set($this->container->getParameter('timezone'));

        $isSaved=false;

        $redirectSave=$_GET["redirect-save"] ?? "";

        if($redirectSave!=""){
            $isSaved=true;
        }

        $dataForm=[];
        $dataForm["redirect"]=false;
        $dataForm["url_key"]=$url_key;
        $dataForm["author"] = $_POST["author"] ?? "";


        $choices=$this->container->getParameter('choice_values');
        $dataForm["choices"]=$choices;
        $choiceValues=array_values($choices);


        $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
        $proposal = $repositoryProposal->findOneBy(['url_key' => $url_key]);

        //si une proposition trouvée
        if(!is_null($proposal)){

            //si la proposition est finie
            if($proposal->getDateEnd()->getTimestamp() < strtotime("now")){
                return $this->showResult($proposal);

            }else{
                //si proposition toujours en cours
                //ET si données envoyéé en post
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

                    //protection contre une tentative de changement d'ID seuls les choix en base sont parcouru
                    foreach($proposal->getChoices() as $choice){

                        //si valeur pas définie on prend le plus bas -> "a rejeter"
                        $vote_value=$_POST["choice_value_".$choice->getId()] ?? $choiceValues[0];

                        //protection contre la saisie d'une valeur "inconnue" : on prend le plus bas -> "a rejeter"
                        if(!in_array($vote_value,$choiceValues)){
                            $vote_value=$choiceValues[0];
                        }
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
            $dataForm["timezone"]=date_default_timezone_get();
            if($isSaved){
                return $this->render('vote/save.html.twig', $dataForm);
            }else{
                return $this->render('vote/form.html.twig', $dataForm);
            }

        }else{
            return $this->render('vote/form.html.twig', $dataForm);
        }


    }


    public function showResult($proposal){
        $dataTemplate=[];

        $dataTemplate["proposal"]=$proposal;

        return $this->render('vote/result.html.twig', $dataTemplate);
    }
}