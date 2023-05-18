<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JsonSerializable;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[Vich\Uploadable]
class Project implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $user_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[Vich\UploadableField(mapping: 'stl', fileNameProperty: 'stlName', size: 'stlSize')]
    private ?File $stl_file = null;
    
    #[ORM\Column(nullable: true)]
    private ?string $stlName = null;

    #[ORM\Column(nullable: true)]
    private ?int $stlSize = null;

    #[Vich\UploadableField(mapping: 'gcode', fileNameProperty: 'gcodeName', size: 'gcodeSize')]
    private ?File $gcode_file = null;
    
    #[ORM\Column(nullable: true)]
    private ?string $gcodeName = null;

    #[ORM\Column(nullable: true)]
    private ?int $gcodeSize = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_submitted = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_updated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stl_uri = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gcode_uri = null;

    public function setStlFile(?File $stlFile = null): void {
        $this->stl_file = $stlFile;
        
        if (null!== $stlFile) {
            $this->date_updated = new \DateTimeImmutable();
        }
    }
    
    public function getStlFile(): ?File {
        return $this->stl_file;
    }
    
    public function setStlName(?string $name): void {
        $this->stlName = $name;
    }
    
    public function getStlName(): ?String {
        return $this->stlName;
    }
    
    public function setStlSize(?string $size): void {
        $this->stlSize = $size;
    }
    
    public function getStlSize(): ?string {
        return $this->stlSize;
    }
    
    public function setGcodeFile(?File $gcodeFile = null): void {
        $this->gcode_file = $gcodeFile;
        
         if (null!== $gcodeFile) {
            $this->date_updated = new \DateTimeImmutable();
        }
    }
    
    public function getGcodeFile(): ?File {
        return $this->gcode_file;
    }
    
    public function setGcodeName(?string $name): void {
        $this->gcodeName = $name;
    }
    
    public function getGcodeName(): ?String {
        return $this->gcodeName;
    }
    
    public function setGcodeSize(?string $size): void {
        $this->gcodeSize = $size;
    }
    
    public function getGcodeSize(): ?string {
        return $this->gcodeSize;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateSubmitted(): ?\DateTimeInterface
    {
        return $this->date_submitted;
    }

    public function setDateSubmitted(\DateTimeInterface $date_submitted): self
    {
        $this->date_submitted = $date_submitted;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->date_updated;
    }

    public function setDateUpdated(\DateTimeInterface $date_updated): self
    {
        $this->date_updated = $date_updated;

        return $this;
    }
    
    public function JsonSerialize(): mixed {
        return array( 
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'stl_uri' => $this->stl_uri,
            'gcode_uri' => $this->gcode_uri,
            'date_submitted' => date('r', $this->date_submitted->getTimeStamp()),
            'date_modified' => date('r', $this->date_updated->getTimeStamp())
        );
    }

    public function getStlUri(): ?string
    {
        return $this->stl_uri;
    }

    public function setStlUri(?string $stl_uri): self
    {
        $this->stl_uri = $stl_uri;

        return $this;
    }

    public function getGcodeUri(): ?string
    {
        return $this->gcode_uri;
    }

    public function setGcodeUri(?string $gcode_uri): self
    {
        $this->gcode_uri = $gcode_uri;

        return $this;
    }
}
