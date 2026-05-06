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

use F0ska\AutoGridTestBundle\Entity\CustomActionExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class RowActionPermissionTest extends WebTestCase
{
    public function testEnabledRowsHideDeleteAndShowCustomAction(): void
    {
        $client = static::createClient();
        $name = 'Row Permission Enabled ' . bin2hex(random_bytes(4));
        $this->createCustomActionExample($name, true);

        $row = $this->requestCustomActionGridFilteredByName($client, $name)
            ->filter(sprintf('tr:contains("%s")', $name))
        ;

        $this->assertCount(0, $row->filter('button[name^="delete-"]'));
        $this->assertCount(1, $row->filter('.custom-action-btn'));
    }

    public function testDisabledRowsShowDeleteAndHideCustomAction(): void
    {
        $client = static::createClient();
        $name = 'Row Permission Disabled ' . bin2hex(random_bytes(4));
        $this->createCustomActionExample($name, false);

        $row = $this->requestCustomActionGridFilteredByName($client, $name)
            ->filter(sprintf('tr:contains("%s")', $name))
        ;

        $this->assertCount(1, $row->filter('button[name^="delete-"]'));
        $this->assertCount(0, $row->filter('.custom-action-btn'));
    }

    public function testExistingGeneralPermissionDenialStillWins(): void
    {
        $client = static::createClient();
        $client->request('GET', '/auto-grid/advanced');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('button[name^="delete-"]');
    }

    private function createCustomActionExample(string $name, bool $enabled): CustomActionExample
    {
        $entity = (new CustomActionExample())
            ->setName($name)
            ->setDescription($name)
            ->setEnabled($enabled)
            ->setPublishAt(new \DateTimeImmutable('2026-01-01 10:00:00'))
        ;

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();
        $entityManager->clear();

        return $entity;
    }

    private function requestCustomActionGridFilteredByName($client, string $name): Crawler
    {
        $crawler = $client->request('GET', '/auto-grid/custom-action');
        $filterForm = $crawler->filter('form[name^="filter-name-"]')->form();
        $client->submit($filterForm, [$filterForm->getName() . '[name]' => $name]);
        $client->followRedirect();

        return $client->getCrawler();
    }
}
