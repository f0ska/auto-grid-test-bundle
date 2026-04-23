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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ErrorFlowTest extends WebTestCase
{
    private const ERROR_SELECTOR = '.alert-danger, .callout.alert, .message.is-danger';

    public function testInvalidActionShowsGridErrorState(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/auto-grid/customization-service?agId=random-column-order&agAction=missing_action'
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Unknown Action');
    }

    public function testInvalidIdParameterShowsGridErrorState(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/auto-grid/customization-service?agId=random-column-order&agAction=view&agParams[id]=bad'
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Invalid request parameter');
    }

    public function testCustomizationCanThrowControlledUserMessage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/auto-grid/customization-service-error');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Customization says no');
    }

    public function testMissingEntityShowsGridNotFoundMessage(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/auto-grid/customization-service?agId=random-column-order&agAction=view&agParams[id]=999999999'
        );

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(self::ERROR_SELECTOR, 'Not found');
    }
}
