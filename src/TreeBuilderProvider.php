<?php

namespace Menschoid\AutoTreeBuilderBundle;

use Menschoid\AutoTreeBuilderBundle\Attributes\ConfigParameter;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class TreeBuilderProvider
{
    /**
     * Returns the TreeBuilder for a given class name.
     */
    public function getTreeBuilder(string $className): TreeBuilder
    {
        $configTreeInformation = $this->getAttributeInformation($className);

        return $this->buildTreeBuilder($className, $configTreeInformation);
    }

    /**
     * Gathering all the existing information from the property attributes and formatting them
     * in a way that all the following steps can process.
     *
     * @return array<string,array>
     * @throws ReflectionException
     */
    public function getAttributeInformation(string $className): array
    {
        $reflectionClass = new ReflectionClass($className);
        $configTreeInformation = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(ConfigParameter::class);

            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();

                $name = $property->getName();
                $configTreeInformation[$name] = [
                    'type' => $property->getType()->getName(),
                    'restrictions' => $attributeInstance->restrictions,
                    'alias' => $attributeInstance->alias ?? ''
                ];
            }
        }

        return $configTreeInformation;
    }

    /**
     * @param string $className
     * @param array<string,array> $treeBuilderInformation
     * @return TreeBuilder
     */
    public function buildTreeBuilder (string $className, array $treeBuilderInformation): TreeBuilder
    {
        $treeBuilder = new TreeBuilder($className);
        $rootNodeChildren = $treeBuilder->getRootNode()->children();
        foreach ($treeBuilderInformation as $name => $nodeInformation) {
            match ($nodeInformation['type']) {
                'bool' => $this->addBooleanNode($rootNodeChildren, $name),
                'int' => $this->addIntegerNode($rootNodeChildren, $name),
                default => $this->addScalarNode($rootNodeChildren, $name)
            };
            $rootNodeChildren->scalarNode($name)->end();
        }
        $treeBuilder->setPathSeparator('/');

        return $treeBuilder;
    }

    private function addScalarNode(NodeBuilder$rootNodeChildren, string $name): void
    {
        $rootNodeChildren->scalarNode($name)->end();
    }

    private function addBooleanNode(NodeBuilder$rootNodeChildren, string $name): void
    {
        $rootNodeChildren->booleanNode($name)->end();
    }

    private function addIntegerNode(NodeBuilder$rootNodeChildren, string $name): void
    {
        $rootNodeChildren->integerNode($name)->end();
    }
}