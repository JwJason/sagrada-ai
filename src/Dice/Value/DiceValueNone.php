<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

use Sagrada\Dice\Shade\SagradaDiceShadeInterface;
use Sagrada\Dice\Shade\SagradaDiceShadeLight;
use Sagrada\Dice\Shade\SagradaDiceShadeNone;

final class DiceValueNone implements DiceValueInterface
{
    public function getShade() : SagradaDiceShadeInterface
    {
        return new SagradaDiceShadeNone();
    }

    public function getValue(): int
    {
        return 0;
    }

    public function getSymbol(): string
    {
        return '0';
    }
}
