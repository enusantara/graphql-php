<?php

namespace Digia\GraphQL\Type;

use Digia\GraphQL\Language\AST\DirectiveLocationEnum;
use Digia\GraphQL\Type\Definition\AbstractType;
use Digia\GraphQL\Type\Definition\DirectiveInterface;
use Digia\GraphQL\Type\Definition\NamedTypeInterface;
use Digia\GraphQL\Type\Definition\TypeInterface;
use Digia\GraphQL\Type\Definition\EnumType;
use Digia\GraphQL\Type\Definition\Field;
use Digia\GraphQL\Type\Definition\InputObjectType;
use Digia\GraphQL\Type\Definition\InterfaceType;
use Digia\GraphQL\Type\Definition\ListType;
use Digia\GraphQL\Type\Definition\NonNullType;
use Digia\GraphQL\Type\Definition\ObjectType;
use Digia\GraphQL\Type\Definition\ScalarType;
use Digia\GraphQL\Type\Definition\UnionType;
use function Digia\GraphQL\Util\arraySome;

/**
 * @return ObjectType
 */
function __Schema(): ObjectType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLObjectType([
            'name'            => '__Schema',
            'isIntrospection' => true,
            'description'     =>
                'A GraphQL Schema defines the capabilities of a GraphQL server. It ' .
                'exposes all available types and directives on the server, as well as ' .
                'the entry points for query, mutation, and subscription operations.',
            'fields'          => function () {
                return [
                    'types'            => [
                        'description' => 'A list of all types supported by this server.',
                        'type'        => GraphQLNonNull(GraphQLList(GraphQLNonNull(__Type()))),
                        'resolve'     => function (SchemaInterface $schema): array {
                            return array_values($schema->getTypeMap());
                        },
                    ],
                    'queryType'        => [
                        'description' => 'The type that query operations will be rooted at.',
                        'type'        => GraphQLNonNull(__Type()),
                        'resolve'     => function (SchemaInterface $schema): ObjectType {
                            return $schema->getQuery();
                        },
                    ],
                    'mutationType'     => [
                        'description' =>
                            'If this server supports mutation, the type that ' .
                            'mutation operations will be rooted at.',
                        'type'        => __Type(),
                        'resolve'     => function (SchemaInterface $schema): ObjectType {
                            return $schema->getMutation();
                        },
                    ],
                    'subscriptionType' => [
                        'description' =>
                            'If this server support subscription, the type that ' .
                            'subscription operations will be rooted at.',
                        'type'        => __Type(),
                        'resolve'     => function (SchemaInterface $schema): ObjectType {
                            return $schema->getSubscription();
                        },
                    ],
                    'directives'       => [
                        'description' => 'A list of all directives supported by this server.',
                        'type'        => GraphQLNonNull(GraphQLList(GraphQLNonNull(__Directive()))),
                        'resolve'     => function (SchemaInterface $schema): array {
                            return $schema->getDirectives();
                        },
                    ],
                ];
            }
        ]);
    }

    return $instance;
}

/**
 * @return ObjectType
 */
function __Directive(): ObjectType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLObjectType([
            'name'            => '__Directive',
            'isIntrospection' => true,
            'description'     =>
                'A Directive provides a way to describe alternate runtime execution and ' .
                'type validation behavior in a GraphQL document.' .
                "\n\nIn some cases, you need to provide options to alter GraphQL's " .
                'execution behavior in ways field arguments will not suffice, such as ' .
                'conditionally including or skipping a field. Directives provide this by ' .
                'describing additional information to the executor.',
            'fields'          => function () {
                return [
                    'name'        => ['type' => GraphQLNonNull(GraphQLString())],
                    'description' => ['type' => GraphQLString()],
                    'locations'   => [
                        'type' => GraphQLNonNull(GraphQLList(GraphQLNonNull(__DirectiveLocation()))),
                    ],
                    'args'        => [
                        'type'    => GraphQLNonNull(GraphQLList(GraphQLNonNull(__InputValue()))),
                        'resolve' => function (DirectiveInterface $directive): array {
                            return $directive->getArguments() ?: [];
                        },
                    ],
                ];
            }
        ]);
    }

    return $instance;
}

/**
 * @return EnumType
 */
