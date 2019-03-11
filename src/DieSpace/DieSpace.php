<?php
declare(strict_types=1);

namespace Sagrada\DieSpace;

use Sagrada\Board\Space\Restriction\PartialRestrictionInterface;
use Sagrada\Board\Space\Restriction\NoRestriction;
use Sagrada\Dice\SagradaDie;

class DieSpace
{
    /**
     * @var ?SagradaDie
     */
    protected $die;

    /**
     * @return SagradaDie|null
     */
    public function getDie(): ?SagradaDie
    {
        return $this->die;
    }

    /**
     * @param SagradaDie $die
     */
    public function setDie(SagradaDie $die): void
    {
        $this->die = $die;
    }

    public function clearDie(): void
    {
        $this->die = null;
    }

    /**
     * @return bool
     */
    public function hasDie(): bool
    {
        return $this->die !== null;
    }
}
