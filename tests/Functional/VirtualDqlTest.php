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

class VirtualDqlTest extends WebTestCase
{
    public function testVirtualDqlFieldsAreLoaded(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');

        $this->assertResponseIsSuccessful();
        
        // Check if "Comments Count" column exists
        $this->assertSelectorExists('th:contains("Comments Count")');
        
        // Check if "Author Articles" column exists
        $this->assertSelectorExists('th:contains("Author Articles")');

        // Verify that the values are hydrated (assuming fixtures have some data)
        // We look for a cell under "Comments Count"
        $commentsCountCell = $crawler->filter('table')->first()->filter('td')->eq(5); // Adjust index if needed
        $this->assertNotEmpty($commentsCountCell->text());
    }

    public function testVirtualDqlSorting(): void
    {
        $client = static::createClient();
        
        // Sort by comments count ascending
        $client->request('GET', '/auto-grid/relations', ['articles' => ['order' => ['commentsCount' => 'asc']]]);
        $this->assertResponseIsSuccessful();
        
        // Sort by comments count descending
        $client->request('GET', '/auto-grid/relations', ['articles' => ['order' => ['commentsCount' => 'desc']]]);
        $this->assertResponseIsSuccessful();
    }

    public function testVirtualDqlFiltering(): void
    {
        $client = static::createClient();
        
        // Filter by comments count
        $client->request('GET', '/auto-grid/relations', ['articles' => ['filter' => ['commentsCount' => '0']]]);
        $this->assertResponseIsSuccessful();
        
        // Filter by range on articlesCount (Virtual DQL field)
        // RangeCondition expects 'min' and 'max' keys if correctly configured by GuesserService
        $client->request('GET', '/auto-grid/relations', ['users' => ['filter' => ['articlesCount' => ['min' => '0', 'max' => '10']]]]);
        $this->assertResponseIsSuccessful();
    }
}
