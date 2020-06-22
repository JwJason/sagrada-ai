<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\Game\State;
use Sagrada\Player\SagradaPlayer;
use Sagrada\ScoreCards\SagradaScoreCardCollection;

class Game
{
    public const TOTAL_NUMBER_OF_ROUNDS = 10;
    public const TURNS_PER_PLAYER_PER_ROUND = 2;

    /** @var int */
    protected $draftPoolSize;
    /** @var DiePlacement\Finder */
    protected $placementFinder;
    /** @var DiePlacement\BoardPlacer */
    protected $placementPlacer;
    /** @var DiePlacement\Validator */
    protected $placementValidator;
    /** @var array */
    protected $players;
    /** @var SagradaScoreCardCollection */
    protected $scoreCards;
    /** @var State */
    protected $state;

    /**
     * @return int
     */
    public function getDraftPoolSize(): int
    {
        return $this->draftPoolSize;
    }

    /**
     * @param int $draftPoolSize
     */
    public function setDraftPoolSize(int $draftPoolSize): void
    {
        $this->draftPoolSize = $draftPoolSize;
    }

    /**
     * @return DiePlacement\Finder
     */
    public function getPlacementFinder(): DiePlacement\Finder
    {
        return $this->placementFinder;
    }

    /**
     * @param DiePlacement\Finder $placementFinder
     */
    public function setPlacementFinder(DiePlacement\Finder $placementFinder): void
    {
        $this->placementFinder = $placementFinder;
    }

    /**
     * @return DiePlacement\BoardPlacer
     */
    public function getPlacementPlacer(): DiePlacement\BoardPlacer
    {
        return $this->placementPlacer;
    }

    /**
     * @param DiePlacement\BoardPlacer $placementPlacer
     */
    public function setPlacementPlacer(DiePlacement\BoardPlacer $placementPlacer): void
    {
        $this->placementPlacer = $placementPlacer;
    }

    /**
     * @return DiePlacement\Validator
     */
    public function getPlacementValidator(): DiePlacement\Validator
    {
        return $this->placementValidator;
    }

    /**
     * @param DiePlacement\Validator $placementValidator
     */
    public function setPlacementValidator(DiePlacement\Validator $placementValidator): void
    {
        $this->placementValidator = $placementValidator;
    }

    public function getPlayerIndex(SagradaPlayer $player): int
    {
        foreach ($this->players as $index => $comparisonPlayer) {
            if ($player === $comparisonPlayer) {
                return $index;
            }
        }
        throw new \RuntimeException('Player not found in game players collection');
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param int $playerIndex
     * @return SagradaPlayer
     */
    public function getPlayer(int $playerIndex): SagradaPlayer
    {
        $players = $this->getPlayers();
        if (empty($players[$playerIndex])) {
            throw new \OutOfRangeException(sprintf('Invalid player index: %d', $playerIndex));
        }
        return $players[$playerIndex];
    }

    /**
     * @param $players
     */
    public function setPlayers(array $players): void
    {
        $this->players = $players;
    }

    /**
     * @return SagradaScoreCardCollection
     */
    public function getScoreCards(): SagradaScoreCardCollection
    {
        return $this->scoreCards;
    }

    /**
     * @param SagradaScoreCardCollection $scoreCards
     */
    public function setScoreCards(SagradaScoreCardCollection $scoreCards): void
    {
        $this->scoreCards = $scoreCards;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState(State $state): void
    {
        $this->state = $state;
    }
}
