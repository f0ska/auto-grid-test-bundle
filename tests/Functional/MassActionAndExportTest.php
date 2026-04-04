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

use F0ska\AutoGridTestBundle\Entity\CustomActionExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MassActionAndExportTest extends WebTestCase
{
    public function testMassAction(): void
    {
        $client = static::createClient();

        // 1. Load the custom action grid
        $crawler = $client->request('GET', '/auto-grid/custom-action');
        $this->assertResponseIsSuccessful();

        // 2. Find the mass action form and form name
        $massActionFormElement = $crawler->filter('form')->reduce(function ($node) {
            return str_starts_with($node->attr('name') ?? '', 'mass-');
        })->first();
        $this->assertGreaterThan(0, $massActionFormElement->count(), 'Mass action form not found.');
        $formName = $massActionFormElement->attr('name');

        // 3. Select items
        $checkboxes = $crawler->filter('input[type="checkbox"][name="' . $formName . '[ids][]"]');
        $this->assertGreaterThanOrEqual(2, $checkboxes->count(), 'Not enough items to perform mass action.');

        $selectedIds = [];
        for ($i = 0; $i < 2; $i++) {
            $selectedIds[] = $checkboxes->eq($i)->attr('value');
        }

        // 4. Find the button for "Custom Mass Action" and click it
        $button = $crawler->filter('button[value="Custom Mass Action"]');
        if ($button->count() === 0) {
             $button = $crawler->filter('button:contains("Custom Mass Action")');
        }

        $client->submit($button->first()->form(), [
            $formName . '[ids]' => $selectedIds,
        ]);

        // 5. Follow redirect and verify flash message (Subscriber uses 'warning' type)
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-warning');
        // The code is normalized to snake_case in the subscriber message
        $this->assertSelectorTextContains('.alert-warning', 'Greetings from the "custom_mass_action"');
    }

    public function testExportAction(): void
    {
        $client = static::createClient();

        // 1. Load the custom action grid
        $crawler = $client->request('GET', '/auto-grid/custom-action');
        $this->assertResponseIsSuccessful();

        // 2. Find the export button
        $exportButton = $crawler->filter('button[value="export_example"]');
        if ($exportButton->count() === 0) {
            $exportButton = $crawler->filter('button:contains("Export Action Example")');
        }

        // 3. Click the export button
        $client->submit($exportButton->first()->form());

        // 4. Follow redirect to the download URL
        $this->assertTrue($client->getResponse()->isRedirect(), 'Export action did not redirect.');
        $client->followRedirect();

        // 5. Verify the response is a file download
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertResponseHeaderSame('Content-Disposition', 'attachment; filename=export.csv');
    }
}
