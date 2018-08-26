<?php
/**
 * Project : jm
 * File : MajorityJudgment.php
 */

namespace App\Utils;


class MajorityJudgment
{
    /**
     * @var array
     */
    protected $votes = [];

    /**
     * @var array
     */
    protected $choices = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * MajorityJudgment constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public function getVotes(): array
    {
        return $this->votes;
    }

    /**
     * @param array $votes
     * @return MajorityJudgment
     */
    public function setVotes(array $votes): MajorityJudgment
    {
        $this->votes = $votes;
        return $this;
    }


    /**
     * @param string $choice
     * @param string $value
     * @return MajorityJudgment
     */
    public function addVote(string $choice, string $value): MajorityJudgment
    {
        $this->votes[] = ["choice"=>$choice,"value"=>$value];
        return $this;
    }

    /**
     * @return array
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     * @return MajorityJudgment
     */
    public function setChoices(array $choices): MajorityJudgment
    {
        $this->choices = $choices;
        return $this;
    }

    /**
     * @param string $choice
     * @return MajorityJudgment
     */
    public function addChoice(string $choice): MajorityJudgment
    {
        $this->choices[] = $choice;
        return $this;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return MajorityJudgment
     */
    public function setValues(array $values): MajorityJudgment
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param string $value
     * @return MajorityJudgment
     */
    public function addValue(string $value): MajorityJudgment
    {
        $this->values[] = $value;
        return $this;
    }


    public function process(){

        //create an array for store result
        $result=[];

        //create an array for each choice
        foreach($this->choices as $choice){
            $result[$choice]=[];
            //init a stat for each value
            foreach($this->values as $value){
                $result[$choice][$value]=0;
            }
        }

        //sort vote
        foreach($this->votes as $vote){
            $result[$vote["choice"]][$vote["value"]]++;
        }


        foreach($this->choices as $choice){
           $nbParticipation=0;
           foreach($result[$choice] as $vote=>$quantity){
               $nbParticipation+=$quantity;
           }
        }

        if($nbParticipation>0){
            foreach($this->choices as $choice){
                foreach($result[$choice] as $vote=>$quantity){
                    $result[$choice][$vote] = $quantity * 100 / $nbParticipation;
                }
            }
        }





        return $result;
    }

    public function meritProfiles(){

        $result=$this->process();
        return $result;
    }

  public function winner(){
      $result=$this->process();

      //attribue une note (décroissante) aux mentions
      $notes=[];
      $mentionByChoice=[];
      $choiceByMentions=[];
      $noteByChoice=[];
      $mentionByChoiceQuantity=[];

      $note=count($this->values);
      foreach($this->values as $value){
          $notes[$value]=$note;
          $choiceByMentions[$value]=[];
          $note--;
      }
      //on attribue la mention pour chaque choix
      foreach($this->choices as $choice){
          $nbParticipation=0;
          foreach($result[$choice] as $vote=>$quantity){
              //echo "<br >".$choice." : +".$quantity." participation =".$nbParticipation;
              $nbParticipation+=$quantity;
              if($nbParticipation>=50){
                //  echo "<br >--plus de 50  : +".$nbParticipation;
                  $mentionByChoice[$choice]=$vote;
                  $choiceByMentions[$vote][]=$choice;
                  $noteByChoice[$choice]=$notes[$vote];
                  $mentionByChoiceQuantity[$choice]=$quantity;
                  //echo $vote;
                  break;
              }
          }
      }
      //on class les résultat
      asort($noteByChoice);
      reset($noteByChoice);
      $winnerChoice = key($noteByChoice);

      //TODO regarde si le vainqueur à une égalité

          //https://fr.wikipedia.org/wiki/Jugement_majoritaire
          //Critère de victoire: avoir le plus d'électeurs attribuant strictement plus que la mention majoritaire commune
          //Critère de défaite: avoir le plus d'électeurs attribuant strictement moins que la mention majoritaire commune





      return $winnerChoice;

  }


}