<?php
declare(strict_types=1);

namespace Sagrada;

use Sagrada\Board\Board;
use Sagrada\DieSpace\DieSpace;
use Sagrada\Validators\DiePlacementValidator;

/**
 * Class DiePlacementManager
 * @package Sagrada
 */
class DiePlacementManager
{
    /**
     * @var DiePlacementValidator
     */
    protected $placementValidator;

    /**
     * DiePlacementManager constructor.
     * @param DiePlacementValidator $placementValidator
     */
    public function __construct(DiePlacementValidator $placementValidator)
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
            throw new IllegalBoardPlacementException("Invalid dice placement");
        }

        $dieSpace = new DieSpace();
        $dieSpace->setDie($diePlacement->getDie());

        $boardSpace = $board->getSpace($diePlacement->getCoordinates());
        $boardSpace->setDieSpace($dieSpace);

        $board->setSpace($boardSpace, $diePlacement->getCoordinates());
    }
}
