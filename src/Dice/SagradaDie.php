<?php
declare(strict_types=1);

namespace Sagrada\Dice;

use Sagrada\Dice\Color\DiceColorInterface;
use Sagrada\Dice\Value\DiceValueInterface;

class SagradaDie
{
    protected $color;
    protected $value;

    /**
     * SagradaDie constructor.
     * @param DiceColorInterface $color
     * @param $value
     * @param $coordinates
     */
    public function __construct(DiceColorInterface $color, DiceValueInterface $value)
    {
        $this->color = $color;
        $this->value = $value;
    }

    /**
     * @return DiceColorInterface
     */
    public function getColor(): DiceColorInterface
    {
        return $this->color;
    }

    /**
     * @param DiceColorInterface $color
     */
    public function setColor(DiceColorInterface $color): void
    {
        $this->color = $color;
    }

    /**
     * @return DiceValueInterface
     */
    public function getValue(): DiceValueInterface
    {
        return $this->value;
    }

    /**
     * @param DiceValueInterface $value
     */
    public function setValue(DiceValueInterface $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%d-%s',
            $this->getValue()->getSymbol(),
            $this->getColor()->getSymbol()
        );
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * @param SagradaDie $die
     * @return bool
     */
    public function equals(SagradaDie $die): bool
    {
        $color = $die->getColor();
        $value = $die->getValue();
        return $this->getColor() instanceof $color
            && $this->getValue() instanceof $value;
    }
}
