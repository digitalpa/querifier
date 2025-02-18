<?php

namespace Paknahad\Querifier;

use Paknahad\Querifier\Parts\AbstractCondition;
use Paknahad\Querifier\Parts\Combiner;

class Query
{
    private $conditions = [];
    private $children = [];

    /**
     * @param AbstractCondition $condition
     */
    public function addCondition(AbstractCondition $condition): void
    {
        $this->conditions[$condition->getName()] = $condition;
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return Query
     */
    public function rearrange(): self
    {
        foreach ($this->conditions as $name => $condition) {
            $this->getCombinedConditions($condition);
        }

        foreach ($this->children as $name) {
            unset($this->conditions[$name]);
        }

        return $this;
    }

    /**
     * @param AbstractCondition $condition
     * @param bool              $isChild
     *
     * @return AbstractCondition
     */
    private function getCombinedConditions(AbstractCondition $condition, bool $isChild = false): AbstractCondition
    {
        if ($condition instanceof Combiner) {
            foreach ($condition->getConditionsName() as $subCondition) {
                $condition->addCondition($this->getCombinedConditions($this->conditions[$subCondition], true));
            }
        }

        if ($isChild) {
            $this->children[] = $condition->getName();
        }

        return $condition;
    }
}
