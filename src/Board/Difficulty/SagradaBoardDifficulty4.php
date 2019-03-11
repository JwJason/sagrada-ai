<?php
declare(strict_types=1);

namespace Sagrada\Board\Difficulty;

class SagradaBoardDifficulty4 implements SagradaBoardDifficultyInterface
{
    public function getValue(): int
    {
        return 4;
    }
}
