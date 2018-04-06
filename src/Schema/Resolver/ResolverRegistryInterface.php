<?php

namespace Digia\GraphQL\Schema\Resolver;

interface ResolverRegistryInterface
{
    /**
     * @param string            $typeName
     * @param ResolverInterface $resolver
     */
    public function register(string $typeName, ResolverInterface $resolver): void;

    /**
     * @param string $typeName
     * @param string $fieldName
     * @return callable|null
     */
    public function getFieldResolver(string $typeName, string $fieldName): ?callable;

    /**
     * @param string $typeName
     * @return callable|null
     */
    public function getTypeResolver(string $typeName): ?callable;

    /**
     * @param string $typeName
     * @return ResolverInterface|null
     */
    public function getResolver(string $typeName): ?ResolverInterface;
}
