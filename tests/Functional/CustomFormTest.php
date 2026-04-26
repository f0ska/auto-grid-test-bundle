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

use F0ska\AutoGridTestBundle\Entity\CustomFormExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomFormTest extends WebTestCase
{
    public function testCustomFormUsage(): void
    {
        $client = static::createClient();
        $title = 'Custom Form Test ' . uniqid();
        $updatedTitle = $title . ' Updated';

        // 1. Load the custom form example
        $crawler = $client->request('GET', '/auto-grid/custom-form');
        $this->assertResponseIsSuccessful();

        // 2. Click 'Create' to see the custom form
        $createUrl = $crawler->filter('a[href*="agAction=create"]')->attr('href');
        $crawler = $client->request('GET', $createUrl);
        $this->assertResponseIsSuccessful();

        // 3. Verify that the custom form field 'file' is present
        $this->assertSelectorExists('input[type="file"]', 'Custom form should contain a file input.');

        // 4. Submit the form
        $form = $crawler->filter('form')->form();
        $client->submit($form, [
            $form->getName() . '[title]' => $title,
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // 5. Verify persistence and create event side effects
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(CustomFormExample::class)->findOneBy(['title' => $title]);
        $this->assertNotNull($entity);
        $this->assertSame('Prepared by create event', $entity->getNote());
        $entityId = $entity->getId();

        // 6. Filter the grid down to the new item and open the view page
        $crawler = $client->request('GET', '/auto-grid/custom-form');
        $filterForm = $crawler->filter('form[name^="filter-id-"]')->form();
        $client->submit($filterForm, [$filterForm->getName() . '[id]' => $entityId]);
        $client->followRedirect();
        $crawler = $client->getCrawler();

        $row = $crawler->filter(sprintf('tr:contains("%s")', $title));
        $this->assertGreaterThan(0, $row->count(), 'Created item not found in grid after filtering by ID.');

        $viewUrl = $row->filter('a[href*="agAction=view"]')->attr('href');
        $crawler = $client->request('GET', $viewUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', sprintf('Viewed "%s"', $title));
        $this->assertSelectorTextContains('body', 'Prepared by create event');

        // 7. Edit the item and verify the edit event updates the note
        $editUrl = $crawler->filter('a[href*="agAction=edit"]')->attr('href');
        $crawler = $client->request('GET', $editUrl);
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $client->submit($form, [
            $form->getName() . '[title]' => $updatedTitle,
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $entityManager->clear();
        $entity = $entityManager->getRepository(CustomFormExample::class)->find($entityId);
        $this->assertSame($updatedTitle, $entity->getTitle());
        $this->assertSame('Prepared by edit event', $entity->getNote());
    }
}
