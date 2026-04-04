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

final class SmokeTest extends WebTestCase
{
    /**
     * @dataProvider routeProvider
     */
    public function testPagesLoadSuccessfully(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful(sprintf('The URL "%s" failed to load.', $url));
    }

    public static function routeProvider(): iterable
    {
        yield ['/auto-grid/'];
        yield ['/auto-grid/types'];
        yield ['/auto-grid/relations'];
        yield ['/auto-grid/advanced-1'];
        yield ['/auto-grid/advanced-2'];
        yield ['/auto-grid/custom-action'];
        yield ['/auto-grid/custom-form'];
        yield ['/auto-grid/corporate'];
        yield ['/auto-grid/customization-service'];
    }
}
