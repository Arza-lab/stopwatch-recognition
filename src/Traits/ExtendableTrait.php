<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ExtendableTrait
{
    #[ORM\Column(name: 'extension', type: 'json', nullable: true)]
    protected ?array $extension = null;

    public function getExtension(): ?array
    {
        return $this->extension;
    }

    public function setExtension(?array $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getExtensionValue(string $key)
    {
        return $this->extension[$key] ?? null;
    }

    public function setExtensionValue(string $key, $value): self
    {
        $this->extension[$key] = $value;

        return $this;
    }
}
