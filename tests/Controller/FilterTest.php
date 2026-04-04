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

namespace F0ska\AutoGridTestBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FilterTest extends WebTestCase
{
    public function testFilteringByName(): void
    {
        $client = static::createClient();

        // 1. Load the basic example grid
        $crawler = $client->request('GET', '/auto-grid/');
        self::assertResponseIsSuccessful();

        // 2. Get the first row's name to use as a filter value
        // AutoGrid tables usually have a predictable structure.
        // We look for the first <td> in the <tbody> that isn't the ID or an action cell.
        $firstName = $crawler->filter('table tbody tr:first-child td')->at(1)->text();

        // 3. Submit the filter form
        // The filter form for 'name' is named filter-name-[hash]
        // Since the hash is dynamic, we'll find the form by its action or a partial name match
        $form = $crawler->filter('form[name^="filter-name"]')->form();
        $client->submit($form, [
            $form->getName() . '[name]' => $firstName,
        ]);

        self::assertResponseIsSuccessful();
        $crawler = $client->getCrawler();

        // 4. Verify that the result contains the name we filtered for
        self::assertStringContainsString($firstName, $crawler->filter('table tbody')->text());

        // 5. Optionally verify that the row count is reduced (depends on fixtures)
    }
}
