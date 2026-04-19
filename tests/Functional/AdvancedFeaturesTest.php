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

use F0ska\AutoGridTestBundle\Entity\AdvancedArticleExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvancedFeaturesTest extends WebTestCase
{
    public function testFieldsetsInView(): void
    {
        $client = static::createClient();

        // 1. Get an entity to view
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(AdvancedArticleExample::class)->findOneBy([]);
        $this->assertNotNull($entity);

        // 2. Load the detail view (which also uses fieldsets)
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $viewUrl = $crawler->filter('tr:contains("' . $entity->getTitle() . '") a[href*="agAction=view"]')->attr('href');
        $crawler = $client->request('GET', $viewUrl);
        $this->assertResponseIsSuccessful();

        // 3. Verify Fieldsets (rendered as cards or headers)
        $this->assertSelectorTextContains('body', 'Content Info');
        $this->assertSelectorTextContains('body', 'Metatags');
        $this->assertSelectorTextContains('body', 'Full Content');
    }

    public function testValueDecoration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $this->assertResponseIsSuccessful();

        // 1. Verify ValuePrefix ("#") on ID column
        // Now that ID is explicitly allowed, it should be visible
        $this->assertGreaterThan(0, $crawler->filter('td:contains("#")')->count(), 'ValuePrefix "#" not found in grid.');
    }

    public function testAssociatedFieldValues(): void
    {
        $client = static::createClient();

        // 1. Get an entity with an author email to verify
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(AdvancedArticleExample::class)->findOneBy([]);
        $authorEmail = $entity->getAuthor()->getEmail();

        // 2. Load grid
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $this->assertResponseIsSuccessful();

        // 3. Verify that the author's email (from AssociatedField) is visible in the row
        $this->assertGreaterThan(0, $crawler->filter('tr:contains("' . $entity->getTitle() . '"):contains("' . $authorEmail . '")')->count(), 'AssociatedField value (email) not found in row.');
    }
}
