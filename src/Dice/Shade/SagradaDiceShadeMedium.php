<?php
declare(strict_types=1);

namespace Sagrada\Dice\Shade;

final class SagradaDiceShadeMedium implements SagradaDiceShadeInterface
{
    public function getString(): string
    {
        return 'medium';
    }

    public function getValue(): int
    {
        return 2;
    }
}
