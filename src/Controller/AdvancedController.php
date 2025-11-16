<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace F0ska\AutoGridTestBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use F0ska\AutoGridBundle\Factory\AutoGridFactory;
use F0ska\AutoGridTestBundle\Entity\AdvancedArticleExample;
use F0ska\AutoGridTestBundle\Entity\AdvancedUserExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auto-grid')]
final class AdvancedController extends AbstractController
{
    #[Route('/advanced-1/{tagId}', name: 'auto_grid_test_advanced_1')]
    public function one(AutoGridFactory $factory, ?int $tagId = null): Response
    {
        $params = $tagId ? ['filter' => ['tags' => $tagId]] : [];
        $grid = $factory->create(AdvancedArticleExample::class, initialParameters: $params);
        return $grid->getResponse() ?? $this->render(
            '@F0skaAutoGridTest/examples/advanced_1.html.twig',
            ['grid' => $grid]
        );
    }

    #[Route('/advanced-2/{id}', name: 'auto_grid_test_advanced_2')]
    public function two(AutoGridFactory $factory, ?int $id = null): Response
    {
        $action = 'create';
        $params = [];
        if ($id) {
            $action = 'view';
            $params['id'] = $id;
        }

        $where = 'advancedUserExample.banned = :banned';
        $bind = new ArrayCollection([new Parameter('banned', 0)]);

        $grid = $factory->create(
            entityClass: AdvancedUserExample::class,
            gridId: 'advanced2',
            queryExpression: $where,
            queryParameters: $bind,
            initialAction: $action,
            initialParameters: $params
        );

        return $grid->getResponse() ?? $this->render(
            '@F0skaAutoGridTest/examples/advanced_2.html.twig',
            ['grid' => $grid]
        );
    }
}
