<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProposalRepository")
 */
class Proposal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $presentation;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_of_choices;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_end;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_delete;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $participant_counter;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Choice", mappedBy="proposal", orphanRemoval=true)
     */
    private $choices;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_key;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="proposal", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="proposal", orphanRemoval=true)
     */
    private $participations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_result_key;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_participation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_facebook_enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_name_required;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFakeVote;

    public function __construct()
    {
        $this->choices = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getNumberOfChoices(): ?int
    {
        return $this->number_of_choices;
    }

    public function setNumberOfChoices(int $number_of_choices): self
    {
        $this->number_of_choices = $number_of_choices;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(?\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(?\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

        return $this;
    }

    public function getDateDelete(): ?\DateTimeInterface
    {
        return $this->date_delete;
    }

    public function setDateDelete(?\DateTimeInterface $date_delete): self
    {
        $this->date_delete = $date_delete;

        return $this;
    }

    public function getParticipantCounter(): ?int
    {
        return $this->participant_counter;
    }

    public function setParticipantCounter(?int $participant_counter): self
    {
        $this->participant_counter = $participant_counter;

        return $this;
    }

    /**
     * @return Collection|Choice[]
     */
    public function getChoices(): Collection
    {
        return $this->choices;
    }

    /**
     * @return Choice[]
     */
    public function getShuffleChoices(): array
    {
            $array=$this->choices->toArray();
            shuffle($array);
            return $array;


    }

    public function addChoice(Choice $choice): self
    {
        if (!$this->choices->contains($choice)) {
            $this->choices[] = $choice;
            $choice->setProposal($this);
        }

        return $this;
    }

    public function removeChoice(Choice $choice): self
    {
        if ($this->choices->contains($choice)) {
            $this->choices->removeElement($choice);
            // set the owning side to null (unless already changed)
            if ($choice->getProposal() === $this) {
                $choice->setProposal(null);
            }
        }

        return $this;
    }

    public function getUrlKey(): ?string
    {
        return $this->url_key;
    }

    public function setUrlKey(?string $url_key): self
    {
        $this->url_key = $url_key;

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setProposal($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getProposal() === $this) {
                $vote->setProposal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setProposal($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->contains($participation)) {
            $this->participations->removeElement($participation);
            // set the owning side to null (unless already changed)
            if ($participation->getProposal() === $this) {
                $participation->setProposal(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getUrlResultKey(): ?string
    {
        return $this->url_result_key;
    }

    public function setUrlResultKey(?string $url_result_key): self
    {
        $this->url_result_key = $url_result_key;

        return $this;
    }

    public function getMaxParticipation(): ?int
    {
        return $this->max_participation;
    }

    public function setMaxParticipation(?int $max_participation): self
    {
        $this->max_participation = $max_participation;

        return $this;
    }

    public function getIsFacebookEnabled(): ?bool
    {
        return $this->is_facebook_enabled;
    }

    public function setIsFacebookEnabled(bool $is_facebook_enabled): self
    {
        $this->is_facebook_enabled = $is_facebook_enabled;

        return $this;
    }

    public function getIsNameRequired(): ?bool
    {
        return $this->is_name_required;
    }

    public function setIsNameRequired(?bool $is_name_required): self
    {
        $this->is_name_required = $is_name_required;

        return $this;
    }

    public function getIsFakeVote(): ?bool
    {
        return $this->isFakeVote;
    }

    public function setIsFakeVote(bool $isFakeVote): self
    {
        $this->isFakeVote = $isFakeVote;

        return $this;
    }
}
