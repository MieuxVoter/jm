<?php
/**
 * Project : jm
 * File : StartAVote.php
 */

namespace App\Controller;



use App\Entity\Choice;
use App\Entity\Participation;
use App\Entity\Proposal;
use App\Service\ParametersService;

use oceanBigOne\MajorityJudgment\Ballot;
use oceanBigOne\MajorityJudgment\MeritProfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;
use Symfony\Component\Filesystem\Filesystem;

class Vote extends AbstractController
{
    public function form($url_key, ParametersService $params)
    {
        set_time_limit(60*5);
        date_default_timezone_set($params->get('timezone'));
        $error = 0;

        $isSaved = false;

        $redirectSave = $_GET["redirect-save"] ?? "";

        if ($redirectSave != "") {
            $isSaved = true;
        }

        $dataForm = [];
        $dataForm["facebook_api_id"] = $params->get('facebook_api_id');
        $dataForm["redirect"] = false;
        $dataForm["url_key"] = $url_key;
        $dataForm["author"] = $_POST["author"] ?? "";
        $dataForm["error"] = false;
        $dataForm["toastrmessage"] = null;
        $dataForm["author_Invalid"] = "";

        $choices = $params->get('choice_values');
        $dataForm["choices"] = $choices;
        $choiceValues = array_keys($choices);

        $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
        $proposal = $repositoryProposal->findOneBy(['url_key' => $url_key]);
        $isResultLink = false;
        if (is_null($proposal)) {
            $proposal = $repositoryProposal->findOneBy(['url_result_key' => $url_key]);
            if (!is_null($proposal)) {
                $isResultLink = true;
            }
        }

        $dataForm["selected_values"] = [];

        //si une proposition trouvée
        if (!is_null($proposal)) {


            //compte le nombre de participations
            //recupere les votes
            $repositoryVote = $this->getDoctrine()->getRepository(\App\Entity\Vote::class);
            $votes = $repositoryVote->findBy(['proposal' => $proposal->getId()]);


            $nChoices = 0;
            foreach ($proposal->getChoices() as $choice) {
                $nChoices++;
                $vote_value = null;
                if (count($_POST)) {
                    $vote_value = $_POST["choice_value_" . $choice->getId()] ?? null;
                    if (!in_array($vote_value, ["vote_0", "vote_1", "vote_2", "vote_3", "vote_4", "vote_5"])) {
                        $error++;
                    }
                }
                $dataForm["selected_values"]["choice_value_" . $choice->getId()] = $vote_value;
            }
            if ($error > 0) {
                $dataForm["toastrmessage"] = ["type" => "error", "title" => "Erreur", "text" => "Vous devez évaluer toutes les propositions !"];

            }
            $nbParticipations = count($votes) / $nChoices;


            //si la proposition est finie ou qu'on a un lien d'affichae temps reel
            if ($proposal->getDateEnd()->getTimestamp() < strtotime("now") || $isResultLink == true || ($proposal->getMaxParticipation() > 1 and $nbParticipations >= $proposal->getMaxParticipation())) {

                //si affichage temps réeel et que le nombre de participation ne permet pas un affichage en temps réel
                if ($isResultLink && $nbParticipations > 100) {
                    $dataTemplate = [];
                    $dataTemplate["proposal"] = $proposal;
                    $dataTemplate["facebook_api_id"] = $params->get('facebook_api_id');
                    return $this->render('vote/too-much-vote-for-preview.html.twig', $dataTemplate);
                } else {
                    return $this->showResult($proposal, $nbParticipations, $params);
                }


            } else {
                //si proposition toujours en cours
                //ET si données envoyéé en post
                if (count($_POST)) {


                    //TODO : vérification d'erreurs possibles ?
                    if ($proposal->getIsNameRequired() && trim($dataForm["author"]) === "") {
                        $error++;
                        $dataForm["author_Invalid"] = "is-invalid";
                    }


                    if ($error == 0) {

                        $isSaved = true;

                        $entityManager = $this->getDoctrine()->getManager();


                        $dataForm["proposal_id"] = $proposal->getId();

                        $participation = new Participation();
                        $participation->setProposal($proposal);
                        $participation->setAuthor(trim($dataForm["author"]));
                        $date_start = new \DateTime();
                        $date_start->setTimestamp(strtotime("now"));
                        $participation->setDateCreate($date_start);
                        $entityManager->persist($participation);
                        $entityManager->flush();

                        //protection contre une tentative de changement d'ID seuls les choix en base sont parcouru
                        foreach ($proposal->getChoices() as $choice) {

                            //si valeur pas définie on prend le plus bas -> "a rejeter"
                            $vote_value = $_POST["choice_value_" . $choice->getId()] ?? $choiceValues[0];

                            //protection contre la saisie d'une valeur "inconnue" : on prend le plus bas -> "a rejeter"
                            if (!in_array($vote_value, $choiceValues)) {
                                $vote_value = $choiceValues[0];
                            }
                            $vote = new \App\Entity\Vote();
                            $vote->setProposal($proposal);
                            $vote->setParticipation($participation);
                            $vote->setChoice($choice);
                            $vote->setVoteValue($vote_value);
                            $entityManager->persist($vote);
                            $entityManager->flush();

                            $filesystem = new Filesystem();
                            if ($filesystem->exists(__DIR__ . '/../../var/cached-result/' . $proposal->getUrlKey())){
                                $filesystem->remove(__DIR__ . '/../../var/cached-result/' . $proposal->getUrlKey());
                            }
                        }

                        $dataForm["redirect"] = true;
                    }

                }

            }

            $dataForm["proposal"] = $proposal;
            $dataForm["timezone"] = date_default_timezone_get();
            if ($isSaved) {
                return $this->render('vote/save.html.twig', $dataForm);
            } else {
                return $this->render('vote/form.html.twig', $dataForm);
            }

        } else {
            $dataForm["proposal"] = new Proposal();
            $dataForm["error"] = true;
            $dataForm["timezone"] = date_default_timezone_get();
            return $this->render('vote/form.html.twig', $dataForm);
        }


    }