function __DirectiveLocation(): EnumType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLEnumType([
            'name'            => '__DirectiveLocation',
            'isIntrospection' => true,
            'description'     =>
                'A Directive can be adjacent to many parts of the GraphQL language, a ' .
                '__DirectiveLocation describes one such possible adjacencies.',
            'values'          => [
                DirectiveLocationEnum::QUERY                  => [
                    'description' => 'Location adjacent to a query operation.',
                ],
                DirectiveLocationEnum::MUTATION               => [
                    'description' => 'Location adjacent to a mutation operation.',
                ],
                DirectiveLocationEnum::SUBSCRIPTION           => [
                    'description' => 'Location adjacent to a subscription operation.',
                ],
                DirectiveLocationEnum::FIELD                  => [
                    'description' => 'Location adjacent to a field.',
                ],
                DirectiveLocationEnum::FRAGMENT_DEFINITION    => [
                    'description' => 'Location adjacent to a fragment definition.',
                ],
                DirectiveLocationEnum::FRAGMENT_SPREAD        => [
                    'description' => 'Location adjacent to a fragment spread.',
                ],
                DirectiveLocationEnum::INLINE_FRAGMENT        => [
                    'description' => 'Location adjacent to an inline fragment.',
                ],
                DirectiveLocationEnum::SCHEMA                 => [
                    'description' => 'Location adjacent to a schema definition.',
                ],
                DirectiveLocationEnum::SCALAR                 => [
                    'description' => 'Location adjacent to a scalar definition.',
                ],
                DirectiveLocationEnum::OBJECT                 => [
                    'description' => 'Location adjacent to an object type definition.',
                ],
                DirectiveLocationEnum::FIELD_DEFINITION       => [
                    'description' => 'Location adjacent to a field definition.',
                ],
                DirectiveLocationEnum::ARGUMENT_DEFINITION    => [
                    'description' => 'Location adjacent to an argument definition.',
                ],
                DirectiveLocationEnum::INTERFACE              => [
                    'description' => 'Location adjacent to an interface definition.',
                ],
                DirectiveLocationEnum::UNION                  => [
                    'description' => 'Location adjacent to a union definition.',
                ],
                DirectiveLocationEnum::ENUM                   => [
                    'description' => 'Location adjacent to an enum definition.',
                ],
                DirectiveLocationEnum::ENUM_VALUE             => [
                    'description' => 'Location adjacent to an enum value definition.',
                ],
                DirectiveLocationEnum::INPUT_OBJECT           => [
                    'description' => 'Location adjacent to an input object type definition.',
                ],
                DirectiveLocationEnum::INPUT_FIELD_DEFINITION => [
                    'description' => 'Location adjacent to an input object field definition.',
                ],
            ],
        ]);
    }

    return $instance;
}

/**
 * @return ObjectType
 */
