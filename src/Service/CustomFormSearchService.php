<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace F0ska\AutoGridTestBundle\Service;

use Doctrine\ORM\QueryBuilder;
use F0ska\AutoGridBundle\Model\Parameters;
use F0ska\AutoGridBundle\Search\SearchServiceInterface;
use F0ska\AutoGridBundle\Service\QueryFieldResolver;

class CustomFormSearchService implements SearchServiceInterface
{
    public static int $calls = 0;

    public function __construct(private readonly QueryFieldResolver $fieldResolver)
    {
    }

    public function apply(QueryBuilder $builder, string $term, array $fields, Parameters $parameters): void
    {
        self::$calls++;

        $isNegated = str_starts_with(strtolower($term), 'not ');
        $searchTerm = $isNegated ? trim(substr($term, 4)) : $term;

        if ($searchTerm === '') {
            return;
        }

        $expressions = [];
        foreach ($fields as $index => $field) {
            $parameter = 'custom_search_' . $index;
            $operator = $isNegated ? 'NOT LIKE' : 'LIKE';
            $expressions[] = sprintf(
                "COALESCE(%s, '') %s :%s",
                $this->fieldResolver->resolve($builder, $field),
                $operator,
                $parameter
            );
            $builder->setParameter($parameter, '%' . $searchTerm . '%');
        }

        if ($expressions === []) {
            return;
        }

        $builder->andWhere(
            $isNegated ? $builder->expr()->andX(...$expressions) : $builder->expr()->orX(...$expressions)
        );
    }
}
