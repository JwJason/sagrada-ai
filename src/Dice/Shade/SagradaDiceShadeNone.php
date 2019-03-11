<?php
declare(strict_types=1);

namespace Sagrada\Dice\Shade;

final class SagradaDiceShadeNone implements SagradaDiceShadeInterface
{
    public function getString(): string
    {
        return 'none';
    }

    public function getValue(): int
    {
        return 0;
    }
}