function __Type(): ObjectType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLObjectType([
            'name'            => '__Type',
            'isIntrospection' => true,
            'description'     =>
                'The fundamental unit of any GraphQL Schema is the type. There are ' .
                'many kinds of types in GraphQL as represented by the `__TypeKind` enum.' .
                '\n\nDepending on the kind of a type, certain fields describe ' .
                'information about that type. Scalar types provide no information ' .
                'beyond a name and description, while Enum types provide their values. ' .
                'Object and Interface types provide the fields they describe. Abstract ' .
                'types, Union and Interface, provide the Object types possible ' .
                'at runtime. List and NonNull types compose other types.',
            'fields'          => function () {
                return [
                    'kind'          => [
                        'type'    => GraphQLNonNull(__TypeKind()),
                        'resolve' => function (TypeInterface $type) {
                            if ($type instanceof ScalarType) {
                                return TypeKindEnum::SCALAR;
                            }
                            if ($type instanceof ObjectType) {
                                return TypeKindEnum::OBJECT;
                            }
                            if ($type instanceof InterfaceType) {
                                return TypeKindEnum::INTERFACE;
                            }
                            if ($type instanceof UnionType) {
                                return TypeKindEnum::UNION;
                            }
                            if ($type instanceof EnumType) {
                                return TypeKindEnum::ENUM;
                            }
                            if ($type instanceof InputObjectType) {
                                return TypeKindEnum::INPUT_OBJECT;
                            }
                            if ($type instanceof ListType) {
                                return TypeKindEnum::LIST;
                            }
                            if ($type instanceof NonNullType) {
                                return TypeKindEnum::NON_NULL;
                            }

                            throw new \Exception(sprintf('Unknown kind of type: %s', $type));
                        },
                    ],
                    'name'          => ['type' => GraphQLString()],
                    'description'   => ['type' => GraphQLString()],
                    'fields'        => [
                        'type'    => GraphQLList(GraphQLNonNull(__Field())),
                        'args'    => [
                            'includeDeprecated' => ['type' => GraphQLBoolean(), 'defaultValue' => false],
                        ],
                        'resolve' => function (TypeInterface $type, array $args): ?array {
                            [$includeDeprecated] = $args;

                            if ($type instanceof ObjectType || $type instanceof InterfaceType) {
                                $fields = array_values($type->getFields());

                                if (!$includeDeprecated) {
                                    $fields = array_filter($fields, function (Field $field) {
                                        return !$field->isDeprecated();
                                    });
                                }

                                return $fields;
                            }

                            return null;
                        },
                    ],
                    'interfaces'    => [
                        'type'    => GraphQLList(GraphQLNonNull(__Type())),
                        'resolve' => function (TypeInterface $type): ?array {
                            return $type instanceof ObjectType ? $type->getInterfaces() : null;
                        },
                    ],
                    'possibleTypes' => [
                        'type'    => GraphQLList(GraphQLNonNull(__Type())),
                        'resolve' => function (TypeInterface $type, $args, $context, $info): ?array {
                            /** @var SchemaInterface $schema */
                            [$schema] = $info;
                            /** @noinspection PhpParamsInspection */
                            return $type instanceof AbstractType ? $schema->getPossibleTypes($type) : null;
                        },
                    ],
                    'enumValues'    => [
                        'type'    => GraphQLList(GraphQLNonNull(__EnumValue())),
                        'args'    => [
                            'includeDeprecated' => ['type' => GraphQLBoolean(), 'defaultValue' => false],
                        ],
                        'resolve' => function (TypeInterface $type, array $args): ?array {
                            [$includeDeprecated] = $args;

                            if ($type instanceof EnumType) {
                                $values = array_values($type->getValues());

                                if (!$includeDeprecated) {
                                    $values = array_filter($values, function (Field $field) {
                                        return !$field->isDeprecated();
                                    });
                                }

                                return $values;
                            }

                            return null;
                        },
                    ],
                    'inputFields'   => [
                        'type'    => GraphQLList(GraphQLNonNull(__InputValue())),
                        'resolve' => function (TypeInterface $type): ?array {
                            return $type instanceof InputObjectType ? $type->getFields() : null;
                        },
                    ],
                    'ofType'        => ['type' => __Type()],
                ];
            }
        ]);
    }

    return $instance;
}

/**
 * @return ObjectType
 */
function __Field(): ObjectType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLObjectType([
            'name'            => '__Field',
            'isIntrospection' => true,
            'description'     =>
                'Object and Interface types are described by a list of Fields, each of ' .
                'which has a name, potentially a list of arguments, and a return type.',
            'fields'          => function () {
                return [
                    'name'              => ['type' => GraphQLNonNull(GraphQLString())],
                    'description'       => ['type' => GraphQLString()],
                    'args'              => [
                        'type'    => GraphQLNonNull(GraphQLList(GraphQLNonNull(__InputValue()))),
                        'resolve' => function (DirectiveInterface $directive): array {
                            return $directive->getArguments() ?: [];
                        },
                    ],
                    'type'              => ['type' => GraphQLNonNull(__Type())],
                    'isDeprecated'      => ['type' => GraphQLNonNull(GraphQLBoolean())],
                    'deprecationReason' => ['type' => GraphQLString()],
                ];
            }
        ]);
    }

    return $instance;
}

/**
 * @return ObjectType
 */
function __InputValue(): ObjectType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLObjectType([
            'name'            => '__InputValue',
            'isIntrospection' => true,
            'description'     =>
                'Arguments provided to Fields or Directives and the input fields of an ' .
                'InputObject are represented as Input Values which describe their type ' .
                'and optionally a default value.',
            'fields'          => function () {
                return [
                    'name'         => ['type' => GraphQLNonNull(GraphQLString())],
                    'description'  => ['type' => GraphQLString()],
                    'type'         => ['type' => GraphQLNonNull(__Type())],
                    'defaultValue' => [
                        'type'        => GraphQLString(),
                        'description' =>
                            'A GraphQL-formatted string representing the default value for this ' .
                            'input value.',
                        'resolve'     => function ($inputValue) {
                            // TODO: Implement this when we have support for printing AST.
                            return null;
                        }
                    ],
                ];
            }
        ]);
    }

    return $instance;
}

