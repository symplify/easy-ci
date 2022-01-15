<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\Astral\NodeValue;

use EasyCI20220115\PHPStan\Type\ConstantScalarType;
use EasyCI20220115\PHPStan\Type\UnionType;
final class UnionTypeValueResolver
{
    /**
     * @return mixed[]
     */
    public function resolveConstantTypes(\EasyCI20220115\PHPStan\Type\UnionType $unionType) : array
    {
        $resolvedValues = [];
        foreach ($unionType->getTypes() as $unionedType) {
            if (!$unionedType instanceof \EasyCI20220115\PHPStan\Type\ConstantScalarType) {
                continue;
            }
            $resolvedValues[] = $unionedType->getValue();
        }
        return $resolvedValues;
    }
}