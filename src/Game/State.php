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
    /** @var bool */
    protected $gameIsCompleted;

    /** @var int */
    protected $currentRound;

    /** @var int */
    protected $currentTurn;

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

    public function setCurrentRound(int $currentRound): void
    {
        if ($currentRound > Game::TOTAL_NUMBER_OF_ROUNDS) {
            throw new \LogicException(sprintf('Max of %s rounds per game exceeded', Game::TOTAL_NUMBER_OF_ROUNDS));
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
        $this->gameIsCompleted = false;
    }

    protected function initializeRound(): void
    {
        if (!empty($this->remainingTurns)) {
            throw new \LogicException("Can't increment the round when there are player turns currently remaining");
        }

        $this->instantiateTurns();
        $this->refreshDraftPoolFromDiceBag();
        $this->currentTurn = 1;
    }

    public function nextTurn(): void
    {
        array_shift($this->remainingTurns);

        $this->currentTurn++;

        if (count($this->remainingTurns) === 0) {
            if ($this->hasRoundsRemaining() === true) {
                $this->setCurrentRound($this->getCurrentRound() + 1);
                $this->initializeRound();
            } else {
                $this->gameIsCompleted = true;
            }
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

    /**
     * @return int
     */
    public function getCurrentTurn(): int
    {
        return $this->currentTurn;
    }

    public function currentRoundHasTurnsRemaining(): bool
    {
        return count($this->remainingTurns) > 0;
    }

    public function deepCopy(): self
    {
        return deep_copy($this);
    }

    public function __toString()
    {
        $draftPoolString = '';
        $playerString = '';

        if ($this->gameIsCompleted() === true) {
            /** @var SagradaPlayer $player */
            foreach ($this->getGame()->getPlayers() as $player) {
                $playerString .= $player;
            }
            $gameStatusString = 'GAME COMPLETED';
        } else {
            $draftPoolString = sprintf(
                "Draft Pool\n" .
                "-----------\n" .
                "%s\n" .
                "-----------\n",
                (string)$this->getDraftPool()
            );
            $gameStatusString = sprintf(
                "> Round #%d\n" .
                "> Turn %d / %d\n" .
                "-----------\n",
                $this->getCurrentRound(),
                $this->getCurrentTurn(),
                count($this->getGame()->getPlayers()) * Game::TURNS_PER_PLAYER_PER_ROUND
            );
            $playerString = (string)$this->getCurrentPlayer();
        }

        return sprintf(
            "%s%s%s\n",
            $gameStatusString,
            $draftPoolString,
            $playerString
        );
    }

    /**
     * @return bool
     */
    public function gameIsCompleted(): bool
    {
        return $this->gameIsCompleted;
    }
}
