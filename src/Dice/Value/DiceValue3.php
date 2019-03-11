<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

use Sagrada\Dice\Shade\SagradaDiceShadeInterface;
use Sagrada\Dice\Shade\SagradaDiceShadeMedium;

final class DiceValue3 implements DiceValueInterface
{
    public function getShade() : SagradaDiceShadeInterface
    {
        return new SagradaDiceShadeMedium();
    }

    public function getValue(): int
    {
        return 3;
    }

    public function getSymbol(): string
    {
        return '3';
    }
}
