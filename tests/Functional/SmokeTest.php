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

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    #[DataProvider('demoRoutesProvider')]
    public function testDemoRoutesLoadSuccessfully(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful(sprintf('URL "%s" failed to load.', $url));
    }

    public static function demoRoutesProvider(): array
    {
        return [
            ['/auto-grid/'],
            ['/auto-grid/types'],
            ['/auto-grid/relations'],
            ['/auto-grid/advanced-1'],
            ['/auto-grid/advanced-2'],
            ['/auto-grid/custom-action'],
            ['/auto-grid/custom-form'],
            ['/auto-grid/corporate'],
            ['/auto-grid/customization-service'],
        ];
    }
}
