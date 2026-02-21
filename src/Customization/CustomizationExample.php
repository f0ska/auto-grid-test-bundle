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

namespace F0ska\AutoGridTestBundle\Customization;

use F0ska\AutoGridBundle\Customization\CustomizationInterface;
use F0ska\AutoGridBundle\Model\AutoGrid;
use F0ska\AutoGridBundle\Model\Parameters;

class CustomizationExample implements CustomizationInterface
{
    public function execute(AutoGrid $autoGrid, Parameters $parameters): void
    {
        if ($autoGrid->getId() === 'random-column-order') {
            $fields = [];
            $keys = array_keys($parameters->fields);
            shuffle($keys);
            foreach ($keys as $key) {
                $fields[$key] = $parameters->fields[$key];
            }
            $parameters->fields = $fields;

            $parameters->attributes['title'] = 'The order of the columns is random on each reload 😁';
        }
    }

    public static function getPriority(): int
    {
        return 0;
    }
}
