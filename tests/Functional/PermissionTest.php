<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace F0ska\AutoGridTestBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PermissionTest extends WebTestCase
{
    public function testActionsAreDisallowedByDefault(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $this->assertResponseIsSuccessful();

        // AdvancedArticleExample uses DisallowActionsByDefault
        $this->assertSelectorNotExists('a[href*="agAction=create"]', 'Create button should be disallowed.');
        $this->assertSelectorNotExists('a[href*="agAction=delete"]', 'Delete button should be disallowed.');
    }

    public function testFieldsAreDisallowedByDefault(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $this->assertResponseIsSuccessful();

        // Check for specific columns that SHOULD be visible
        $headers = $crawler->filter('th')->each(fn($node) => strtolower(trim($node->text())));

        $this->assertContains('title', $headers);
        $this->assertContains('content', $headers);
        $this->assertContains('created at', $headers);
        $this->assertContains('author', $headers);
        $this->assertContains('tags', $headers);
    }

    public function testSpecificFieldPermission(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $this->assertResponseIsSuccessful();

        // AdvancedArticleExample has: #[Permission('grid', allow: false)] on 'published' and 'updatedAt'
        $headers = $crawler->filter('th')->each(fn($node) => strtolower(trim($node->text())));
        $this->assertNotContains('published', $headers, 'Published column should be hidden in grid.');
        $this->assertNotContains('updated at', $headers, 'Updated At column should be hidden in grid.');
    }
}
