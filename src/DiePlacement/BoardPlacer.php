<?php
declare(strict_types=1);

namespace Sagrada\DiePlacement;

use Sagrada\Board\Board;
use Sagrada\DiePlacement;
use Sagrada\DieSpace\DieSpace;

/**
 * Class BoardPlacer
 * @package Sagrada
 */
class BoardPlacer
{
    /**
     * @var Validator
     */
    protected $placementValidator;

    /**
     * BoardPlacer constructor.
     * @param Validator $placementValidator
     */
    public function __construct(Validator $placementValidator)
    {
        $this->placementValidator = $placementValidator;
    }

    /**
     * @param DiePlacement $diePlacement
     * @param Board $board
     * @return bool
     * @throws \Exception
     */
    public function canPutDiePlacementOnBoard(DiePlacement $diePlacement, Board $board): bool
    {
        $placementValidator = $this->placementValidator;
        return $placementValidator->isValidDiePlacement($diePlacement, $board);
    }

    /**
     * @param DiePlacement $diePlacement
     * @param Board $board
     * @throws \Exception
     * @throws IllegalBoardPlacementException
     */
    public function putDiePlacementOnBoard(DiePlacement $diePlacement, Board $board): void
    {
        if ($this->canPutDiePlacementOnBoard($diePlacement, $board) === false) {
            throw new IllegalBoardPlacementException(sprintf('Invalid die placement: %s', (string)$diePlacement));
        }

        $dieSpace = new DieSpace();
        $dieSpace->setDie($diePlacement->getDie());

        $boardSpace = $board->getSpace($diePlacement->getCoordinates());
        $boardSpace->setDieSpace($dieSpace);

        $board->setSpace($boardSpace, $diePlacement->getCoordinates());
    }
}
