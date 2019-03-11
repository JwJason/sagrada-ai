<?php
declare(strict_types=1);

namespace Sagrada\Dice\Shade;

interface SagradaDiceShadeInterface
{
    public function getString() : string;
    public function getValue() : int;
}
