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

use F0ska\AutoGridTestBundle\Entity\AdvancedArticleExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvancedFilterTest extends WebTestCase
{
    public function testAdvancedFiltering(): void
    {
        $client = static::createClient();

        // 1. Load grid with Advanced Filter enabled
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $this->assertResponseIsSuccessful();

        // 2. Find the advanced filter form (starting with 'filter-')
        $forms = $crawler->filter('form')->reduce(function ($node) {
            return preg_match('/^filter-[a-z0-9]{31}$/', $node->attr('name') ?? '');
        });

        $this->assertGreaterThan(0, $forms->count(), 'Advanced filter form not found.');
        $form = $forms->first()->form();
        $formName = $forms->first()->attr('name');

        // 3. Get search criteria from DB
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(AdvancedArticleExample::class)->findOneBy([]);
        $searchTitle = $entity->getTitle();

        // 4. Submit advanced filter
        $client->submit($form, [
            $formName . '[title]' => $searchTitle,
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // 5. Verify results
        $crawler = $client->getCrawler();
        $this->assertGreaterThan(0, $crawler->filter('table tbody tr:contains("' . $searchTitle . '")')->count());
    }
}
