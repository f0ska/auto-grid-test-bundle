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

use F0ska\AutoGridBundle\Factory\AutoGridFactory;
use F0ska\AutoGridTestBundle\Entity\BasicExample;
use F0ska\AutoGridTestBundle\Entity\CorporateClientExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

    #[Route('/corporate', name: 'auto_grid_test_corporate')]
    public function corporate(AutoGridFactory $factory): Response
    {
        $grid = $factory->create(CorporateClientExample::class);
        return $grid->getResponse() ?? $this->render(
            '@F0skaAutoGridTest/examples/corporate.html.twig',
            ['grid' => $grid]
        );
    }
}
