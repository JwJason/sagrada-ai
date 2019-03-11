<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction;

use Sagrada\Board\Space\Restriction\ColorRestriction\ColorRestrictionInterface;
use Sagrada\Board\Space\Restriction\ValueRestriction\ValueRestrictionInterface;

/**
 * Class Restrictions
 * @package Sagrada\Board\Space\Restriction
 */
class Restrictions
{
    /**
     * @var ColorRestrictionInterface
     */
    protected $colorRestriction;
    /**
     * @var ValueRestrictionInterface
     */
    protected $valueRestriction;

    /**
     * Restrictions constructor.
     * @param ColorRestrictionInterface $colorRestriction
     * @param ValueRestrictionInterface $valueRestriction
     */
    public function __construct(ColorRestrictionInterface $colorRestriction, ValueRestrictionInterface $valueRestriction)
    {
        $this->colorRestriction = $colorRestriction;
        $this->valueRestriction = $valueRestriction;
    }

    /**
     * @return ColorRestrictionInterface
     */
    public function getColorRestriction(): ColorRestrictionInterface
    {
        return $this->colorRestriction;
    }

    /**
     * @return ValueRestrictionInterface
     */
    public function getValueRestriction(): ValueRestrictionInterface
    {
        return $this->valueRestriction;
    }

    /**
     * @return bool
     */
    public function hasAnyRestrictions(): bool
    {
        return $this->hasColorRestriction() || $this->hasValueRestriction();
    }

    /**
     * @return bool
     */
    public function hasColorRestriction(): bool
    {
        return ($this->getColorRestriction() instanceof NoRestriction) === false;
    }

    /**
     * @return bool
     */
    public function hasValueRestriction(): bool
    {
        return ($this->getValueRestriction() instanceof NoRestriction) === false;
    }
}
