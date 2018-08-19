<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Proposal;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StartAVote extends Controller
{
    public function form(){

        $isSaved=false;

        $dataForm=[];
        $dataForm["author"]="";
        $dataForm["presentation"]="";
        $dataForm["time_before_end"]="6 hours";

        $choices=[];

        $choice= new Choice();
        $choice->setLabel("");
        $choice->setSortValue(0);
        $choice->setIsDeleted(0);
        $choices[]=$choice;
        $choicesToSave=[];

        //validation css
        $dataForm["presentation_Invalid"]="";
        $dataForm["time_before_end_Invalid"]="";
        $dataForm["number_of_choices_Invalid"]="";

        //si données envoyéé en post
        if(count($_POST)){
            $error=0;

            $dataForm["author"]=$_POST["author"]??"";
            $dataForm["presentation"]=$_POST["presentation"]??"";
            $dataForm["time_before_end"]=$_POST["time_before_end"]??"6 hours";
            $dataForm["number_of_choices"]=$_POST["number_of_choices"]??0;

            //pas de presentation
            if($dataForm["presentation"]===""){
                $dataForm["presentation_Invalid"]="is-invalid";
                $error++;
            }

            //duree incorrect
            $time_end=strtotime("now +".$dataForm["time_before_end"]);
            if($time_end<strtotime("now") || $time_end>strtotime("now +7 month")){
                $dataForm["time_before_end_Invalid"]="is-invalid";
                $error++;
            }

            //nombre de choix  incorrects
            $nbEnabledChoices=0;
            for($i=1;$i<=$dataForm["number_of_choices"];$i++){
                $num=$i;
                if($i<10){
                    $num="0".$i;
                }
                $choiceDisable=$_POST["remove_choice_".$num]??1;

                $choice= new Choice();
                $choice->setLabel($_POST["choice_value_".$num]??"");
                $choice->setSortValue($i);
                $choice->setIsDeleted($_POST["remove_choice_".$num]??1);
                $choices[]=$choice;

                if(!$choiceDisable &&  $choice->getLabel()!=""){
                    $nbEnabledChoices++;
                    $choicesToSave[]=$choice;
                }
            }

            if($nbEnabledChoices<2){
                $error++;
                $dataForm["number_of_choices_Invalid"]="is-invalid";
            }

            //si aucune erreur
            if($error==0){
                $isSaved=true;

                $entityManager = $this->getDoctrine()->getManager();

                $proposal=new Proposal();
                $proposal->setAuthor($dataForm["author"]);
                $proposal->setPresentation($dataForm["presentation"]);
                $proposal->setNumberOfChoices($nbEnabledChoices);

                $date_start=new \DateTime();
                $date_start->setTimestamp(strtotime("now"));
                $proposal->setDateStart($date_start);

                $date_end=new \DateTime();
                $date_end->setTimestamp($time_end);
                $proposal->setDateEnd($date_end);

                $date_delete=new \DateTime();
                $date_delete->setTimestamp(strtotime("now +".$dataForm["time_before_end"] ."+30 days"));
                $proposal->setDateDelete($date_delete);

                $entityManager->persist($proposal);
                $entityManager->flush();

                $key=$proposal->getId().sha1($proposal->getId().strtotime("now").substr($this->container->getParameter('saltkey'),0,10));

                $proposal->setUrlKey($key);
                $entityManager->persist($proposal);
                $entityManager->flush();
                $dataForm["proposal"]=$proposal;

                //sauvegarde des choix
                foreach($choicesToSave as $choice ){
                    $choice->setProposal($proposal);
                    $entityManager->persist($choice);
                }
                $entityManager->flush();

            }

        }

        $dataForm["choices"]=$choices;
        $dataForm["number_of_choices"]=count($choices)-1;

        if($isSaved){
            return $this->render('start-a-vote/save.html.twig', $dataForm);
        }else{
            return $this->render('start-a-vote/form.html.twig', $dataForm);
        }

    }
}