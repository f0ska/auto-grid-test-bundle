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

namespace F0ska\AutoGridTestBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use F0ska\AutoGridBundle\Factory\AutoGridFactory;
use F0ska\AutoGridTestBundle\Entity\AdvancedArticleExample;
use F0ska\AutoGridTestBundle\Entity\AdvancedUserExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdvancedController extends AbstractController
{
    #[Route('/advanced/{userId}', name: 'auto_grid_test_advanced', defaults: ['userId' => null])]
    public function index(AutoGridFactory $factory, ?int $userId = null): Response
    {
        $grid1 = $factory->create(AdvancedArticleExample::class);

        $action = 'create';
        $params = [];
        if ($userId !== null) {
            $action = 'view';
            $params['id'] = $userId;
        }

        $where = 'advancedUserExample.banned = :banned';
        $bind = new ArrayCollection([new Parameter('banned', 0)]);
        $grid2 = $factory->create(
            entityClass: AdvancedUserExample::class,
            gridId: 'advanced2',
            queryExpression: $where,
            queryParameters: $bind,
            initialAction: $action,
            initialParameters: $params
        );

        if ($grid1->getResponse()) {
            return $grid1->getResponse();
        }

        if ($grid2->getResponse()) {
            return $grid2->getResponse();
        }

        return $this->render('@F0skaAutoGridTest/examples/advanced.html.twig', [
            'grid1' => $grid1,
            'grid2' => $grid2
        ]);
    }
}
