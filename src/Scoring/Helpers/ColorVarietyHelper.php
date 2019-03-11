<?php
declare(strict_types=1);

namespace Sagrada\Scoring\Helpers;

use Sagrada\Board\Space\BoardSpace;
use Sagrada\Board\Space\BoardSpaceCollection;

class ColorVarietyHelper
{
    /**
     * @param BoardSpaceCollection $spaces
     * @return bool
     */
    public function hasColorVariety(BoardSpaceCollection $spaces): bool
    {
        $colorNames = array_map(function (BoardSpace $space) {
            return $space->getDieSpace()->getDie()->getColor()->getValue();
        }, $spaces->getItems());

        return count($colorNames) === count(array_flip($colorNames));
    }

    /**
     * @param BoardSpaceCollection $spaces
     * @return int
     */
    public function getColorVarietySetCount(BoardSpaceCollection $spaces): int
    {
        $colorNames = array_map(function (BoardSpace $space) {
            return $space->getDieSpace()->getDie()->getColor()->getValue();
        }, $spaces->getItems());

        return min(array_count_values($colorNames));
    }
}
