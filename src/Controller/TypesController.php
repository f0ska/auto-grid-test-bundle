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
use F0ska\AutoGridTestBundle\Entity\ArrayObjectTypesExample;
use F0ska\AutoGridTestBundle\Entity\DateTimeTypesExample;
use F0ska\AutoGridTestBundle\Entity\MainTypesExample;
use F0ska\AutoGridTestBundle\Entity\OtherTypesExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auto-grid')]
final class TypesController extends AbstractController
{
    #[Route('/types', name: 'auto_grid_test_types')]
    public function index(AutoGridFactory $factory): Response
    {
        $main = $factory->create(MainTypesExample::class);
        $array = $factory->create(ArrayObjectTypesExample::class);
        $dates = $factory->create(DateTimeTypesExample::class);
        $other = $factory->create(OtherTypesExample::class);

        if ($main->getResponse()) {
            return $main->getResponse();
        }

        if ($array->getResponse()) {
            return $array->getResponse();
        }

        if ($dates->getResponse()) {
            return $dates->getResponse();
        }

        if ($other->getResponse()) {
            return $other->getResponse();
        }

        return $this->render(
            '@F0skaAutoGridTest/examples/types.html.twig',
            [
                'main' => $main,
                'array' => $array,
                'dates' => $dates,
                'other' => $other,
            ]
        );
    }
}
