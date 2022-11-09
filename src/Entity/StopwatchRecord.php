<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\StopwatchRecordRepository;
use App\Traits\TimestampAbleTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StopwatchRecordRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class StopwatchRecord
{
    use TimestampAbleTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $time = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): self
    {
        $this->time = $time;

        return $this;
    }
}
