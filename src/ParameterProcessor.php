<?php

namespace Menschoid\AutoTreeBuilderBundle;

class ParameterProcessor
{
    public function flattenArray($array, $prefix = '')
    {
        $result = array();

        foreach ($array as $key => $value)
        {
            $new_key = $prefix . (empty($prefix) ? '' : '.') . $key;

            if (is_array($value))
            {
                $result = array_merge($result, $this->flattenArray($value, $new_key));
            }
            else
            {
                $result[$new_key] = $value;
            }
        }

        return $result;
    }

    public function replaceAliasesWithPropertyNames(array $parameterList, array $attributeInformation): array
    {
        $aliasReplacementList = $this->getAliasReplacementList($attributeInformation);

        $replacedParamList = [];

        foreach ($parameterList as $paramKey => $paramValue) {
            if (array_key_exists($paramKey, $aliasReplacementList)) {
                $paramKey = $aliasReplacementList[$paramKey];
            }
            $replacedParamList[$paramKey] = $paramValue;
        }

        return $replacedParamList;
    }

    private function getAliasReplacementList (array $attributeInformation): array
    {
        $aliasReplacementList = [];

        foreach ($attributeInformation as $key => $information) {
            $aliasReplacementList[$information['alias']] = $key;
        }

        return $aliasReplacementList;
    }

}