<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Proposal", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $proposal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Choice", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $choice;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $vote_value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participation", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $participation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposal(): ?Proposal
    {
        return $this->proposal;
    }

    public function setProposal(?Proposal $proposal): self
    {
        $this->proposal = $proposal;

        return $this;
    }

    public function getChoice(): ?Choice
    {
        return $this->choice;
    }

    public function setChoice(?Choice $choice): self
    {
        $this->choice = $choice;

        return $this;
    }

    public function getVoteValue(): ?string
    {
        return $this->vote_value;
    }

    public function setVoteValue(string $vote_value): self
    {
        $this->vote_value = $vote_value;

        return $this;
    }

    public function getParticipation(): ?Participation
    {
        return $this->participation;
    }

    public function setParticipation(?Participation $participation): self
    {
        $this->participation = $participation;

        return $this;
    }
}
