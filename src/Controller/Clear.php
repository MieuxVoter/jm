<?php
/**
 * Project : jm
 * File : Clear.php
 */

namespace App\Controller;



use App\Entity\Proposal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;

class Clear extends AbstractController
{

    public function run(){
        $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
        $date_delete=new \DateTime();
        $date_delete->setTimestamp(strtotime("now"));
        $proposals = $repositoryProposal->findByDateDeleteBefore($date_delete);

        $em = $this->getDoctrine()->getEntityManager();
        $n=0;
        foreach($proposals as $proposal){
            $em->remove($proposal);
            $n++;
        }
        $em->flush();
        $dataTemplate["nb"]=$n;

        return $this->render('clear.html.twig', $dataTemplate);
    }
}