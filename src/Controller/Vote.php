<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Participation;
use App\Entity\Proposal;

use oceanBigOne\MajorityJudgment\Ballot;
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
        $choiceValues=array_keys($choices);


        $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
        $proposal = $repositoryProposal->findOneBy(['url_key' => $url_key]);

        //si une proposition trouvée
        if(!is_null($proposal)){

            //si la proposition est finie
            if($proposal->getDateEnd()->getTimestamp() < strtotime("now") || isset($_GET["show-result"])){
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
        $mentions=$this->container->getParameter('choice_values');
        $dataTemplate["mentions"]=$mentions;
        $dataTemplate["mention_colors"]=$this->container->getParameter('choice_colors');


        //recupere les choix/candidats possibles
        $candidates=$proposal->getChoices();

        //recupere les votes
        $repositoryVote = $this->getDoctrine()->getRepository(\App\Entity\Vote::class);
        $votes = $repositoryVote->findBy(['proposal' => $proposal->getId()]);


        //scrutin
        $ballot = new Ballot();

        //ajoute les mentions
        $mentionToIndex=[];
        $indexToMention=[];
        $index=0;
        foreach($mentions as $mention_value=>$mention_label){
            $ballot->addMention($mention_value);
            $mentionToIndex[$mention_value]=$index; //en mémorisant l'index pour le retrouver plus tard
            $indexToMention[$index]=$mention_value;
            $index++;
        }

        //ajoutes les candidats/choix
        $candidatesToIndex=[];
        foreach($candidates as $index=>$candidat) {
            $ballot->addCandidate($candidat->getLabel());
            $candidatesToIndex[$candidat->getLabel()]=$index; //en mémorisant l'index pour le retrouver plus tard
        }

        //ajoutes les votes
        foreach($votes as $vote){
            $ballot->addVote( $candidatesToIndex[$vote->getChoice()->getLabel()], $mentionToIndex[$vote->getVoteValue()] );
        }

        //calcul le resultat
        $result=Ballot::getResult($ballot);
        $resultIndex=array_keys($result);

        //affiche les profiles de merite pour chaque candidat/choix
        $dataTemplate["meritProfiles"]=[];
        foreach($ballot->getCandidates() as $index_of_candidate=>$candidate){

            $offsetCandidat=$candidates[$index_of_candidate]->getId();
            $dataTemplate["meritProfiles"][$offsetCandidat]=[];
            $index_of_mention=0;
            foreach($mentions as $mention_value=>$mention_label){
                $meritProfilValue=$result[$index_of_candidate]["values"]["merit-profile"][$mentionToIndex[$mention_value]];
                $dataTemplate["meritProfiles"][$offsetCandidat][$mention_value]=$meritProfilValue;
                $index_of_mention++;
            }
        }




        //recupere le gagnant
        $repositoryChoice = $this->getDoctrine()->getRepository(Choice::class);
        $winner = $repositoryChoice->findBy(['id' => $candidates[$resultIndex[0]]]);
        $dataTemplate["winner"]=$winner[0];

        $dataTemplate["result"]=$result;







        /*$judgement = new MajorityJudgment();
        $judgement->setChoices($choices);
        $judgement->setValues($choiceValues);

        foreach($votes as $vote){
            $judgement->addVote($vote->getChoice()->getId(),$vote->getVoteValue());
        }
        $dataTemplate["meritProfiles"]=$judgement->meritProfiles();

        $repositoryChoice = $this->getDoctrine()->getRepository(\App\Entity\Choice::class);
        $dataTemplate["winner"]= $repositoryChoice ->findOneBy(["id"=>$judgement->winner()]);*/






        return $this->render('vote/result.html.twig', $dataTemplate);
    }
}