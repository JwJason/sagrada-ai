<?php
declare(strict_types=1);

namespace Sagrada\Board\Iterators;

use Iterator;
use Sagrada\Board\Board;

/**
 * Class AbstractBoardIterator
 * @package Sagrada\Board\Iterators
 */
abstract class AbstractBoardIterator implements Iterator
{
    /**
     * @var Board
     */
    protected $board;

    /**
     * AbstractBoardIterator constructor.
     * @param Board $board
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
    }
}
