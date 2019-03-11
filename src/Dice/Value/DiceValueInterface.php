<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

use Sagrada\Dice\Shade\SagradaDiceShadeInterface;

interface DiceValueInterface
{
    /**
     * @return SagradaDiceShadeInterface
     */
    public function getShade() : SagradaDiceShadeInterface;

    /**
     * @return string
     */
    public function getSymbol() : string;

    /**
     * @return int
     */
    public function getValue() : int;
}
