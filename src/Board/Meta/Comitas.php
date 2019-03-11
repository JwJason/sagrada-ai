<?php
declare(strict_types=1);

namespace Sagrada\Board\Meta;

use Sagrada\Board\Difficulty\SagradaBoardDifficulty5;
use Sagrada\Board\Difficulty\SagradaBoardDifficultyInterface;

class Comitas extends AbstractMetaBoard
{
    public function getDifficulty(): SagradaBoardDifficultyInterface
    {
        return new SagradaBoardDifficulty5();
    }

    public function getGridSymbols(): array
    {
        return [
            ['y', '_', '2', '_', '6'],
            ['_', '4', '_', '5', 'y'],
            ['_', '_', '_', 'y', '5'],
            ['1', '2', 'y', '3', '_']
        ];
    }

    public function getName(): string
    {
        return 'Comitas';
    }
}
