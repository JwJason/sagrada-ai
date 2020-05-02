<?php
declare(strict_types=1);

namespace Sagrada\Game;

/**
 * Class Score
 * @package Sagrada\Game
 */
class Score
{
    /**
     * @var int
     */
    protected $total;

    /**
     * Score constructor.
     * @param int $total
     */
    public function __construct(int $total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    public function __toString()
    {
        return (string)$this->getTotal();
    }
}
