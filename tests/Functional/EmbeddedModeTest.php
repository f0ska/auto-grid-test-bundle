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

namespace F0ska\AutoGridTestBundle\Tests\Functional;

use F0ska\AutoGridTestBundle\Entity\BlogArticleCommentExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmbeddedModeTest extends WebTestCase
{
    public function testUserProfileUsesEmbeddedViewWithShellTabs(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Blog User Example', $crawler->filter('.autogrid-demo-item')->first()->text());

        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');
        $this->assertStringContainsString('/relations/users/', $profileUrl);

        $crawler = $client->request('GET', $profileUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'User profile:');
        $this->assertSelectorTextContains('.autogrid-info-block', 'Embedded tabbed profile:');
        $this->assertSelectorTextContains('nav[aria-label="User profile sections"]', 'Profile');
        $this->assertSelectorTextContains('nav[aria-label="User profile sections"]', 'Articles');
        $this->assertSelectorTextContains('nav[aria-label="User profile sections"]', 'Comments');

        $this->assertSame(0, $crawler->filter('.autogrid a[href*="agAction=create"]')->count());
        $this->assertSame(0, $crawler->filter('.autogrid a[href="#autogrid"]')->count());
    }

    public function testEmbeddedGridKeepsRowActionsButMovesCreateButtonToShell(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');

        $crawler = $client->request('GET', $profileUrl);
        $articlesUrl = $crawler->selectLink('Articles')->link()->getUri();

        $crawler = $client->request('GET', $articlesUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'New article');

        $this->assertSame(0, $crawler->filter('.autogrid a[href*="agAction=create"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('.autogrid a[href*="agAction=view"]')->count());
        $this->assertSame(0, $crawler->filter('form[name^="filter-author-"]')->count());

        $articleViewUrl = $crawler->filter('.autogrid a[href*="agAction=view"]')->first()->attr('href');
        $crawler = $client->request('GET', $articleViewUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Back to articles');
    }

    public function testEmbeddedEditFormCanBeSubmittedFromShell(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');

        $crawler = $client->request('GET', $profileUrl);
        $editUrl = $crawler->selectLink('Edit profile')->link()->getUri();

        $crawler = $client->request('GET', $editUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->filter('button[form="form-user-profile-edit"]')->count());
        $this->assertSame(1, $crawler->filter('form[id="form-user-profile-edit"]')->count());
    }

    public function testEmbeddedSubgridCreateFormCanBeSubmittedFromShell(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');

        $crawler = $client->request('GET', $profileUrl);
        $articlesUrl = $crawler->selectLink('Articles')->link()->getUri();

        $crawler = $client->request('GET', $articlesUrl);
        $createUrl = $crawler->selectLink('New article')->link()->getUri();

        $crawler = $client->request('GET', $createUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Back to articles');
        $this->assertSame(1, $crawler->filter('button[form="form-user-profile-articles"]')->count());
        $this->assertSame(1, $crawler->filter('form[id="form-user-profile-articles"]')->count());
        $this->assertSame(0, $crawler->filter('#form-user-profile-articles_author')->count());
    }

    public function testEmbeddedArticleCreateUsesProfileUserAsAuthor(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');

        preg_match('#/relations/users/(\d+)/view#', $profileUrl, $matches);
        $userId = (int) $matches[1];

        $crawler = $client->request('GET', $profileUrl);
        $articlesUrl = $crawler->selectLink('Articles')->link()->getUri();

        $crawler = $client->request('GET', $articlesUrl);
        $createUrl = $crawler->selectLink('New article')->link()->getUri();

        $crawler = $client->request('GET', $createUrl);
        $form = $crawler->filter('form[id="form-user-profile-articles"]')->form();
        $title = 'Embedded Article ' . uniqid();

        $client->submit($form, [
            $form->getName() . '[title]' => $title,
            $form->getName() . '[content]' => 'Created from embedded profile context.',
            $form->getName() . '[published]' => '1',
        ]);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->clear();
        $article = $entityManager->getRepository(BlogArticleExample::class)->findOneBy(['title' => $title]);

        $this->assertNotNull($article);
        $this->assertSame($userId, $article->getAuthor()?->getId());
    }

    public function testEmbeddedCommentCreateUsesProfileUserAsAuthor(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');

        preg_match('#/relations/users/(\d+)/view#', $profileUrl, $matches);
        $userId = (int) $matches[1];

        $crawler = $client->request('GET', $profileUrl);
        $commentsUrl = $crawler->selectLink('Comments')->link()->getUri();

        $crawler = $client->request('GET', $commentsUrl);
        $createUrl = $crawler->selectLink('New comment')->link()->getUri();

        $crawler = $client->request('GET', $createUrl);
        $this->assertSame(0, $crawler->filter('#form-user-profile-comments_author')->count());

        $form = $crawler->filter('form[id="form-user-profile-comments"]')->form();
        $articleValue = $crawler->filter('select[name="' . $form->getName() . '[article]"] option')->eq(1)->attr('value');
        $comment = 'Embedded comment ' . uniqid();

        $client->submit($form, [
            $form->getName() . '[article]' => $articleValue,
            $form->getName() . '[comment]' => $comment,
        ]);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->clear();
        $createdComment = $entityManager->getRepository(BlogArticleCommentExample::class)->findOneBy(['comment' => $comment]);

        $this->assertNotNull($createdComment);
        $this->assertSame($userId, $createdComment->getAuthor()?->getId());
    }

    public function testTabbedProfilePreservesSelectedTheme(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid-bulma/relations');
        $this->assertResponseIsSuccessful();

        $profileUrl = $crawler->filter('a[href*="/relations/users/"][href*="/view"]')->first()->attr('href');
        $this->assertStringContainsString('/auto-grid-bulma/relations/users/', $profileUrl);

        $crawler = $client->request('GET', $profileUrl);
        $articlesUrl = $crawler->selectLink('Articles')->link()->getUri();
        $this->assertStringContainsString('/auto-grid-bulma/relations/users/', $articlesUrl);
    }
}
