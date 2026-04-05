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

use F0ska\AutoGridTestBundle\Entity\CustomActionExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomActionRouteTest extends WebTestCase
{
    public function testCustomActionRoutes(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid/custom-action');
        $this->assertResponseIsSuccessful();

        // CustomActionExample has: #[ActionRoute('create')]
        // and controller sets routePrefix: 'auto_grid_test_custom_action_'
        // So 'create' action should point to 'auto_grid_test_custom_action_create'

        $createUrl = $crawler->filter('a[href*="custom-action-create"]')->attr('href');
        $this->assertStringContainsString('custom-action-create', $createUrl);

        // Test clicking the create link
        $client->request('GET', $createUrl);
        $this->assertTrue($client->getResponse()->isRedirect('/auto-grid/custom-action'));
        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Greetings from the "CREATE" action');
    }
}
