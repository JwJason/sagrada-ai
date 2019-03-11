<?php
declare(strict_types=1);

namespace Sagrada\Board\Meta;

use Sagrada\Board\Difficulty\SagradaBoardDifficultyInterface;

abstract class AbstractMetaBoard
{
    abstract public function getDifficulty(): SagradaBoardDifficultyInterface;
    abstract public function getGridSymbols(): array;
    abstract public function getName(): string;
}
