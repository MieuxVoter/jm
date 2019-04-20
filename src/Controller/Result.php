<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Proposal;
use App\Service\ParametersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;


class Result extends AbstractController
{
    public function form(ParametersService $params){

        date_default_timezone_set($params->get('timezone'));

        $isSaved=false;

        $redirectSave=$_GET["redirect-save"] ?? "";

        if($redirectSave!=""){
            $isSaved=true;
        }
        $proposal=null;
        $dataForm=[];
        $dataForm["facebook_api_id"]=$params->get('facebook_api_id');
        $dataForm["redirect"]=false;
        $dataForm["author"]="";
        $dataForm["title"]="";
        $dataForm["presentation"]="";
        $dataForm["time_before_end"]="6 hours";
        $dataForm["visibleBeforeEnd"]="no";
        $dataForm["limitParticipation"]="no";
        $dataForm["sendMail"]="no";
        $dataForm["facebookEnabled"]="no";
        $dataForm["nameRequired"]="no";
        $dataForm["mailValue"]="";
        $dataForm["limitParticipationValue"]="10";
        $dataForm["collapseOptions"]="yes";


        $choices=[];

        $choice= new Choice();
        $choice->setLabel("");
        $choice->setSortValue(0);
        $choice->setIsDeleted(0);
        $choices[]=$choice;
        $choicesToSave=[];

        //validation css
        $dataForm["title_Invalid"]="";
        $dataForm["presentation_Invalid"]="";
        $dataForm["time_before_end_Invalid"]="";
        $dataForm["number_of_choices_Invalid"]="";
        $dataForm["mailValue_Invalid"]="";
        $dataForm["limitParticipationValue_Invalid"]="";
        $dataForm["number_of_fake_votes_Invalid"]="";
        $dataForm["number_of_fake_votes_is_too_big"]="";

        //ajoute les mentions
        $dataForm["mentions"]=$params->get('choice_values');



        if(count($_POST)==1){
            //pas de titre
            if(isset($_POST["title"])){
                $dataForm["title"]=$_POST["title"];

            }
        }
        //si données envoyéé en post
        if(count($_POST)>1){
            $error=0;

            $dataForm["author"]=$_POST["author"]??"";
            $dataForm["presentation"]=$_POST["presentation"]??"";
            $dataForm["title"]=$_POST["title"]??"";
            $dataForm["time_before_end"]=$_POST["time_before_end"]??"6 hours";
            $dataForm["number_of_choices"]=$_POST["number_of_choices"]??0;

            $dataForm["visibleBeforeEnd"]=$_POST["visibleBeforeEnd"]??"no";
            $dataForm["limitParticipation"]=$_POST["limitParticipation"]??"no";
            $dataForm["sendMail"]=$_POST["sendMail"]??"no";
            $dataForm["facebookEnabled"]=$_POST["facebookEnabled"]??"no";
            $dataForm["nameRequired"]=$_POST["nameRequired"]??"no";
            $dataForm["mailValue"]=$_POST["mailValue"]??"";
            $dataForm["limitParticipationValue"]=$_POST["limitParticipationValue"]??10;

            $options=[$dataForm["visibleBeforeEnd"],$dataForm["limitParticipation"],$dataForm["sendMail"], $dataForm["facebookEnabled"],   $dataForm["nameRequired"]];
            $dataForm["collapseOptions"]="yes";
            if(in_array("yes",$options)){
                $dataForm["collapseOptions"]="no";
            }

            $dataForm["limitParticipationValue_Invalid"]="";
            if($dataForm["limitParticipation"]=="yes" && ( intval($dataForm["limitParticipationValue"])<2 || intval($dataForm["limitParticipationValue"])>=100 ) ){
                $dataForm["limitParticipationValue_Invalid"]="is-invalid";
                $error++;
            }

            $dataForm["mailValue_Invalid"]="";
            if($dataForm["sendMail"]=="yes" && !filter_var($dataForm["mailValue"], FILTER_VALIDATE_EMAIL)){
                $dataForm["mailValue_Invalid"]="is-invalid";
                $error++;
            }



            //pas de titre
            if($dataForm["title"]===""){
                $dataForm["title_Invalid"]="is-invalid";
                $error++;
            }

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
            $nbVotesPerChoice=[];

            for($i=1;$i<=$dataForm["number_of_choices"];$i++){
                $nbVote=0;
                $num=$i;
                if($i<10){
                    $num="0".$i;
                }
                $choiceDisable=$_POST["remove_choice_".$num]??1;

                $choice= new Choice();
                $choice->setLabel($_POST["choice_value_".$num]??"");
                $choice->setExplanation($_POST["choice_explanation_".$num]??"");
                $choice->setSortValue($i);
                $choice->setIsDeleted($_POST["remove_choice_".$num]??1);
                $choices[]=$choice;
                if(!$choiceDisable &&  $choice->getLabel()!=""){
                    $nbEnabledChoices++;
                    $choicesToSave[]=$choice;
                }
                $dataFakeVotes=[];
                foreach($dataForm["mentions"] as $mention_value=>$mention_label){
                    $dataFakeVotes[$mention_value]=$_POST["fakevote_".$mention_value."_".$num]??0;
                    $nbVote += $dataFakeVotes[$mention_value];
                }
                if(!$choice->getIsDeleted()){
                    $nbVotesPerChoice[$nbVote]=1;
                }

                $choice->setFakeVotes($dataFakeVotes);
            }

            if(count($nbVotesPerChoice)>1){
                $error++;
                $dataForm["number_of_fake_votes_Invalid"]="is-invalid";
            }

            if(count($nbVotesPerChoice)==1){
                $nbVotes=array_keys($nbVotesPerChoice);
                if($nbVotes[0]>100){
                    $error++;
                    $dataForm["number_of_fake_votes_is_too_big"]="is-invalid";
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
                $proposal->setTitle($dataForm["title"]);
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

                //Options
                if( $dataForm["limitParticipation"]=="yes"){
                    $proposal->setMaxParticipation($dataForm["limitParticipationValue"]);
                }

                if( $dataForm["facebookEnabled"]=="yes"){
                    $proposal->setIsFacebookEnabled(true);
                }else{
                    $proposal->setIsFacebookEnabled(false);
                }

                if( $dataForm["nameRequired"]== "yes"){
                    $proposal->setIsNameRequired(true);
                }else{
                    $proposal->setIsNameRequired(false);
                }


                $entityManager->persist($proposal);
                $entityManager->flush();

                $key=$proposal->getId().sha1($proposal->getId().strtotime("now").substr($params->get('saltkey'),0,10));
                $keyResult=$proposal->getId().sha1("RESULT".$proposal->getId().strtotime("now").substr($params->get('saltkey'),0,10));

                $proposal->setUrlKey($key);

                //URL RESULT
                if($dataForm["visibleBeforeEnd"]=="yes"){
                    $proposal->setUrlResultKey($keyResult);
                }





                $entityManager->persist($proposal);
                $entityManager->flush();
                $dataForm["proposal"]=$proposal;

                //sauvegarde des choix
                foreach($choicesToSave as $choice ){
                    $choice->setProposal($proposal);
                    $entityManager->persist($choice);
                }
                $entityManager->flush();


                //génération et sauvegardes des votes



                $dataForm["redirect"]=true;

                //envoi du mail
                if($dataForm["sendMail"]=="yes"){

                    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . '://'.$_SERVER["HTTP_HOST"];

                    $url=$this->generateUrl('app_jm_vote', array('url_key' => $proposal->getUrlKey()));
                    $text="Bonjour,\n\r\n\r";
                    $text.="Un nouveau vote vient d'etre lance sur ".$_SERVER['SERVER_NAME'].".\n\r\n\r";
                    $text.="Voici le lien vers le formulaire de vote (ou page de resultat une fois le vote termine) :\n\r".$link.$url;
                    if($proposal->getUrlResultKey()){
                        $urlResult=$this->generateUrl('app_jm_vote', array('url_key' => $proposal->getUrlResultKey()));
                        $text.="\n\r\n\rVoici le lien vers la page de resultat des votes en temps reel :\n\r".$link.$urlResult;
                    }
                    $text.="\n\r\n\r ** Mail envoye automatiquement, merci de ne pas y repondre **";

                    $to      = $dataForm["mailValue"];
                    $subject = "Lancement d'un nouveau vote";
                    $message = $text;
                    $headers = 'From: Jugement Majoritaire<noreply@'.$_SERVER['SERVER_NAME'] . ">\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    //TODO MISE EN FORME DU MAIL AVEC TWIG (phpmailer ?)
                    mail($to, $subject, $message, $headers);

                }

            }

        }

        $dataForm["choices"]=$choices;
        $dataForm["number_of_choices"]=count($choices)-1;

        if($isSaved){
            if(is_null($proposal)){
                $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
                $proposal = $repositoryProposal->findOneBy(['url_key' => $_GET["key"]]);
            }
            $dataForm["proposal"]=$proposal;

            return $this->render('result/save.html.twig', $dataForm);
        }else{
            return $this->render('result/form.html.twig', $dataForm);
        }

    }
}