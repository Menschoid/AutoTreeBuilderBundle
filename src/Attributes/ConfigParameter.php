<?php

namespace Menschoid\AutoTreeBuilderBundle\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ConfigParameter
{
    public string $alias;

    public array $restrictions;

    public function __construct(string $alias, array $restrictions)
    {
        $this->alias = $alias;
        $this->restrictions = $restrictions;
    }
}