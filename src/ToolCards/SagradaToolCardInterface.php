<?php
declare(strict_types=1);

namespace Sagrada\ToolCards;

use Sagrada\RestrictionModifiers\SagradaRestrictionModifierInterface;

interface SagradaToolCardInterface
{
    /**
     * @return SagradaRestrictionModifierInterface
     */
    public function getRestrictionModifier(): SagradaRestrictionModifierInterface;

    /**
     * @param SagradaRestrictionModifierInterface $restrictionModifier
     */
    public function setRestrictionModifier(SagradaRestrictionModifierInterface $restrictionModifier);
}
