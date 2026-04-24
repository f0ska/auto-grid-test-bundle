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

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VirtualDqlTest extends WebTestCase
{
    public function testVirtualDqlFieldsAreLoaded(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('th:contains("Comments")');
        $this->assertSelectorNotExists('form[name^="filter-commentsCount-"]');

        $articlesTable = $this->findTableByHeader($crawler, 'Comments');
        $commentsCountValues = $this->extractColumnValues($articlesTable, 'Comments');

        $this->assertNotEmpty($commentsCountValues);
    }

    public function testVirtualDqlSorting(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $this->assertResponseIsSuccessful();

        $sortCommentsAscLink = $crawler
            ->filter('a[href*="agId=articles"][href*="agParams%5Border%5D%5BcommentsCount%5D=asc"]')
            ->first();
        $this->assertGreaterThan(0, $sortCommentsAscLink->count(), 'Comments sort link not found.');

        $crawler = $client->request('GET', $sortCommentsAscLink->link()->getUri());
        $this->assertResponseIsSuccessful();
        $commentsCountValues = $this->extractColumnValues($this->findTableByHeader($crawler, 'Comments'), 'Comments');
        $this->assertSame($commentsCountValues, $this->sortedValues($commentsCountValues, 'asc'));
    }

    public function testArticleGridColumnOrderPreservesAssociatedSubfieldPositions(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/relations');
        $this->assertResponseIsSuccessful();

        $articlesTable = $this->findTableByHeader($crawler, 'Comments');
        $headers = $this->extractHeaders($articlesTable);

        $this->assertLessThan(
            $this->findHeaderIndex($headers, 'comments'),
            $this->findHeaderIndex($headers, 'title'),
            'Comments should appear immediately after Title for a more natural scan order.'
        );

        $this->assertFalse(
            $this->hasHeaderExactly($headers, 'author email'),
            'Author Email should stay hidden in the articles grid because of grid-specific permissions.'
        );

        $this->assertFalse(
            $this->hasHeaderExactly($headers, 'id'),
            'ID should stay hidden in the articles grid.'
        );

        $this->assertFalse(
            $this->hasHeaderExactly($headers, 'created at'),
            'Created at should stay hidden in the articles grid.'
        );

        $this->assertFalse(
            $this->hasHeaderExactly($headers, 'author posts'),
            'Author Posts should stay hidden in the articles grid.'
        );

        $this->assertGreaterThan(
            $this->findHeaderIndex($headers, 'author'),
            $this->findHeaderIndex($headers, 'published'),
            'Published should be placed after the content-related columns.'
        );
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
        return $this->findHeaderIndex($this->extractHeaders($table), strtolower($header));
    }

    /**
     * @return list<string>
     */
    private function extractHeaders(Crawler $table): array
    {
        return $table
            ->filter('thead th')
            ->each(fn(Crawler $column): string => strtolower(trim($column->text())));
    }

    /**
     * @param list<string> $headers
     */
    private function findHeaderIndex(array $headers, string $needle): int
    {
        foreach ($headers as $index => $header) {
            if (str_contains($header, strtolower($needle))) {
                return $index;
            }
        }

        $this->fail(sprintf('Column "%s" not found.', $needle));
    }

    /**
     * @param list<string> $headers
     */
    private function hasHeader(array $headers, string $needle): bool
    {
        foreach ($headers as $header) {
            if (str_contains($header, strtolower($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $headers
     */
    private function hasHeaderExactly(array $headers, string $needle): bool
    {
        return in_array(strtolower($needle), $headers, true);
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
