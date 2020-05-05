<?php
declare(strict_types=1);

namespace Sagrada\Game;

use Sagrada\Board\Board;
use Sagrada\Game;
use Sagrada\Player\SagradaPlayer;
use Sagrada\Scoring\Scorers\FromSagradaScoreCardFactory;
use Sagrada\Turn;

/**
 * Class PlayerState
 * @package Sagrada\Game
 */
class PlayerState
{
    /** @var Board */
    protected $board;

    /** @var Game */
    protected $game;

    /** @var SagradaPlayer */
    protected $player;

    /** @var Turn\Collection */
    protected $turnHistory;

    public function __construct(
        SagradaPlayer $player,
        Board $board,
        Game $game
    ) {
        $this->board = $board;
        $this->player = $player;
        $this->game = $game;
        $this->turnHistory = new Turn\Collection();
    }

    public function hasAnyPossibleMovesRemaining(): bool
    {
        // XXX: This isn't, strictly speaking, correct. We should check the dice bag and open spots
        // to make sure there's a possible color left to play somewhere.
        return $this->getBoard()->getAllOpenSpaces()->getCount() > 0;
    }

    public function getBoard(): Board
    {
        return $this->board;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getPlayer(): SagradaPlayer
    {
        return $this->player;
    }

    public function getScore(): Score
    {
        $scorerFactory = new FromSagradaScoreCardFactory();
        $boardScorer = $scorerFactory->createFromScoreCardCollection($this->getGame()->getScoreCards(), $this->getBoard());
        return new Score($boardScorer->getScore());
    }

    public function getTurnHistory(): Turn\Collection
    {
        return $this->turnHistory;
    }
}
