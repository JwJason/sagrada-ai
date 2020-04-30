<?php
declare(strict_types=1);

namespace Sagrada\Game;

use function DeepCopy\deep_copy;
use Sagrada\Board\Board;
use Sagrada\Dice\DiceBag;
use Sagrada\Dice\DiceDraftPool;
use Sagrada\DieSpace\DieSpace;
use Sagrada\Player\SagradaPlayer;
use Sagrada\Scoring\BoardScorer;
use Sagrada\Validators\DiePlacementValidator;
use Sagrada\DiePlacement;

/**
 * Class PlayerGameState
 * @package Sagrada\Game
 */
class PlayerGameState
{
    /**
     * @var Board
     */
    protected $board;
    /**
     * @var DiceBag
     */
    protected $diceBag;
    /**
     * @var DiceDraftPool
     */
    protected $draftPool;
    /**
     * @var SagradaPlayer
     */
    protected $player;
    /**
     * @var DiePlacementValidator
     */
    protected $placementValidator;
    /**
     * @var BoardScorer
     */
    protected $scorer;
    /**
     * @var int
     */
    protected $turnsRemaining;

    /**
     * PlayerGameState constructor.
     * @param Board $board
     * @param DiceBag $diceBag
     * @param DiceDraftPool $draftPool
     * @param SagradaPlayer $player
     * @param DiePlacementValidator $placementValidator
     */
    public function __construct(
        Board $board,
        DiceBag $diceBag,
        DiceDraftPool $draftPool,
        SagradaPlayer $player,
        DiePlacementValidator $placementValidator
    ) {
        $this->board = $board;
        $this->diceBag = $diceBag;
        $this->draftPool = $draftPool;
        $this->player = $player;
        $this->placementValidator = $placementValidator;

        $this->turnsRemaining = 30;
    }

    /**
     * @return PlayerGameState
     */
    public function deepCopy(): self
    {
        return deep_copy($this);
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return DiceBag
     */
    public function getDiceBag(): DiceBag
    {
        return $this->diceBag;
    }

    /**
     * @return int
     */
    public function getTurnsRemaining(): int
    {
        return $this->turnsRemaining;
    }

    /**
     * @return bool
     */
    public function hasTurnsRemaining(): bool
    {
        return $this->getTurnsRemaining() > 0;
    }

    /**
     * @return bool
     */
    public function hasAnyPossibleMovesRemaining(): bool
    {
        // XXX: This isn't, strictly speaking, correct. We should check the dice bag and open spots
        // to make sure there's a possible color left to play somewhere.
        return $this->getBoard()->getAllOpenSpaces()->getCount() > 0;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function decrementTurnsRemaining(): int
    {
        if ($this->hasTurnsRemaining() === false) {
            throw new \Exception('No turns remaining.');
        }
        return --$this->turnsRemaining;
    }

    /**
     * @return DiceDraftPool
     */
    public function getDraftPool(): DiceDraftPool
    {
        return $this->draftPool;
    }

    /**
     * @return SagradaPlayer
     */
    public function getPlayer(): SagradaPlayer
    {
        return $this->player;
    }

    /**
     * @return DiePlacementValidator
     */
    public function getPlacementValidator(): DiePlacementValidator
    {
        return $this->placementValidator;
    }
}