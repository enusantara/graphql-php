<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Language\AST\KindEnum;

class ObjectTypeDefinitionNode implements NodeInterface
{

    use KindTrait;

    /**
     * @inheritdoc
     */
    protected function configure(): array
    {
        return [
            'kind' => KindEnum::OBJECT_TYPE_DEFINITION,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        // TODO: Implement getValue() method.
    }
}