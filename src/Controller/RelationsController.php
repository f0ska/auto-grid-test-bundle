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
use F0ska\AutoGridTestBundle\Entity\BlogArticleCommentExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleTagExample;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auto-grid')]
final class RelationsController extends AbstractController
{
    #[Route('/relations', name: 'auto_grid_test_relations')]
    public function index(AutoGridFactory $factory): Response
    {
        $articles = $factory->create(BlogArticleExample::class);
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
}
