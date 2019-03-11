<?php
declare(strict_types=1);

namespace Sagrada\Dice\Shade;

final class SagradaDiceShadeLight implements SagradaDiceShadeInterface
{
    public function getString(): string
    {
        return 'light';
    }

    public function getValue(): int
    {
        return 1;
    }
}
