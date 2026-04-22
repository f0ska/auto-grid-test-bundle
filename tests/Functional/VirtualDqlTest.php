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

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VirtualDqlTest extends WebTestCase
{
    public function testVirtualDqlFieldsAreLoaded(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('th:contains("Comments Count")');
        $this->assertSelectorExists('th:contains("Author Articles")');
        $this->assertSelectorNotExists('form[name^="filter-commentsCount-"]');
        $this->assertSelectorNotExists('form[name^="filter-articlesCount-"]');

        $articlesTable = $this->findTableByHeader($crawler, 'Comments Count');
        $commentsCountValues = $this->extractColumnValues($articlesTable, 'Comments Count');
        $authorArticlesValues = $this->extractColumnValues($articlesTable, 'Author Articles');

        $this->assertNotEmpty($commentsCountValues);
        $this->assertNotEmpty($authorArticlesValues);
    }

    public function testVirtualDqlSorting(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $this->assertResponseIsSuccessful();

        $sortCommentsAscLink = $crawler
            ->filter('a[href*="agId=articles"][href*="agParams%5Border%5D%5BcommentsCount%5D=asc"]')
            ->first();
        $this->assertGreaterThan(0, $sortCommentsAscLink->count(), 'Comments Count sort link not found.');

        $crawler = $client->request('GET', $sortCommentsAscLink->link()->getUri());
        $this->assertResponseIsSuccessful();
        $commentsCountValues = $this->extractColumnValues($this->findTableByHeader($crawler, 'Comments Count'), 'Comments Count');
        $this->assertSame($commentsCountValues, $this->sortedValues($commentsCountValues, 'asc'));

        $crawler = $client->request('GET', '/auto-grid/relations');
        $this->assertResponseIsSuccessful();
        $sortAuthorArticlesAscLink = $crawler
            ->filter('a[href*="agId=articles"][href*="agParams%5Border%5D%5Bauthor:articlesCount%5D=asc"]')
            ->first();
        $this->assertGreaterThan(0, $sortAuthorArticlesAscLink->count(), 'Author Articles sort link not found.');

        $crawler = $client->request('GET', $sortAuthorArticlesAscLink->link()->getUri());
        $this->assertResponseIsSuccessful();
        $authorArticlesValues = $this->extractColumnValues($this->findTableByHeader($crawler, 'Author Articles'), 'Author Articles');
        $this->assertSame($authorArticlesValues, $this->sortedValues($authorArticlesValues, 'asc'));
    }

    private function findTableByHeader(Crawler $crawler, string $header): Crawler
    {
        foreach ($crawler->filter('table')->each(fn(Crawler $table): Crawler => $table) as $table) {
            if ($table->filter(sprintf('th:contains("%s")', $header))->count() > 0) {
                return $table;
            }
        }

        $this->fail(sprintf('Table with header "%s" not found.', $header));
    }

    /**
     * @return list<int>
     */
    private function extractColumnValues(Crawler $table, string $header): array
    {
        $columnIndex = $this->findColumnIndex($table, $header);
        $values = [];

        foreach ($table->filter('tbody tr')->each(fn(Crawler $row): Crawler => $row) as $row) {
            if ($row->filter('td')->count() <= $columnIndex) {
                continue;
            }
            $text = trim($row->filter('td')->eq($columnIndex)->text());
            $values[] = (int) $text;
        }

        return $values;
    }

    private function findColumnIndex(Crawler $table, string $header): int
    {
        foreach ($table->filter('thead th')->each(fn(Crawler $column): Crawler => $column) as $index => $column) {
            if (str_contains(trim($column->text()), $header)) {
                return $index;
            }
        }

        $this->fail(sprintf('Column "%s" not found.', $header));
    }

    /**
     * @param list<int> $values
     * @return list<int>
     */
    private function sortedValues(array $values, string $direction): array
    {
        $sorted = $values;

        if ($direction === 'asc') {
            sort($sorted);
            return $sorted;
        }

        rsort($sorted);
        return $sorted;
    }
}
