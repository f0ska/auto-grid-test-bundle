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

use F0ska\AutoGridTestBundle\Entity\CorporateClientExample;
use F0ska\AutoGridTestBundle\Entity\CustomFormExample;
use F0ska\AutoGridTestBundle\Service\CustomFormSearchService;
use F0ska\AutoGridBundle\ActionParameter\SearchParameter;
use F0ska\AutoGridBundle\Model\Parameters;
use F0ska\AutoGridBundle\Service\ParametersService;
use F0ska\AutoGridBundle\Service\QueryFieldResolver;
use F0ska\AutoGridBundle\Service\RowActionPermissionService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class SearchTest extends WebTestCase
{
    private const ERROR_SELECTOR = '.alert-danger, .callout.alert, .message.is-danger';

    public function testSearchFormRendersForSearchableEntity(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/corporate');

        $this->assertResponseIsSuccessful();
        $this->assertSame(1, $crawler->filter('form[name^="search-"]')->count());
        $this->assertSame('1', $crawler->filter('form[name^="search-"] input[name$="[term]"]')->attr('minlength'));
        $this->assertSame('255', $crawler->filter('form[name^="search-"] input[name$="[term]"]')->attr('maxlength'));
    }

    public function testSearchFormDoesNotRenderWhenSearchActionIsDenied(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/advanced');

        $this->assertResponseIsSuccessful();
        $this->assertSame(0, $crawler->filter('form[name^="search-"]')->count());
    }

    public function testSearchActionRedirectsToGridWithNormalizedTerm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/corporate');
        $form = $crawler->filter('form[name^="search-"]')->form();

        $client->submit($form, [$form->getName() . '[term]' => '  invoice  ']);

        $this->assertTrue($client->getResponse()->isRedirect());
        $location = $client->getResponse()->headers->get('Location') ?? '';
        $this->assertStringContainsString('agAction=grid', $location);
        $this->assertStringContainsString('agParams%5Bsearch%5D%5Bterm%5D=invoice', $location);
    }

    public function testEmptySearchRemovesSearchState(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/corporate');
        $agId = $this->getAgIdFromForm($crawler, 'form[name^="search-"]');

        $crawler = $client->request(
            'GET',
            sprintf('/auto-grid/corporate?agId=%s&agAction=grid&agParams[search][term]=invoice', $agId)
        );
        $form = $crawler->filter('form[name^="search-"]')->form();
        $client->submit($form, [$form->getName() . '[term]' => '']);

        $this->assertTrue($client->getResponse()->isRedirect());
        $location = $client->getResponse()->headers->get('Location') ?? '';
        $this->assertStringNotContainsString('search', $location);
    }

    public function testSearchResetsPageAndPreservesFilterOrderAndLimit(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/corporate');
        $agId = $this->getAgIdFromForm($crawler, 'form[name^="search-"]');

        $crawler = $client->request(
            'GET',
            sprintf(
                '/auto-grid/corporate?agId=%s&agAction=grid&agParams[filter][status]=active&agParams[order][name]=desc&agParams[limit]=50&agParams[page]=2',
                $agId
            )
        );
        $form = $crawler->filter('form[name^="search-"]')->form();
        $client->submit($form, [$form->getName() . '[term]' => 'abc']);

        $this->assertTrue($client->getResponse()->isRedirect());
        $location = $client->getResponse()->headers->get('Location') ?? '';
        $this->assertStringContainsString('filter', $location);
        $this->assertStringContainsString('order', $location);
        $this->assertStringContainsString('limit', $location);
        $this->assertStringNotContainsString('page', $location);
    }

    public function testDefaultSearchReturnsRowsMatchingAnyConfiguredField(): void
    {
        $client = static::createClient();
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $name = 'Search ' . bin2hex(random_bytes(4));
        $entity = (new CorporateClientExample())
            ->setName($name)
            ->setContactEmail($name . '@example.test')
            ->setRevenue('100.00')
            ->setStatus('active')
            ->setLastAuditAt(new \DateTimeImmutable());

        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->clear();

        $crawler = $client->request('GET', '/auto-grid/corporate');
        $form = $crawler->filter('form[name^="search-"]')->form();

        $client->submit($form, [$form->getName() . '[term]' => $name]);
        $crawler = $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('table tbody tr:contains("' . $name . '")')->count());
    }

    public function testInvalidSearchParamsAreRejected(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/corporate');
        $agId = $this->getAgIdFromForm($crawler, 'form[name^="search-"]');

        $client->request(
            'GET',
            sprintf('/auto-grid/corporate?agId=%s&agAction=grid&agParams[search]=bad', $agId)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Invalid request parameter');
    }

    public function testInvalidSearchFormShowsValidationError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/custom-form');
        $form = $crawler->filter('form[name^="search-"]')->form();

        $client->submit($form, [$form->getName() . '[term]' => 'ab']);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'This value is too short');
    }

    public function testSearchTermLengthUsesMultibyteCharacters(): void
    {
        $term = str_repeat('ї', 255);
        $parameter = new SearchParameter();

        $this->assertSame(['term' => $term], $parameter->normalize(
            ['term' => $term],
            $this->createParameters(
                [
                    'searchable' => [
                        'fields' => ['name'],
                        'min_length' => 1,
                        'max_length' => 255,
                    ],
                ],
                ['search' => true]
            )
        ));
    }

    public function testSearchPermissionBlocksDirectAction(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/advanced');
        $agId = $this->getAgIdFromForm($crawler, 'form[name^="filter-"]');

        $client->request(
            'GET',
            sprintf('/auto-grid/advanced?agId=%s&agAction=search', $agId)
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Not Allowed');
    }

    public function testCustomSearchServiceIsCalledAndSupportsNegatedTerms(): void
    {
        CustomFormSearchService::$calls = 0;
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $needle = 'custom-negated-search-' . uniqid();

        $titleMatch = (new CustomFormExample())
            ->setTitle('Title ' . $needle)
            ->setStatus(CustomFormExample::STATUS_NEW);
        $noteMatch = (new CustomFormExample())
            ->setTitle('Note match control')
            ->setStatus(CustomFormExample::STATUS_PENDING)
            ->setNote('Note ' . $needle);
        $kept = (new CustomFormExample())
            ->setTitle('Kept custom search control')
            ->setStatus(CustomFormExample::STATUS_APPROVED)
            ->setNote(null);

        $entityManager->persist($titleMatch);
        $entityManager->persist($noteMatch);
        $entityManager->persist($kept);
        $entityManager->flush();
        $searchService = new CustomFormSearchService(new QueryFieldResolver());

        $normalBuilder = $entityManager->createQueryBuilder()
            ->select('entity')
            ->from(CustomFormExample::class, 'entity');
        $searchService->apply($normalBuilder, $needle, ['title', 'note'], $this->createParameters());
        $normalIds = array_map(static fn (CustomFormExample $entity): int => (int) $entity->getId(), $normalBuilder->getQuery()->getResult());

        $negatedBuilder = $entityManager->createQueryBuilder()
            ->select('entity')
            ->from(CustomFormExample::class, 'entity');
        $searchService->apply($negatedBuilder, 'not ' . $needle, ['title', 'note'], $this->createParameters());
        $negatedIds = array_map(static fn (CustomFormExample $entity): int => (int) $entity->getId(), $negatedBuilder->getQuery()->getResult());

        $this->assertGreaterThan(0, CustomFormSearchService::$calls);
        $this->assertContains($titleMatch->getId(), $normalIds);
        $this->assertContains($noteMatch->getId(), $normalIds);
        $this->assertNotContains($kept->getId(), $normalIds);
        $this->assertNotContains($titleMatch->getId(), $negatedIds);
        $this->assertNotContains($noteMatch->getId(), $negatedIds);
        $this->assertContains($kept->getId(), $negatedIds);
    }

    private function createParameters(array $attributes = [], array $permissions = []): Parameters
    {
        return new Parameters(
            [
                'agId' => 'test',
                'action' => 'grid',
                'route' => [],
                'permissions' => $permissions,
                'request' => [],
                'attributes' => $attributes,
                'query' => [],
            ],
            $this->createMock(ParametersService::class),
            $this->createMock(RowActionPermissionService::class)
        );
    }

    private function getAgIdFromForm(Crawler $crawler, string $selector): string
    {
        $action = $crawler->filter($selector)->first()->attr('action');
        parse_str((string) parse_url((string) $action, PHP_URL_QUERY), $query);

        $this->assertArrayHasKey('agId', $query);
        return (string) $query['agId'];
    }
}
