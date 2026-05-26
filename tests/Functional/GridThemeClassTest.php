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

use F0ska\AutoGridBundle\Model\FieldParameter;
use F0ska\AutoGridBundle\Twig\Extension as AutoGridExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GridThemeClassTest extends WebTestCase
{
    #[DataProvider('gridTableClassProvider')]
    public function testThemeGridTableKeepsFixedUxClasses(string $url, array $expectedClasses): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();

        $tableClass = $crawler->filter('table')->first()->attr('class') ?? '';

        foreach ($expectedClasses as $expectedClass) {
            $this->assertStringContainsString($expectedClass, $tableClass);
        }
    }

    public static function gridTableClassProvider(): array
    {
        return [
            'bootstrap 5' => ['/auto-grid/corporate', ['table', 'table-hover', 'table-striped', 'table-sm']],
            'bootstrap 4' => ['/auto-grid-bootstrap4/corporate', ['table', 'table-hover', 'table-striped', 'table-sm']],
            'bulma' => ['/auto-grid-bulma/corporate', ['table', 'is-fullwidth', 'is-hoverable', 'is-striped', 'is-narrow']],
            'foundation' => ['/auto-grid-foundation/corporate', ['hover', 'table-sm']],
            'flowbite' => ['/auto-grid-flowbite/corporate', ['w-full', 'text-sm', 'text-left', 'text-gray-500', 'table-sm', 'is-narrow']],
        ];
    }

    public function testFlowbiteGridRowsKeepFixedUxClasses(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/auto-grid-flowbite/corporate');

        $this->assertResponseIsSuccessful();

        $rowClass = $crawler->filter('table > tbody > tr')->first()->attr('class') ?? '';

        $this->assertStringContainsString('odd:bg-white', $rowClass);
        $this->assertStringContainsString('even:bg-gray-50', $rowClass);
        $this->assertStringContainsString('hover:bg-gray-100', $rowClass);
    }

    public function testColumnHtmlClassHelperCanAppendOrOverrideThemeDefaults(): void
    {
        static::createClient();

        $extension = static::getContainer()->get(AutoGridExtension::class);
        $field = new FieldParameter([
            'attributes' => [
                'column_html_class' => [
                    'column' => 'w-25',
                    'header' => 'text-end',
                ],
            ],
        ]);

        $this->assertSame('align-middle w-25 text-end', $extension->agColumnClass($field, 'header', 'align-middle'));

        $field->attributes['column_html_class']['override'] = true;

        $this->assertSame('w-25 text-end', $extension->agColumnClass($field, 'header', 'align-middle'));
    }
}
