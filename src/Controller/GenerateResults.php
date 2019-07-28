<?php
/**
 * Project : jm
 * File : Clear.php
 */

namespace App\Controller;



use App\Entity\Proposal;
use App\Service\ParametersService;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;
use Symfony\Component\Filesystem\Filesystem;

class GenerateResults extends AbstractController
{

    public function run(ParametersService $params){
        set_time_limit(60*60);
        $filesystem = new Filesystem();
        $n=0;
        $dataTemplate=[];
        $dataTemplate["warning"]="";

        //set a flag before process
        $flagFile= __DIR__ . '/../../var/cached-result/wip.txt';

        if( !$filesystem->exists($flagFile)  ) {
            $filesystem->touch($flagFile);

            try {
                $repositoryProposal = $this->getDoctrine()->getRepository(Proposal::class);
                $date_before = new \DateTime();
                $date_before->setTimestamp(strtotime("now -1 day"));
                $date_after = new \DateTime();
                $date_after->setTimestamp(strtotime("now"));
                $proposals = $repositoryProposal->findByDateEndBetween($date_before,$date_after);
                $em = $this->getDoctrine()->getEntityManager();
                $voteController = new Vote();

                foreach ($proposals as $proposal) {
                    $result_cached = __DIR__ . '/../../var/cached-result/' . $proposal->getUrlKey();
                    if (!$filesystem->exists($result_cached)) {
                        $voteController->compilResult($proposal, $params, $this);
                        $n++;
                    }

                }
                $em->flush();
                $filesystem->remove($flagFile);
            } catch (Exception $e) {
                if ($filesystem->exists($flagFile)) {
                    $filesystem->remove($flagFile);
                }
            }
        }else{
            $dataTemplate["warning"]="Process already in progress... ";
        }
        $dataTemplate["nb"]=$n;

        return $this->render('generateResults.html.twig', $dataTemplate);
    }
}