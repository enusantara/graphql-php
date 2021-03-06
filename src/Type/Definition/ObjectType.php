<?php

namespace Digia\GraphQL\Type\Definition;

use Digia\GraphQL\Error\InvariantException;
use Digia\GraphQL\Language\Node\ASTNodeAwareInterface;
use Digia\GraphQL\Language\Node\ASTNodeTrait;
use Digia\GraphQL\Language\Node\NameAwareInterface;
use Digia\GraphQL\Language\Node\ObjectTypeDefinitionNode;
use Digia\GraphQL\Language\Node\ObjectTypeExtensionNode;
use React\Promise\PromiseInterface;
use function Digia\GraphQL\Type\resolveThunk;

/**
 * Object Type Definition
 *
 * Almost all of the GraphQL types you define will be object types. Object types
 * have a name, but most importantly describe their fields.
 *
 * Example:
 *
 *     $AddressType = newObjectType([
 *       'name'   => 'Address',
 *       'fields' => [
 *         'street'    => ['type' => stringType()],
 *         'number'    => ['type' => intType()],
 *         'formatted' => [
 *           'type'    => stringType(),
 *           'resolve' => function ($obj) {
 *             return $obj->number . ' ' . $obj->street
 *           }
 *         ]
 *       ]
 *     ]);
 *
 * When two types need to refer to each other, or a type needs to refer to
 * itself in a field, you can use a function expression (aka a closure or a
 * thunk) to supply the fields lazily.
 *
 * Example:
 *
 *     $PersonType = newObjectType([
 *       'name' => 'Person',
 *       'fields' => function () {
 *         return [
 *           'name'       => ['type' => stringType()],
 *           'bestFriend' => ['type' => $PersonType],
 *         ];
 *       }
 *     ]);
 */
class ObjectType implements NamedTypeInterface, CompositeTypeInterface, OutputTypeInterface,
    ASTNodeAwareInterface, DescriptionAwareInterface, FieldsAwareInterface
{
    use NameTrait;
    use DescriptionTrait;
    use FieldsTrait;
    use ResolveTrait;
    use ASTNodeTrait;
    use ExtensionASTNodesTrait;

    /**
     * @var callable
     */
    protected $isTypeOfCallback;

    /**
     * Interfaces can be defined either as an array or as a thunk.
     * Using thunks allows for cross-referencing of interfaces.
     *
     * @var array|callable
     */
    protected $interfacesOrThunk;

    /**
     * A list of interface instances.
     *
     * @var InterfaceType[]|null
     */
    protected $interfaces;

    /**
     * ObjectType constructor.
     *
     * @param string                        $name
     * @param null|string                   $description
     * @param array|callable                $fieldsOrThunk
     * @param array|callable                $interfacesOrThunk
     * @param callable|null                 $isTypeOfCallback
     * @param ObjectTypeDefinitionNode|null $astNode
     * @param ObjectTypeExtensionNode[]     $extensionASTNodes
     */
    public function __construct(
        string $name,
        ?string $description,
        $fieldsOrThunk,
        $interfacesOrThunk,
        ?callable $isTypeOfCallback,
        ?ObjectTypeDefinitionNode $astNode,
        array $extensionASTNodes
    ) {
        $this->name              = $name;
        $this->description       = $description;
        $this->rawFieldsOrThunk  = $fieldsOrThunk;
        $this->interfacesOrThunk = $interfacesOrThunk;
        $this->isTypeOfCallback  = $isTypeOfCallback;
        $this->astNode           = $astNode;
        $this->extensionAstNodes = $extensionASTNodes;
    }

    /**
     * @param mixed $value
     * @param mixed $context
     * @param mixed $info
     * @return bool|PromiseInterface
     */
    public function isTypeOf($value, $context, $info)
    {
        return null !== $this->isTypeOfCallback
            ? \call_user_func($this->isTypeOfCallback, $value, $context, $info)
            : false;
    }

    /**
     * @return bool
     * @throws InvariantException
     */
    public function hasInterfaces(): bool
    {
        return !empty($this->getInterfaces());
    }

    /**
     * @return InterfaceType[]
     * @throws InvariantException
     */
    public function getInterfaces(): array
    {
        if ($this->interfaces === null) {
            $this->interfaces = $this->buildInterfaces($this->interfacesOrThunk);
        }
        return $this->interfaces;
    }

    /**
     * @return bool
     */
    public function hasIsTypeOfCallback(): bool
    {
        return null !== $this->isTypeOfCallback;
    }

    /**
     * @return null|callable
     */
    public function getIsTypeOfCallback(): ?callable
    {
        return $this->isTypeOfCallback;
    }

    /**
     * @param array|callable $interfacesOrThunk
     * @return array
     * @throws InvariantException
     */
    protected function buildInterfaces($interfacesOrThunk): array
    {
        $interfaces = resolveThunk($interfacesOrThunk);

        if (!\is_array($interfaces)) {
            throw new InvariantException(
                \sprintf('%s interfaces must be an array or a function which returns an array.', $this->name)
            );
        }

        return $interfaces;
    }
}
