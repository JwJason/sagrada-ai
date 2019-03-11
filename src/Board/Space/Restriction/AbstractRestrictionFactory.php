<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction;

interface PartialRestrictionFactoryInterface
{
    public function canCreateRestrictionFromSymbol(string $symbol): bool;
}
