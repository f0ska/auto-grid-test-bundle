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

use Doctrine\ORM\EntityManagerInterface;
use F0ska\AutoGridBundle\Factory\AutoGridFactory;
use F0ska\AutoGridBundle\Model\AutoGrid;
use F0ska\AutoGridBundle\ValueObject\AutoGridMode;
use F0ska\AutoGridTestBundle\Entity\BlogArticleCommentExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleTagExample;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class RelationsController extends AbstractController
{
    #[Route('/relations', name: 'auto_grid_test_relations')]
    public function index(AutoGridFactory $factory): Response
    {
        $articles = $factory->create(BlogArticleExample::class, gridId: 'articles');
        $tags = $factory->create(BlogArticleTagExample::class);
        $comments = $factory->create(BlogArticleCommentExample::class);
        $users = $factory->create(BlogUserExample::class);

        if ($articles->getResponse()) {
            return $articles->getResponse();
        }

        if ($tags->getResponse()) {
            return $tags->getResponse();
        }

        if ($comments->getResponse()) {
            return $comments->getResponse();
        }

        if ($users->getResponse()) {
            return $users->getResponse();
        }

        return $this->render(
            '@F0skaAutoGridTest/examples/relations.html.twig',
            [
                'articles' => $articles,
                'tags' => $tags,
                'comments' => $comments,
                'users' => $users,
            ]
        );
    }

    #[Route('/relations/users/{id}/view', name: 'auto_grid_test_relations_user_view')]
    public function userView(int $id, AutoGridFactory $factory, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserEntity($id, $entityManager);
        $grid = $factory->create(
            BlogUserExample::class,
            gridId: 'user-profile-view',
            initialAction: 'view',
            initialParameters: ['id' => $id],
            mode: AutoGridMode::Embedded
        );

        return $this->renderUserProfile($user, 'view', $grid);
    }

    #[Route('/relations/users/{id}/edit', name: 'auto_grid_test_relations_user_edit')]
    public function userEdit(int $id, AutoGridFactory $factory, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserEntity($id, $entityManager);
        $grid = $factory->create(
            BlogUserExample::class,
            gridId: 'user-profile-edit',
            initialAction: 'edit',
            initialParameters: ['id' => $id],
            mode: AutoGridMode::Embedded
        );

        if ($grid->getResponse()) {
            return $grid->getResponse();
        }

        return $this->renderUserProfile($user, 'view', $grid, 'form-user-profile-edit');
    }

    #[Route('/relations/users/{id}/articles', name: 'auto_grid_test_relations_user_articles')]
    public function userArticles(int $id, AutoGridFactory $factory, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserEntity($id, $entityManager);
        $grid = $factory->create(
            BlogArticleExample::class,
            gridId: 'user-profile-articles',
            mode: AutoGridMode::Embedded,
            context: ['author' => $user]
        );

        if ($grid->getResponse()) {
            return $grid->getResponse();
        }

        return $this->renderUserProfile($user, 'articles', $grid);
    }

    #[Route('/relations/users/{id}/comments', name: 'auto_grid_test_relations_user_comments')]
    public function userComments(int $id, AutoGridFactory $factory, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserEntity($id, $entityManager);
        $grid = $factory->create(
            BlogArticleCommentExample::class,
            gridId: 'user-profile-comments',
            mode: AutoGridMode::Embedded,
            context: ['author' => $user]
        );

        if ($grid->getResponse()) {
            return $grid->getResponse();
        }

        return $this->renderUserProfile($user, 'comments', $grid);
    }

    private function getUserEntity(int $id, EntityManagerInterface $entityManager): BlogUserExample
    {
        $user = $entityManager->getRepository(BlogUserExample::class)->find($id);
        if (!$user instanceof BlogUserExample) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }

    private function renderUserProfile(
        BlogUserExample $user,
        string $activeTab,
        AutoGrid $grid,
        ?string $formId = null
    ): Response {
        return $this->render(
            '@F0skaAutoGridTest/examples/user_profile.html.twig',
            [
                'user' => $user,
                'activeTab' => $activeTab,
                'grid' => $grid,
                'formId' => $formId,
            ]
        );
    }
}
