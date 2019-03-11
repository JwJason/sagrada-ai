<?php
declare(strict_types=1);

namespace Sagrada\Board\Difficulty;

class SagradaBoardDifficulty5 implements SagradaBoardDifficultyInterface
{
    public function getValue(): int
    {
        return 5;
    }
}
