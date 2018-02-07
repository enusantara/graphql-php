<?php

namespace Digia\GraphQL\Type\Definition;

use Digia\GraphQL\Language\AST\Node\NodeInterface;
use Digia\GraphQL\Language\AST\KindEnum;

class FloatType extends AbstractScalarType
{

    /**
     * @inheritdoc
     */
    protected function configure(): array
    {
        return [
            'name'        => TypeEnum::FLOAT,
            'description' =>
                'The `Float` scalar type represents signed double-precision fractional ' .
                'values as specified by ' .
                '[IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).',
        ];
    }

    /**
     * @inheritdoc
     */
    public function serialize($value)
    {
        return $this->coerceValue($value);
    }

    /**
     * @inheritdoc
     */
    public function parseValue($value)
    {
        return $this->coerceValue($value);
    }

    /**
     * @inheritdoc
     */
    public function parseLiteral(NodeInterface $astNode, ...$args)
    {
        return in_array($astNode->getKind(), [KindEnum::FLOAT, KindEnum::INT]) ? $astNode->getValue() : null;
    }

    /**
     * @param $value
     * @return float
     * @throws \TypeError
     */
    private function coerceValue($value): float
    {
        if ($value === '') {
            throw new \TypeError('Float cannot represent non numeric value: (empty string)');
        }

        if (is_numeric($value)) {
            return (float)$value;
        }

        throw new \TypeError('Float cannot represent non numeric value');
    }
}