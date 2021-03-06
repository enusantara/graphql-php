<?php

namespace Digia\GraphQL\Util;

use Digia\GraphQL\Error\InvariantException;
use Digia\GraphQL\Schema\Schema;
use Digia\GraphQL\Type\Definition\AbstractTypeInterface;
use Digia\GraphQL\Type\Definition\LeafTypeInterface;
use Digia\GraphQL\Type\Definition\ListType;
use Digia\GraphQL\Type\Definition\NamedTypeInterface;
use Digia\GraphQL\Type\Definition\NonNullType;
use Digia\GraphQL\Type\Definition\ObjectType;
use Digia\GraphQL\Type\Definition\TypeInterface;

class TypeHelper
{
    /**
     * Provided two types, return true if the types are equal (invariant).
     *
     * @param TypeInterface $typeA
     * @param TypeInterface $typeB
     * @return bool
     */
    public static function isEqualType(TypeInterface $typeA, TypeInterface $typeB): bool
    {
        // Equivalent types are equal.
        if ($typeA === $typeB) {
            return true;
        }

        // If either type is non-null, the other must also be non-null.
        if ($typeA instanceof NonNullType && $typeB instanceof NonNullType) {
            return self::isEqualType($typeA->getOfType(), $typeB->getOfType());
        }

        // If either type is a list, the other must also be a list.
        if ($typeA instanceof ListType && $typeB instanceof ListType) {
            return self::isEqualType($typeA->getOfType(), $typeB->getOfType());
        }

        // Otherwise the types are not equal.
        return false;
    }

    /**
     * Provided a type and a super type, return true if the first type is either
     * equal or a subset of the second super type (covariant).
     *
     * @param Schema        $schema
     * @param TypeInterface $maybeSubtype
     * @param TypeInterface $superType
     * @return bool
     *
     * @throws InvariantException
     */
    public static function isTypeSubtypeOf(
        Schema $schema,
        TypeInterface $maybeSubtype,
        TypeInterface $superType
    ): bool {
        // Equivalent type is a valid subtype.
        if ($maybeSubtype === $superType) {
            return true;
        }

        // If superType is non-null, maybeSubType must also be non-null.
        if ($superType instanceof NonNullType) {
            if ($maybeSubtype instanceof NonNullType) {
                return self::isTypeSubtypeOf($schema, $maybeSubtype->getOfType(), $superType->getOfType());
            }
            return false;
        }

        if ($maybeSubtype instanceof NonNullType) {
            // If superType is nullable, maybeSubType may be non-null or nullable.
            return self::isTypeSubtypeOf($schema, $maybeSubtype->getOfType(), $superType);
        }

        // If superType type is a list, maybeSubType type must also be a list.
        if ($superType instanceof ListType) {
            if ($maybeSubtype instanceof ListType) {
                return self::isTypeSubtypeOf($schema, $maybeSubtype->getOfType(), $superType->getOfType());
            }
            return false;
        }

        if ($maybeSubtype instanceof ListType) {
            // If superType is not a list, maybeSubType must also be not a list.
            return false;
        }

        // If superType type is an abstract type, maybeSubType type may be a currently
        // possible object type.
        if ($superType instanceof AbstractTypeInterface &&
            $maybeSubtype instanceof ObjectType &&
            $schema->isPossibleType($superType, $maybeSubtype)) {
            return true;
        }

        // Otherwise, the child type is not a valid subtype of the parent type.
        return false;
    }

    /**
     * Provided two composite types, determine if they "overlap". Two composite
     * types overlap when the Sets of possible concrete types for each intersect.
     *
     * This is often used to determine if a fragment of a given type could possibly
     * be visited in a context of another type.
     *
     * @param Schema        $schema
     * @param TypeInterface $typeA
     * @param TypeInterface $typeB
     * @return bool
     *
     * @throws InvariantException
     */
    public static function doTypesOverlap(Schema $schema, TypeInterface $typeA, TypeInterface $typeB): bool
    {
        // Equivalent types overlap
        if ($typeA === $typeB) {
            return true;
        }

        if ($typeA instanceof AbstractTypeInterface) {
            if ($typeB instanceof AbstractTypeInterface) {
                // If both types are abstract, then determine if there is any intersection
                // between possible concrete types of each.
                return arraySome($schema->getPossibleTypes($typeA),
                    function (NamedTypeInterface $type) use ($schema, $typeB) {
                        return $schema->isPossibleType($typeB, $type);
                    });
            }

            // Determine if the latter type is a possible concrete type of the former.
            /** @noinspection PhpParamsInspection */
            return $schema->isPossibleType($typeA, $typeB);
        }

        if ($typeB instanceof AbstractTypeInterface) {
            // Determine if the former type is a possible concrete type of the latter.
            /** @noinspection PhpParamsInspection */
            return $schema->isPossibleType($typeB, $typeA);
        }

        // Otherwise the types do not overlap.
        return false;
    }

    /**
     * Two types conflict if both types could not apply to a value simultaneously.
     * Composite types are ignored as their individual field types will be compared
     * later recursively. However List and Non-Null types must match.
     *
     * @param TypeInterface $typeA
     * @param TypeInterface $typeB
     * @return bool
     */
    public static function compareTypes(TypeInterface $typeA, TypeInterface $typeB): bool
    {
        if ($typeA instanceof ListType) {
            return $typeB instanceof ListType
                ? self::compareTypes($typeA->getOfType(), $typeB->getOfType())
                : true;
        }
        if ($typeB instanceof ListType) {
            return true;
        }
        if ($typeA instanceof NonNullType) {
            return $typeB instanceof NonNullType
                ? self::compareTypes($typeA->getOfType(), $typeB->getOfType())
                : true;
        }
        if ($typeB instanceof NonNullType) {
            return true;
        }
        if ($typeA instanceof LeafTypeInterface || $typeB instanceof LeafTypeInterface) {
            return $typeA !== $typeB;
        }
        return false;
    }
}
