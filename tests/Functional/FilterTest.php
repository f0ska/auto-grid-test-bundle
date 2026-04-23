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

class FilterTest extends WebTestCase
{
    public function testTextFiltering(): void
    {
        $client = static::createClient();

        // 1. Get a name to search for from the database
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(BasicExample::class)->findOneBy([]);
        $this->assertNotNull($entity, 'No entities found in database. Did you load fixtures?');
        $searchName = $entity->getName();

        // 2. Load the basic grid
        $crawler = $client->request('GET', '/auto-grid/');
        $this->assertResponseIsSuccessful();

        // 3. Find the filter form for 'name'
        $form = $crawler->filter('form[name^="filter-name-"]')->form();

        // 4. Submit the filter
        $client->submit($form, [
            $form->getName() . '[name]' => $searchName,
        ]);

        if ($client->getResponse()->isRedirect()) {
            $client->followRedirect();
        }
        $this->assertResponseIsSuccessful();

        // 5. Verify the results
        $crawler = $client->getCrawler();
        $this->assertGreaterThan(0, $crawler->filter('table tbody tr:contains("' . $searchName . '")')->count());
    }

    public function testBooleanFiltering(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/auto-grid/');
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name^="filter-enabled-"]')->form();

        $client->submit($form, [
            $form->getName() . '[enabled]' => '0',
        ]);

        if ($client->getResponse()->isRedirect()) {
            $client->followRedirect();
        }
        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();
        $this->assertGreaterThan(0, $crawler->filter('table tbody tr')->count());
    }

    public function testIdFiltering(): void
    {
        $client = static::createClient();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(BasicExample::class)->findOneBy([]);
        $searchId = $entity->getId();

        $crawler = $client->request('GET', '/auto-grid/');
        $form = $crawler->filter('form[name^="filter-id-"]')->form();

        $client->submit($form, [
            $form->getName() . '[id]' => $searchId,
        ]);

        if ($client->getResponse()->isRedirect()) {
            $client->followRedirect();
        }
        $this->assertResponseIsSuccessful();

        $crawler = $client->getCrawler();
        $this->assertEquals(1, $crawler->filter('table tbody tr')->count());
    }
}
