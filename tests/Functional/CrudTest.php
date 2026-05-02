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

class CrudTest extends WebTestCase
{
    public function testFullCrudCycle(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/');
        $this->assertResponseIsSuccessful();

        // 1. CREATE
        $createUrl = $crawler->filter('a[href*="agAction=create"]')->first()->attr('href');
        $crawler = $client->request('GET', $createUrl);
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $client->submit($form, [
            $form->getName() . '[name]' => 'CRUD Test Item',
            $form->getName() . '[enabled]' => '1',
            $form->getName() . '[publishAt]' => '2025-01-01 10:00:00',
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Verify in DB
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(BasicExample::class)->findOneBy(['name' => 'CRUD Test Item']);
        $this->assertNotNull($entity, 'Entity was not found in DB after creation.');
        $entityId = $entity->getId();

        // 2. FILTER & VIEW
        // Since there are 10k rows, we must filter to find our item
        $crawler = $client->request('GET', '/auto-grid/');
        $filterForm = $crawler->filter('form[name^="filter-id-"]')->form();
        $client->submit($filterForm, [$filterForm->getName() . '[id]' => $entityId]);
        $client->followRedirect();
        $crawler = $client->getCrawler();

        $row = $crawler->filter('tr:contains("CRUD Test Item")');
        $this->assertGreaterThan(0, $row->count(), 'Item not found in grid even after filtering by ID.');

        $viewUrl = $row->filter('a[href*="agAction=view"]')->attr('href');
        $crawler = $client->request('GET', $viewUrl);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'CRUD Test Item');

        // 3. EDIT
        $editUrl = $crawler->filter('a[href*="agAction=edit"]')->attr('href');
        $crawler = $client->request('GET', $editUrl);
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();
        $client->submit($form, [
            $form->getName() . '[name]' => 'CRUD Test Item UPDATED',
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $entityManager->clear();
        $entity = $entityManager->getRepository(BasicExample::class)->find($entityId);
        $this->assertEquals('CRUD Test Item UPDATED', $entity->getName());

        // 4. DELETE
        // Filter again for the updated name
        $crawler = $client->request('GET', '/auto-grid/');
        $filterForm = $crawler->filter('form[name^="filter-id-"]')->form();
        $client->submit($filterForm, [$filterForm->getName() . '[id]' => $entityId]);
        $client->followRedirect();
        $crawler = $client->getCrawler();

        $row = $crawler->filter('tr:contains("CRUD Test Item UPDATED")');
        $client->request('GET', sprintf('/auto-grid/?agId=basic-example&agAction=delete&agParams[id]=%d', $entityId));
        $this->assertResponseIsSuccessful();

        $entityManager->clear();
        $entity = $entityManager->getRepository(BasicExample::class)->find($entityId);
        $this->assertNotNull($entity, 'Entity should not be deleted by a GET delete request.');

        $crawler = $client->request('GET', '/auto-grid/');
        $filterForm = $crawler->filter('form[name^="filter-id-"]')->form();
        $client->submit($filterForm, [$filterForm->getName() . '[id]' => $entityId]);
        $client->followRedirect();
        $crawler = $client->getCrawler();

        $row = $crawler->filter('tr:contains("CRUD Test Item UPDATED")');
        $deleteForm = $crawler->filter('form[name^="delete-"]')->first();
        $deleteFormName = $deleteForm->attr('name');
        $deleteButton = $row->filter(sprintf('button[name="%s[id]"]', $deleteFormName))->first();
        $this->assertGreaterThan(0, $deleteButton->count(), 'Delete button was not rendered.');

        $client->request('POST', $deleteForm->attr('action'), [
            $deleteFormName => [
                '_token' => 'invalid-token',
                'id' => $deleteButton->attr('value'),
            ],
        ]);
        $this->assertResponseIsSuccessful();

        $entityManager->clear();
        $entity = $entityManager->getRepository(BasicExample::class)->find($entityId);
        $this->assertNotNull($entity, 'Entity should not be deleted when CSRF token is invalid.');

        $crawler = $client->request('GET', '/auto-grid/');
        $filterForm = $crawler->filter('form[name^="filter-id-"]')->form();
        $client->submit($filterForm, [$filterForm->getName() . '[id]' => $entityId]);
        $client->followRedirect();
        $crawler = $client->getCrawler();

        $row = $crawler->filter('tr:contains("CRUD Test Item UPDATED")');
        $deleteForm = $crawler->filter('form[name^="delete-"]')->first();
        $deleteFormName = $deleteForm->attr('name');
        $deleteButton = $row->filter(sprintf('button[name="%s[id]"]', $deleteFormName))->first();
        $deleteToken = $deleteForm->filter(sprintf('input[name="%s[_token]"]', $deleteFormName))->attr('value');

        $client->request('POST', $deleteForm->attr('action'), [
            $deleteFormName => [
                '_token' => $deleteToken,
                'id' => $deleteButton->attr('value'),
            ],
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        $entityManager->clear();
        $entity = $entityManager->getRepository(BasicExample::class)->find($entityId);
        $this->assertNull($entity);
    }
}