    public function showResult($proposal, $nbParticipations, ParametersService $params)
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir([__DIR__ . '/../../var/cached-result/'], 0777);
        //si le fichier de cache exist et qu'il ne s'agit pas d'une prévisualisation du resultat
        if (
            ($filesystem->exists(__DIR__ . '/../../var/cached-result/' . $proposal->getUrlKey()))
            &&
            ($proposal->getDateEnd()->getTimestamp() < strtotime("now") || ($proposal->getMaxParticipation() > 1 and $nbParticipations >= $proposal->getMaxParticipation()))
        ) {
            $serialized_result = file_get_contents(__DIR__ . '/../../var/cached-result/' . $proposal->getUrlKey());
        } else {
            $serialized_result = $this->compilResult($proposal, $params);
        }
        $result = unserialize($serialized_result);
        if ($result["number_of_votes"] >= 2){
            return  $this->render('vote/result.html.twig', $result);
        }else{
            return $this->render('vote/not-enough-vote.html.twig', $result);
        }

    }




    public function compilResult($proposal, ParametersService $params,$container=null){

        if(is_null($container)){
            $container=$this;
        }

        $dataTemplate=[];
        $dataTemplate["proposal"]=$proposal;
        $dataTemplate["facebook_api_id"]=$params->get('facebook_api_id');
        $mentions=$params->get('choice_values');
        $dataTemplate["mentions"]=$mentions;
        $dataTemplate["mention_colors"]=$params->get('choice_colors');




        //recupere les choix/candidats possibles
        $candidates=$proposal->getChoices();

        //recupere les votes
        $repositoryVote = $container->getDoctrine()->getRepository(\App\Entity\Vote::class);
        $votes = $repositoryVote->findBy(['proposal' => $proposal->getId()]);

        //recupere les participants
        $dataTemplate["participations"]=[];
        $repositoryParticipations = $container->getDoctrine()->getRepository(\App\Entity\Participation::class);
        $dataTemplate["participations"] = $repositoryParticipations->findBy(['proposal' => $proposal->getId()]);
        shuffle($dataTemplate["participations"]);

        //scrutin
        $ballot = new Ballot();

        //ajoute les mentions
        $mentionToObject=[];
        $mentionLabelToValue=[];
        $dataTemplate["mentionLabelColors"]=[];
        foreach($mentions as $mention_value=>$mention_label){
            $mentionToObject[$mention_value]=new \oceanBigOne\MajorityJudgment\Mention($mention_label); //en mémorisant l'index pour le retrouver plus tard
            $mentionLabelToValue[$mention_label]=$mention_value;
            $ballot->addMention( $mentionToObject[$mention_value]);
            $dataTemplate["mentionLabelColors"][$mention_label]=$dataTemplate["mention_colors"][$mention_value];
        }




        //ajoutes les candidats/choix
        $candidateToObject=[];
        $candidateToId=[];
        foreach($candidates as $index=>$candidat) {
            $candidateToObject[$candidat->getLabel()] = new \oceanBigOne\MajorityJudgment\Candidate($candidat->getLabel());
            $candidateToId[$candidat->getLabel()]=$candidat->getId();
            $ballot->addCandidate($candidateToObject[$candidat->getLabel()]);
        }

        $dataTemplate["number_of_votes"]=count($votes);

        //si assez de votes
        if($dataTemplate["number_of_votes"]>=2) {

            //ajoutes les votes
            foreach ($votes as $vote) {
                try {
                    $ballot->addVote(new \oceanBigOne\MajorityJudgment\Vote($candidateToObject[$vote->getChoice()->getLabel()], $mentionToObject[$vote->getVoteValue()]));
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }

            //calcul le resultat
            $result = $ballot->proceedElection();


            //affiche les profiles de merite pour chaque candidat/choix
            $repositoryChoice = $container->getDoctrine()->getRepository(Choice::class);
            $dataTemplate["result"] = [];
            $dataTemplate["meritProfiles"] = [];
            foreach ($result as $candidate) {
                $meritProfile = new MeritProfile();
                $data = [
                    "candidate" => $repositoryChoice->findBy(['id' => $candidateToId[$candidate->getName()]])[0],
                    "meritProfile" => $meritProfile->getAsMeritArray($candidate, $ballot->getVotes(), $ballot->getMentions()),
                    "majorityMention" => $meritProfile->processMajorityMention($candidate, $ballot->getVotes(), $ballot->getMentions()),
                    "percentOfMajorityMention" => $meritProfile->processPercentOfMajorityMention($candidate, $ballot->getVotes(), $ballot->getMentions()),
                    "percentOfBetterThanMajorityMention" => $meritProfile->processPercentOfBetterThanMajorityMention($candidate, $ballot->getVotes(), $ballot->getMentions()),
                    "percentOfWorseThanMajorityMention" => $meritProfile->processPercentOfWorseThanMajorityMention($candidate, $ballot->getVotes(), $ballot->getMentions())
                ];
                $dataTemplate["result"][] = $data;
                $dataTemplate["meritProfiles"][$candidateToId[$candidate->getName()]] = [];
                foreach ($data["meritProfile"] as $merit) {
                    $dataTemplate["meritProfiles"][$candidateToId[$candidate->getName()]][$mentionLabelToValue[$merit->getMention()->getLabel()]] = $merit->getPercent();
                }
            }

            $dataTemplate["winner"] = $dataTemplate["result"][0]["candidate"];
            $dataTemplate["winnerMention"] = $dataTemplate["result"][0]["majorityMention"]->getLabel();
            $dataTemplate["winnerMentionColor"] = $dataTemplate["mention_colors"][$mentionLabelToValue[$dataTemplate["result"][0]["majorityMention"]->getLabel()]];
            $dataTemplate["mentionLabelToValue"] = $mentionLabelToValue;

            $filesystem = new Filesystem();
            $filesystem->mkdir([__DIR__.'/../../var/cached-result/'], 0777);
            $serialized_result=serialize($dataTemplate);
            file_put_contents(__DIR__.'/../../var/cached-result/'.$proposal->getUrlKey(),$serialized_result);

            //$out= $this->render('vote/result.html.twig', $dataTemplate);
        }else{
            //$out=  $this->render('vote/not-enough-vote.html.twig', $dataTemplate);
            $serialized_result=serialize($dataTemplate);
        }


        return $serialized_result;

    }
}