<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Language\AST\KindEnum;
use Digia\GraphQL\Language\AST\Node\Behavior\NameTrait;
use Digia\GraphQL\Language\AST\Node\Behavior\ValueTrait;
use Digia\GraphQL\Language\AST\Node\Contract\NodeInterface;

class ObjectFieldNode extends AbstractNode implements NodeInterface
{

    use NameTrait;
    use ValueTrait;

    /**
     * @var string
     */
    protected $kind = KindEnum::OBJECT_FIELD;
}
