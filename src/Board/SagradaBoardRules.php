<?php
declare(strict_types=1);

namespace Sagrada\Board;

abstract class AbstractSagradaBoardRules
{
    abstract public function getRows();
    abstract public function getCols();

    public function boardIsValid(Board $board) : bool
    {

    }
}