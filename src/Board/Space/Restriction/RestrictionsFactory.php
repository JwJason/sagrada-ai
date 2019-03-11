<?php
declare(strict_types=1);

namespace Sagrada\Board\Space\Restriction;

use Sagrada\Board\Space\Restriction\ColorRestriction\ColorRestriction;
use Sagrada\Board\Space\Restriction\ColorRestriction\ColorRestrictionFactory;
use Sagrada\Board\Space\Restriction\ValueRestriction\ValueRestriction;
use Sagrada\Board\Space\Restriction\ValueRestriction\ValueRestrictionFactory;
use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\Value\DiceValueInterface;

/**
 * Class RestrictionsFactory
 * @package Sagrada\Board\Space\Restriction
 */
class RestrictionsFactory
{
    /**
     * @param string $symbol
     * @return PartialRestrictionInterface
     * @throws \Exception
     */
    public function createRestrictionFromSymbol(string $symbol): PartialRestrictionInterface
    {
        if ($symbol === '_') {
            return new NoRestriction();
        }

        /** @var PartialRestrictionFactoryInterface[] */
        $factories = [
            new ColorRestrictionFactory(),
            new ValueRestrictionFactory()
        ];

        foreach ($factories as $factory) {
            if ($factory->canCreateRestrictionFromSymbol($symbol)) {
                return $factory->createRestrictionFromSymbol($symbol);
            }
        }

        throw new \Exception("Invalid symbol: '$symbol'");
    }

    /**
     * @param string $symbol
     * @return Restrictions
     * @throws \Exception
     */
    public function createRestrictionsFromSymbol(string $symbol): Restrictions
    {
        if ($symbol === '_') {
            return $this->createEmptyRestrictions();
        }

        $colorRestrictionFactory = new ColorRestrictionFactory();
        $valueRestrictionFactory = new ValueRestrictionFactory();

        $colorRestriction = $colorRestrictionFactory->canCreateRestrictionFromSymbol($symbol)
            ? $colorRestrictionFactory->createRestrictionFromSymbol($symbol)
            : null;

        $valueRestriction = $valueRestrictionFactory->canCreateRestrictionFromSymbol($symbol)
            ? $valueRestrictionFactory->createRestrictionFromSymbol($symbol)
            : null;

        if (!$colorRestriction && !$valueRestriction) {
            throw new \Exception("Invalid symbol: '$symbol'");
        }

        return new Restrictions($colorRestriction, $valueRestriction);
    }

    /**
     * @param DiceColorInterface $color
     * @param DiceValueInterface $value
     * @return Restrictions
     */
    public function createRestrictionsFromDiceColorAndValue(
        DiceColorInterface $color,
        DiceValueInterface $value
    ): Restrictions {
        return new Restrictions(
            new ColorRestriction($color),
            new ValueRestriction($value)
        );
    }

    /**
     * @return Restrictions
     */
    public function createEmptyRestrictions(): Restrictions
    {
        return new Restrictions(new NoRestriction(), new NoRestriction());
    }
}
