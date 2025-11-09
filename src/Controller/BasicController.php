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

use F0ska\AutoGridBundle\Factory\AutoGridFactory;
use F0ska\AutoGridTestBundle\Entity\BasicExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auto-grid')]
final class BasicController extends AbstractController
{
    #[Route('/', name: 'auto_grid_test_basic')]
    public function index(AutoGridFactory $factory): Response
    {
        $grid = $factory->create(BasicExample::class);
        return $grid->getResponse() ?? $this->render(
            '@F0skaAutoGridTest/examples/basic.html.twig',
            ['grid' => $grid]
        );
    }
}
