<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction;

interface PartialRestrictionInterface
{
    /**
     * @return string
     */
    public function getSymbol(): string;
}
