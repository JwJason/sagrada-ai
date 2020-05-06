<?php
declare(strict_types=1);

namespace Sagrada\Board\Space;

/**
 * Class BoardSpaceCollection
 * @package Sagrada\Board\Space
 */
class BoardSpaceCollection
{
    /**
     * @var BoardSpace[]
     */
    protected $items;

    /**
     * BoardSpaceCollection constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->setItems($items);
    }

    /**
     * @return bool
     */
    public function allSpacesHaveDice(): bool
    {
        foreach ($this->getItems() as $space) {
            if ($space->hasDie() === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @return BoardSpaceCollection
     */
    public function getFilteredByHavingDice(): BoardSpaceCollection
    {
        $filtered = array_filter(
            $this->getItems(),
            static function (BoardSpace $space) {
                return $space->hasDie() === true;
            }
        );
        return new BoardSpaceCollection($filtered);
    }

    /**
     * @return BoardSpaceCollection
     */
    public function getFilteredByNotHavingDice(): BoardSpaceCollection
    {
        $filtered = array_filter(
            $this->getItems(),
            static function (BoardSpace $space) {
                return $space->hasDie() === false;
            }
        );
        return new BoardSpaceCollection($filtered);
    }
}
