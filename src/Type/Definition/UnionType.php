<?php

namespace Digia\GraphQL\Type\Definition;

use Digia\GraphQL\Language\AST\Node\NodeTrait;
use Digia\GraphQL\Language\AST\Node\UnionTypeDefinitionNode;


/**
 * Union Type Definition
 *
 * When a field can return one of a heterogeneous set of types, a Union type
 * is used to describe what types are possible as well as providing a function
 * to determine which type is actually used when the field is resolved.
 *
 * Example:
 *
 *     const PetType = new GraphQLUnionType({
 *       name: 'Pet',
 *       types: [ DogType, CatType ],
 *       resolveType(value) {
 *         if (value instanceof Dog) {
 *           return DogType;
 *         }
 *         if (value instanceof Cat) {
 *           return CatType;
 *         }
 *       }
 *     });
 *
 */

/**
 * Class UnionType
 *
 * @package Digia\GraphQL\Type\Definition
 * @property UnionTypeDefinitionNode $astNode
 */
class UnionType implements AbstractTypeInterface, CompositeTypeInterface, OutputTypeInterface
{

    use NameTrait;
    use DescriptionTrait;
    use ResolveTypeTrait;
    use NodeTrait;
    use ConfigTrait;

    /**
     * @var TypeInterface[]
     */
    private $types;

    /**
     * @return TypeInterface[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param TypeInterface $type
     * @return $this
     */
    public function addType(TypeInterface $type)
    {
        $this->types[] = $type;

        return $this;
    }

    /**
     * @param TypeInterface[] $types
     * @return $this
     */
    protected function setTypes(array $types)
    {
        foreach ($types as $type) {
            $this->addType($type);
        }

        return $this;
    }
}