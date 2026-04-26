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

class CustomizationServiceTest extends WebTestCase
{
    public function testCustomizationServiceImpact(): void
    {
        $client = static::createClient();

        // 1. Load the customization service demo page
        $crawler = $client->request('GET', '/auto-grid/customization-service');
        $this->assertResponseIsSuccessful();

        // 2. Verify that the customization service changed the title
        // In CustomizationExample.php: $parameters->attributes['title'] = 'The order of the columns is random...'
        $this->assertSelectorTextContains('.autogrid', 'The order of the columns is random');
    }
}
