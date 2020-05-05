<?php
declare(strict_types=1);

namespace Sagrada\Turn;

use Sagrada\Turn;

class Pass extends Turn
{
    public function __toString()
    {
        return '#PASS#';
    }
}
