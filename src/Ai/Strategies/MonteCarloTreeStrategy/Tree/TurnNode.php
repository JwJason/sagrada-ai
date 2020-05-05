<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree;

use Sagrada\Game;
use Sagrada\Turn;

class TurnNode extends Node
{
    /** @var Turn */
    protected $turn;

    public function getLastKnownGameState(): Game\State
    {
        $parentNode = $this->getParent();
        while (!($parentNode instanceof GameStateNode)) {
            $parentNode = $parentNode->getParent();
            if ($parentNode === null) {
                throw new \RuntimeException('TurnNode has no parent GameStateNode');
            }
        }
        /** @var GameStateNode $parentNode */
        return $parentNode->getGameState();
    }

    public function getAllPrecedingTurns(): array
    {
        $turns = [];
        $parentNode = $this;
        while ($parentNode instanceof self) {
            $turns[] = $parentNode->getTurn();
            $parentNode = $parentNode->getParent();
        }
        return array_reverse($turns);
    }

    /**
     * @return Turn
     */
    public function getTurn(): Turn
    {
        return $this->turn;
    }

    /**
     * @param Turn $turn
     */
    public function setTurn(Turn $turn): void
    {
        $this->turn = $turn;
    }
}
