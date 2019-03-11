<?php
declare(strict_types=1);

namespace Sagrada\Dice\Shade;

final class SagradaDiceShadeDark implements SagradaDiceShadeInterface
{
    public function getString(): string
    {
        return 'dark';
    }

    public function getValue(): int
    {
        return 3;
    }
}
