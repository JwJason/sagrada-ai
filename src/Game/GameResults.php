<?php
declare(strict_types=1);

namespace Sagrada\Game;

use Sagrada\Board\Board;

/**
 * Class GameResults
 * @package Sagrada\Game
 */
class GameResults
{
    /**
     * @var Board
     */
    protected $board;
    /**
     * @var Score
     */
    protected $score;

    /**
     * GameResults constructor.
     * @param Board $board
     * @param Score $score
     */
    public function __construct(Board $board, Score $score)
    {
        $this->board = $board;
        $this->score = $score;
    }

    /**
     * @return Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * @return Score
     */
    public function getScore(): Score
    {
        return $this->score;
    }
}
