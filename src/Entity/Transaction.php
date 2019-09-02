<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank()
     * @Assert\Length(min="10",minMessage="ce champ doit comporter au moins {{limit}}caractere", max="40",maxMessage="ce champ doit comporter au plus {{limit}}caractere")
     */
    private $nomEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min="10",minMessage="ce champ doit comporter au moins {{limit}}caractere", max="40",maxMessage="ce champ doit comporter au plus {{limit}}caractere")
     */
    private $nomReceveur;



    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="13",maxMessage="ce champ doit comporter au plus {{limit}}caractere")
     
     */
    private $cniEnvoyeur;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephoneEnvoyeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephoneReceveur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeEnvoi;

    /**
     * @ORM\Column(type="float")
     */
    private $montantEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tarif", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactions")
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commissionEtat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commisionEnvoi;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commissionRetrait;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commissionSystem;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEnvoyeur(): ?string
    {
        return $this->nomEnvoyeur;
    }

    public function setNomEnvoyeur(string $nomEnvoyeur): self
    {
        $this->nomEnvoyeur = $nomEnvoyeur;

        return $this;
    }

    public function getNomReceveur(): ?string
    {
        return $this->nomReceveur;
    }

    public function setNomReceveur(string $nomReceveur): self
    {
        $this->nomReceveur = $nomReceveur;

        return $this;
    }




    public function getCniEnvoyeur(): ?string
    {
        return $this->cniEnvoyeur;
    }

    public function setCniEnvoyeur(string $cniEnvoyeur): self
    {
        $this->cniEnvoyeur = $cniEnvoyeur;

        return $this;
    }




    public function getTelephoneEnvoyeur(): ?string
    {
        return $this->telephoneEnvoyeur;
    }

    public function setTelephoneEnvoyeur(string $telephoneEnvoyeur): self
    {
        $this->telephoneEnvoyeur = $telephoneEnvoyeur;

        return $this;
    }

    public function getTelephoneReceveur(): ?string
    {
        return $this->telephoneReceveur;
    }

    public function setTelephoneReceveur(string $telephoneReceveur): self
    {
        $this->telephoneReceveur = $telephoneReceveur;

        return $this;
    }

    public function getCodeEnvoi(): ?string
    {
        return $this->codeEnvoi;
    }

    public function setCodeEnvoi(string $codeEnvoi): self
    {
        $this->codeEnvoi = $codeEnvoi;

        return $this;
    }

    public function getMontantEnvoi(): ?float
    {
        return $this->montantEnvoi;
    }

    public function setMontantEnvoi(float $montantEnvoi): self
    {
        $this->montantEnvoi = $montantEnvoi;

        return $this;
    }


    public function getPrix(): ?Tarif
    {
        return $this->prix;
    }

    public function setPrix(Tarif $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCommissionEtat(): ?float
    {
        return $this->commissionEtat;
    }

    public function setCommissionEtat(?float $commissionEtat): self
    {
        $this->commissionEtat = $commissionEtat;

        return $this;
    }

    public function getCommisionEnvoi(): ?float
    {
        return $this->commisionEnvoi;
    }

    public function setCommisionEnvoi(?float $commisionEnvoi): self
    {
        $this->commisionEnvoi = $commisionEnvoi;

        return $this;
    }

    public function getCommissionRetrait(): ?float
    {
        return $this->commissionRetrait;
    }

    public function setCommissionRetrait(?float $commissionRetrait): self
    {
        $this->commissionRetrait = $commissionRetrait;

        return $this;
    }

    public function getCommissionSystem(): ?float
    {
        return $this->commissionSystem;
    }

    public function setCommissionSystem(?float $commissionSystem): self
    {
        $this->commissionSystem = $commissionSystem;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
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
}
