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

class BaseTemplateSlotsTest extends WebTestCase
{
    public function testBaseTemplateBeforeAndAfterSlotsRenderWithAutogridContext(): void
    {
        $client = static::createClient();

        $client->request('GET', '/auto-grid/custom-form');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            '.autogrid-slot-before',
            'Before Slot'
        );
        $this->assertSelectorTextContains(
            '.autogrid-slot-before',
            'Custom Form Example'
        );
        $this->assertSelectorTextContains(
            '.autogrid-slot-before',
            'my-custom-form-example'
        );
        $this->assertSelectorTextContains(
            '.autogrid-slot-after',
            'After Slot'
        );
        $this->assertSelectorTextContains(
            '.autogrid-slot-after',
            'Custom Form Example'
        );
        $this->assertSelectorTextContains(
            '.autogrid-slot-after',
            'my-custom-form-example'
        );
    }
}
