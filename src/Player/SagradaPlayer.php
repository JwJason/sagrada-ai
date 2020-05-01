<?php
declare(strict_types=1);

namespace Sagrada\Player;

use Sagrada\Board\Board;

class SagradaPlayer
{
    /** @var Board */
    protected $board;

    public function __construct(Board $board)
    {
        $this->board = $board;
    }
}

