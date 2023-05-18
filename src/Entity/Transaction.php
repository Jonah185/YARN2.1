<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $account_id = null;

    #[ORM\Column(length: 255)]
    private ?string $authorized_admin = null;

    #[ORM\Column(nullable: true)]
    private ?int $credits_issued = null;

    #[ORM\Column(nullable: true)]
    private ?int $debits = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $timestamp = null;

    #[ORM\Column(nullable: true)]
    private ?int $associated_project = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column]
    private ?int $balance_prior = null;

    #[ORM\Column]
    private ?int $balance_after = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAccountId(): ?string
    {
        return $this->account_id;
    }

    public function setAccountId(string $account_id): self
    {
        $this->account_id = $account_id;

        return $this;
    }

    public function getAuthorizedAdmin(): ?string
    {
        return $this->authorized_admin;
    }

    public function setAuthorizedAdmin(string $authorized_admin): self
    {
        $this->authorized_admin = $authorized_admin;

        return $this;
    }

    public function getCreditsIssued(): ?int
    {
        return $this->credits_issued;
    }

    public function setCreditsIssued(?int $credits_issued): self
    {
        $this->credits_issued = $credits_issued;

        return $this;
    }

    public function getDebits(): ?int
    {
        return $this->debits;
    }

    public function setDebits(?int $debits): self
    {
        $this->debits = $debits;

        return $this;
    }

    public function getAssociatedProject(): ?string
    {
        return $this->associated_project;
    }

    public function setAssociatedProject(?string $associated_project): self
    {
        $this->associated_project = $associated_project;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getBalancePrior(): ?int
    {
        return $this->balance_prior;
    }

    public function setBalancePrior(int $balance_prior): self
    {
        $this->balance_prior = $balance_prior;

        return $this;
    }

    public function getBalanceAfter(): ?int
    {
        return $this->balance_after;
    }

    public function setBalanceAfter(int $balance_after): self
    {
        $this->balance_after = $balance_after;

        return $this;
    }
}
