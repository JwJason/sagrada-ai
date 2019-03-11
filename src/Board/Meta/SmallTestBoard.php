<?php
/**
 * Created by PhpStorm.
 * User: jasonb
 * Date: 2019-03-11
 * Time: 02:24
 */

namespace Sagrada\Board\Meta;


use Sagrada\Board\Difficulty\SagradaBoardDifficulty2;
use Sagrada\Board\Difficulty\SagradaBoardDifficultyInterface;

class SmallTestBoard extends AbstractMetaBoard
{
    public function getDifficulty(): SagradaBoardDifficultyInterface
    {
        return new SagradaBoardDifficulty2();
    }

    public function getGridSymbols(): array
    {
        return [
            ['y', '_', '2'],
            ['_', '4', '_'],
            ['_', '_', '_'],
        ];
    }

    public function getName(): string
    {
        return 'Small Test Boar';
    }
}
