<?php
declare(strict_types=1);

namespace Sagrada\Player;

use Sagrada\Game\PlayerState;

class SagradaPlayer
{
    /** @var string */
    protected $name;

    /** @var PlayerState */
    protected $state;

    /**
     * @return PlayerState
     */
    public function getState(): PlayerState
    {
        return $this->state;
    }

    /**
     * @param PlayerState $state
     */
    public function setState(PlayerState $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return sprintf(
            "Player: %s\n%s",
            $this->getName(),
            (string)$this->getState()
        );
    }
}

