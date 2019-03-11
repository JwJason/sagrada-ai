<?php
declare(strict_types=1);

namespace Sagrada\Dice\Value;

use Sagrada\Dice\Shade\SagradaDiceShadeInterface;
use Sagrada\Dice\Shade\SagradaDiceShadeLight;

final class DiceValue2 implements DiceValueInterface
{
    public function getShade() : SagradaDiceShadeInterface
    {
        return new SagradaDiceShadeLight();
    }

    public function getValue(): int
    {
        return 2;
    }

    public function getSymbol(): string
    {
        return '2';
    }
}
