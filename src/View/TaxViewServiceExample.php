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

namespace F0ska\AutoGridTestBundle\View;

use F0ska\AutoGridBundle\Model\FieldParameter;
use F0ska\AutoGridBundle\View\ViewServiceInterface;

class TaxViewServiceExample implements ViewServiceInterface
{
    public function prepare(object $entity, FieldParameter $field): array
    {
        $value = (float) $entity->getRevenue();
        $result = number_format($value * 0.2, 2, '.', '');
        return [
            'value' => $result,
        ];
    }
}
