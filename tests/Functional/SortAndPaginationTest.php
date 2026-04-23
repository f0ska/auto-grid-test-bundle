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

use F0ska\AutoGridTestBundle\Entity\BasicExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SortAndPaginationTest extends WebTestCase
{
    public function testSortingById(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/');
        $this->assertResponseIsSuccessful();

        // 1. Sort by ID ASC
        $sortAscLink = $crawler->filter('a[href*="agAction=grid"][href*="agParams%5Border%5D%5Bid%5D=asc"]')->first();
        $this->assertGreaterThan(0, $sortAscLink->count(), 'Sort ASC link for ID not found.');
        $crawler = $client->click($sortAscLink->link());
        $this->assertResponseIsSuccessful();

        // Verify order (first item should have the lowest ID)
        $firstId = $crawler->filter('table tbody tr')->first()->filter('td')->first()->text();
        $this->assertEquals(1, (int)$firstId, 'Items not sorted by ID ASC correctly.');

        // 2. Sort by ID DESC
        $sortDescLink = $crawler->filter('a[href*="agAction=grid"][href*="agParams%5Border%5D%5Bid%5D=desc"]')->first();
        $this->assertGreaterThan(0, $sortDescLink->count(), 'Sort DESC link for ID not found.');
        $crawler = $client->click($sortDescLink->link());
        $this->assertResponseIsSuccessful();

        // Verify order (first item should have the highest ID)
        $highestId = $crawler->filter('table tbody tr')->first()->filter('td')->first()->text();
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $lastEntity = $entityManager->getRepository(BasicExample::class)->findOneBy([], ['id' => 'DESC']);
        $this->assertEquals($lastEntity->getId(), (int)$highestId, 'Items not sorted by ID DESC correctly.');
    }

    public function testPagination(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/');
        $this->assertResponseIsSuccessful();

        // 1. Click next page link
        $nextPageLink = $crawler->filter('a[href*="agAction=grid"][href*="agParams%5Bpage%5D=2"]')->first();
        $this->assertGreaterThan(0, $nextPageLink->count(), 'Next page link not found.');
        $crawler = $client->click($nextPageLink->link());
        $this->assertResponseIsSuccessful();

        // Verify URL contains page=2
        $this->assertStringContainsString('agParams%5Bpage%5D=2', $client->getRequest()->getUri());

        // 2. Change items per page
        $limit50Link = $crawler->filter('a[href*="agAction=grid"][href*="agParams%5Blimit%5D=50"]')->first();
        $this->assertGreaterThan(0, $limit50Link->count(), 'Limit 50 link not found.');
        $crawler = $client->click($limit50Link->link());
        $this->assertResponseIsSuccessful();

        // Verify 50 rows are displayed
        $this->assertCount(50, $crawler->filter('table tbody tr'), 'Did not find 50 rows after changing limit.');
    }

    public function testInvalidPaginationLimitFallsBackToDefault(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/?agId=basic-example&agAction=grid&agParams[limit]=999');
        $this->assertResponseIsSuccessful();

        $this->assertCount(
            10,
            $crawler->filter('table tbody tr'),
            'Invalid pagination limit should fall back to the default page size.'
        );
    }
}
