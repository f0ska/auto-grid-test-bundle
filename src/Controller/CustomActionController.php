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
use F0ska\AutoGridTestBundle\Entity\CustomActionExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomActionController extends AbstractController
{
    #[Route('/custom-action', name: 'auto_grid_test_custom_action')]
    public function index(AutoGridFactory $factory): Response
    {
        $grid = $factory->create(
            CustomActionExample::class,
            gridId: 'custom-action',
            routePrefix: 'auto_grid_test_custom_action_'
        );
        return $grid->getResponse() ?? $this->render(
            '@F0skaAutoGridTest/examples/custom_action.html.twig',
            ['grid' => $grid]
        );
    }

    #[Route('/custom-action-create', name: 'auto_grid_test_custom_action_create')]
    public function create(): Response
    {
        $this->addFlash('success', 'Greetings from the "CREATE" action');
        return $this->redirectToRoute('auto_grid_test_custom_action');
    }

    #[Route('/custom-action-edit/{id}', name: 'auto_grid_test_custom_action_edit')]
    public function edit(int $id): Response
    {
        $this->addFlash('info', "Greetings from the \"EDIT\" action #$id");
        return $this->redirectToRoute('auto_grid_test_custom_action');
    }

    #[Route('/custom-action-view/{id}', name: 'auto_grid_test_custom_action_view')]
    public function view(int $id): Response
    {
        $this->addFlash('primary', "Greetings from the \"VIEW\" action #$id");
        return $this->redirectToRoute('auto_grid_test_custom_action');
    }

    #[Route('/custom-action-delete', name: 'auto_grid_test_custom_action_delete', methods: ['POST'])]
    public function delete(FormFactoryInterface $formFactory, Request $request): Response
    {
        $form = $formFactory->createNamedBuilder('delete-custom-action')
            ->setMethod('POST')
            ->add('id', HiddenType::class, ['constraints' => [new NotBlank()]])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $id = (int) $form->get('id')->getData();
            $this->addFlash('danger', "Greetings from the \"DELETE\" action #$id");
        }
        return $this->redirectToRoute('auto_grid_test_custom_action');
    }

    #[Route('/custom-action-download/{hash<[0-9a-f]{40}>}', name: 'auto_grid_test_custom_action_download', methods: ['GET'])]
    public function download(string $hash): Response
    {
        $file = '/tmp/' . $hash;
        if (!is_file($file) || !is_readable($file)) {
            throw new NotFoundHttpException('File not found');
        }
        return $this->file($file, 'export.csv');
    }
}
