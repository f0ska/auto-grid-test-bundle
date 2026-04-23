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

use F0ska\AutoGridTestBundle\Entity\CustomFormExample;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomFormTest extends WebTestCase
{
    public function testCustomFormUsage(): void
    {
        $client = static::createClient();

        // 1. Load the custom form example
        $crawler = $client->request('GET', '/auto-grid/custom-form');
        $this->assertResponseIsSuccessful();

        // 2. Click 'Create' to see the custom form
        $createUrl = $crawler->filter('a[href*="agAction=create"]')->attr('href');
        $crawler = $client->request('GET', $createUrl);
        $this->assertResponseIsSuccessful();

        // 3. Verify that the custom form field 'file' is present
        // (CustomFormExampleType should have a 'file' field)
        $this->assertSelectorExists('input[type="file"]', 'Custom form should contain a file input.');

        // 4. Submit the form
        $form = $crawler->filter('form')->form();
        $client->submit($form, [
            $form->getName() . '[title]' => 'Custom Form Test',
        ]);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // 5. Verify persistence
        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $entity = $entityManager->getRepository(CustomFormExample::class)->findOneBy(['title' => 'Custom Form Test']);
        $this->assertNotNull($entity);
    }
}
