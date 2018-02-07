<?php

namespace Digia\GraphQL\Type\Definition;

class IDType extends StringType
{

    /**
     * @var string
     */
    protected $name = TypeEnum::ID;

    /**
     * @var string
     */
    protected $description =
        'The `ID` scalar type represents a unique identifier, often used to ' .
        'refetch an object or as key for a cache. The ID type appears in a JSON ' .
        'response as a String; however, it is not intended to be human-readable. ' .
        'When expected as an input type, any string (such as `"4"`) or integer ' .
        '(such as `4`) input value will be accepted as an ID.';
}