/**
 * @return ObjectType
 */
function __EnumValue(): ObjectType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLObjectType([
            'name'            => '__EnumValue',
            'isIntrospection' => true,
            'description'     =>
                'One possible value for a given Enum. Enum values are unique values, not ' .
                'a placeholder for a string or numeric value. However an Enum value is ' .
                'returned in a JSON response as a string.',
            'fields'          => function () {
                return [
                    'name'              => ['type' => GraphQLNonNull(GraphQLString())],
                    'description'       => ['type' => GraphQLString()],
                    'isDeprecated'      => ['type' => GraphQLNonNull(GraphQLBoolean())],
                    'deprecationReason' => ['type' => GraphQLString()],
                ];
            }
        ]);
    }

    return $instance;
}

function __TypeKind(): EnumType
{
    static $instance = null;

    if (null === $instance) {
        $instance = GraphQLEnumType([
            'name'            => '__TypeKind',
            'isIntrospection' => true,
            'description'     => 'An enum describing what kind of type a given `__Type` is.',
            'values'          => [
                TypeKindEnum::SCALAR       => [
                    'description' => 'Indicates this type is a scalar.',
                ],
                TypeKindEnum::OBJECT       => [
                    'description' => 'Indicates this type is an object. `fields` and `interfaces` are valid fields.',
                ],
                TypeKindEnum::INTERFACE    => [
                    'description' => 'Indicates this type is an interface. `fields` and `possibleTypes` are valid fields.',
                ],
                TypeKindEnum::UNION        => [
                    'description' => 'Indicates this type is a union. `possibleTypes` is a valid field.',
                ],
                TypeKindEnum::ENUM         => [
                    'description' => 'Indicates this type is an enum. `enumValues` is a valid field.',
                ],
                TypeKindEnum::INPUT_OBJECT => [
                    'description' => 'Indicates this type is an input object. `inputFields` is a valid field.',
                ],
                TypeKindEnum::LIST         => [
                    'description' => 'Indicates this type is a list. `ofType` is a valid field.',
                ],
                TypeKindEnum::NON_NULL     => [
                    'description' => 'Indicates this type is a non-null. `ofType` is a valid field.',
                ],
            ],
        ]);
    }

    return $instance;
}

/**
 * @return Field
 * @throws \TypeError
 */
function SchemaMetaFieldDefinition(): Field
{
    return new Field([
        'name'        => '__schema',
        'type'        => GraphQLNonNull(__Schema()),
        'description' => 'Access the current type schema of this server.',
        'resolve'     => function ($source, $args, $context, $info): SchemaInterface {
            [$schema] = $info;
            return $schema;
        }
    ]);
}

/**
 * @return Field
 * @throws \TypeError
 */
function TypeMetaFieldDefinition(): Field
{
    return new Field([
        'name'        => '__type',
        'type'        => __Type(),
        'description' => 'Request the type information of a single type.',
        'args'        => [
            'name' => ['type' => GraphQLNonNull(GraphQLString())],
        ],
        'resolve'     => function ($source, $args, $context, $info): TypeInterface {
            /** @var SchemaInterface $schema */
            [$name] = $args;
            [$schema] = $info;
            return $schema->getType($name);
        }
    ]);
}

/**
 * @return Field
 * @throws \TypeError
 */
function TypeNameMetaFieldDefinition(): Field
{
    return new Field([
        'name'        => '__typename',
        'type'        => GraphQLNonNull(GraphQLString()),
        'description' => 'The name of the current Object type at runtime.',
        'resolve'     => function ($source, $args, $context, $info): string {
            /** @var NamedTypeInterface $parentType */
            [$parentType] = $info;
            return $parentType->getName();
        }
    ]);
}

/**
 * @return array
 */
function introspectionTypes(): array
{
    return [
        __Schema(),
        __Directive(),
        __DirectiveLocation(),
        __Type(),
        __Field(),
        __InputValue(),
        __EnumValue(),
        __TypeKind(),
    ];
}

/**
 * @param TypeInterface $type
 * @return bool
 */
function isIntrospectionType(TypeInterface $type): bool
{
    return arraySome(
        introspectionTypes(),
        function (TypeInterface $introspectionType) use ($type) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $type->getName() === $introspectionType->getName();
        }
    );
}
