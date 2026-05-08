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
use F0ska\AutoGridTestBundle\Entity\CorporateClientExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilterTest extends WebTestCase
{
    private const ERROR_SELECTOR = '.alert-danger, .callout.alert, .message.is-danger';

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

    public function testUnknownFilterFieldIsRejected(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/');
        $agId = $this->getAgIdFromFilterForm($crawler->filter('form[name^="filter-name-"]')->attr('action'));

        $client->request(
            'GET',
            sprintf('/auto-grid/?agId=%s&agAction=grid&agParams[filter][missingField]=value', $agId)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Invalid request parameter');
    }

    public function testExistingNonFilterableFieldIsRejected(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/');
        $agId = $this->getAgIdFromFilterForm($crawler->filter('form[name^="filter-name-"]')->attr('action'));

        $client->request(
            'GET',
            sprintf('/auto-grid/?agId=%s&agAction=grid&agParams[filter][description]=value', $agId)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Invalid request parameter');
    }

    public function testVisibleFilterMatchesAdditionalConfiguredField(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $needle = 'stage3b-' . uniqid() . '@example.test';
        $entity = (new CorporateClientExample())
            ->setName('Stage 3B Additional Field')
            ->setContactEmail($needle)
            ->setRevenue('100.00')
            ->setStatus('active')
            ->setLastAuditAt(new \DateTimeImmutable());

        $entityManager->persist($entity);
        $entityManager->flush();

        $crawler = $client->request('GET', '/auto-grid/corporate');
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->filter('form[name^="filter-contactEmail-"]')->count());

        $form = $crawler->filter('form[name^="filter-name-"]')->form();
        $client->submit($form, [
            $form->getName() . '[name]' => $needle,
        ]);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('table tbody tr:contains("Stage 3B Additional Field")')->count());
    }

    public function testAdditionalFilterFieldCombinesWithOtherFilters(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $needle = 'stage3b-and-' . uniqid() . '@example.test';
        $entity = (new CorporateClientExample())
            ->setName('Stage 3B Combined Filter')
            ->setContactEmail($needle)
            ->setRevenue('100.00')
            ->setStatus('active')
            ->setLastAuditAt(new \DateTimeImmutable());

        $entityManager->persist($entity);
        $entityManager->flush();

        $crawler = $client->request('GET', '/auto-grid/corporate');
        $agId = $this->getAgIdFromFilterForm($crawler->filter('form[name^="filter-name-"]')->attr('action'));

        $client->request(
            'GET',
            sprintf(
                '/auto-grid/corporate?agId=%s&agAction=grid&agParams[filter][name]=%s&agParams[filter][status]=inactive',
                $agId,
                urlencode($needle)
            )
        );
        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $client->getCrawler()->filter('table tbody tr:contains("Stage 3B Combined Filter")')->count());

        $client->request(
            'GET',
            sprintf(
                '/auto-grid/corporate?agId=%s&agAction=grid&agParams[filter][name]=%s&agParams[filter][status]=active',
                $agId,
                urlencode($needle)
            )
        );

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $client->getCrawler()->filter('table tbody tr:contains("Stage 3B Combined Filter")')->count());
    }

    private function getAgIdFromFilterForm(string $action): string
    {
        parse_str((string) parse_url($action, PHP_URL_QUERY), $query);

        $this->assertArrayHasKey('agId', $query);
        return (string) $query['agId'];
    }
}
