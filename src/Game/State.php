<?php
declare(strict_types=1);

namespace Sagrada\Game;

use function DeepCopy\deep_copy;
use Sagrada\DiceBag;
use Sagrada\DieCollection;
use Sagrada\Game;
use Sagrada\Player\SagradaPlayer;

class State
{
    /** @var int */
    protected $currentRound;

    /** @var DiceBag */
    protected $diceBag;

    /** @var DieCollection */
    protected $draftPool;

    /** @var Game */
    protected $game;

    /** @var array */
    protected $remainingTurns;

    public function getCurrentRound(): int
    {
        return $this->currentRound;
    }

    protected function setCurrentRound(int $currentRound): void
    {
        if ($currentRound > Game::TOTAL_NUMBER_OF_ROUNDS) {
            throw new \Exception(sprintf('Max of %s rounds per game exceeded', Game::TOTAL_NUMBER_OF_ROUNDS));
        }
        $this->currentRound = $currentRound;
    }

    public function initializeFirstRound(): void
    {
        if ($this->currentRound > 0) {
            throw new \LogicException("Can't initialize first round; game is already in progress");
        }
        $this->setCurrentRound(1);
        $this->initializeRound();
    }

    protected function initializeRound(): void
    {
        if (!empty($this->remainingTurns)) {
            throw new \LogicException("Can't increment the round when there are player turns currently remaining");
        }

        $this->instantiateTurns();
        $this->refreshDraftPoolFromDiceBag();
    }

    public function nextTurn(): void
    {
        array_shift($this->remainingTurns);

        if (count($this->remainingTurns) === 0) {
            $this->setCurrentRound($this->getCurrentRound() + 1);
            $this->initializeRound();
        }
    }

    protected function instantiateTurns(): void
    {
        $this->remainingTurns = [];
        for ($i = 0; $i < Game::TURNS_PER_PLAYER_PER_ROUND; $i++) {
            foreach ($this->getGame()->getPlayers() as $player) {
                $this->remainingTurns[] = $player;
            }
        }
    }

    public function hasRoundsRemaining(): bool
    {
        return $this->getCurrentRound() < Game::TOTAL_NUMBER_OF_ROUNDS;
    }

    /**
     * @return DiceBag
     */
    public function getDiceBag(): DiceBag
    {
        return $this->diceBag;
    }

    /**
     * @param DiceBag $diceBag
     */
    public function setDiceBag(DiceBag $diceBag): void
    {
        $this->diceBag = $diceBag;
    }

    /**
     * @return DieCollection
     */
    public function getDraftPool(): DieCollection
    {
        return $this->draftPool;
    }

    /**
     * @param DieCollection $draftPool
     */
    public function setDraftPool(DieCollection $draftPool): void
    {
        $this->draftPool = $draftPool;
    }

    public function refreshDraftPoolFromDiceBag(): void
    {
        $numberOfDieToDraw = $this->getGame()->getDraftPoolSize();
        $draftPool = $this->getDiceBag()->drawDieCollection($numberOfDieToDraw);
        $this->setDraftPool($draftPool);
    }

    /**
     * @return Game
     */
    public function getGame(): Game
    {
        return $this->game;
    }

    /**
     * @param Game $game
     */
    public function setGame(Game $game): void
    {
        $this->game = $game;
    }

    public function getCurrentPlayer(): SagradaPlayer
    {
        return $this->remainingTurns[0];
    }

    public function deepCopy(): self
    {
        return deep_copy($this);
    }
}
