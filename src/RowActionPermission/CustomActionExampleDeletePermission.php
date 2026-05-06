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

namespace F0ska\AutoGridTestBundle\RowActionPermission;

use F0ska\AutoGridBundle\RowActionPermission\RowActionPermissionInterface;
use F0ska\AutoGridBundle\Model\Parameters;
use F0ska\AutoGridTestBundle\Entity\CustomActionExample;

final class CustomActionExampleDeletePermission implements RowActionPermissionInterface
{
    public function isGranted(string $action, object $entity, Parameters $parameters): bool
    {
        return $entity instanceof CustomActionExample && $entity->isEnabled();
    }
}
