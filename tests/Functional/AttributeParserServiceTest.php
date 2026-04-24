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

use F0ska\AutoGridBundle\Service\AttributeParserService;
use F0ska\AutoGridTestBundle\Entity\AdvancedArticleExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use F0ska\AutoGridTestBundle\Entity\CorporateClientExample;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AttributeParserServiceTest extends KernelTestCase
{
    public function testParsesEntityFieldsetsAndDefaultSort(): void
    {
        $attributes = $this->parse(AdvancedArticleExample::class);
        $entityAttributes = $attributes->getEntityAttributes();
        $fieldAttributes = $attributes->getFieldAttributes();

        $this->assertArrayHasKey('content_info', $entityAttributes['fieldset']);
        $this->assertArrayHasKey('metatags', $entityAttributes['fieldset']);
        $this->assertArrayHasKey('full_content', $entityAttributes['fieldset']);

        $this->assertSame('Content Info', $entityAttributes['fieldset']['content_info']['name']);
        $this->assertSame('Metatags', $entityAttributes['fieldset']['metatags']['name']);
        $this->assertSame('Full Content', $entityAttributes['fieldset']['full_content']['name']);

        $this->assertContains('title', $entityAttributes['fieldset']['content_info']['fields']);
        $this->assertContains('author', $entityAttributes['fieldset']['content_info']['fields']);
        $this->assertContains('tags', $entityAttributes['fieldset']['metatags']['fields']);
        $this->assertContains('content', $entityAttributes['fieldset']['full_content']['fields']);

        $this->assertSame('profile_link.html.twig', basename($fieldAttributes['author']['view_template']));
        $this->assertSame('email', $fieldAttributes['author']['fields']['email']['name']);
        $this->assertSame('Author contact', $fieldAttributes['author']['fields']['email']['label']);
        $this->assertSame(5, $fieldAttributes['author']['fields']['email']['position']);
    }

    public function testParsesVirtualFieldsAndFilterableSortableFlags(): void
    {
        $attributes = $this->parse(BlogArticleExample::class);
        $entityAttributes = $attributes->getEntityAttributes();
        $fieldAttributes = $attributes->getFieldAttributes();

        $this->assertSame(['createdAt' => 'desc'], $entityAttributes['default_sort']);
        $this->assertContains('commentsCount', $attributes->getPureVirtualFieldNames());

        $this->assertTrue($fieldAttributes['title']['can_filter']);
        $this->assertTrue($fieldAttributes['title']['can_sort']);
        $this->assertFalse($fieldAttributes['content']['permission']['grid'] ?? false);

        $this->assertSame(
            'F0ska\\AutoGridBundle\\Condition\\AssociationCondition',
            $fieldAttributes['tags']['filterable']['condition']
        );
        $this->assertTrue($fieldAttributes['tags']['filterable']['form_options']['multiple']);

        $this->assertSame('username', $fieldAttributes['author']['fields']['username']['name']);
        $this->assertSame('Author', $fieldAttributes['author']['fields']['username']['label']);
        $this->assertArrayNotHasKey('articlesCount', $fieldAttributes['author']['fields']);

        $this->assertTrue($fieldAttributes['commentsCount']['virtual_column']['allowed']);
        $this->assertTrue($fieldAttributes['commentsCount']['can_sort']);
    }

    public function testParsesVirtualColumnWithoutDqlAsPureVirtualField(): void
    {
        $attributes = $this->parse(CorporateClientExample::class);
        $fieldAttributes = $attributes->getFieldAttributes();

        $this->assertContains('tax', $attributes->getPureVirtualFieldNames());
        $this->assertTrue($fieldAttributes['tax']['virtual_column']['allowed']);
        $this->assertSame(5, $fieldAttributes['tax']['position']);
        $this->assertSame('$ ', $fieldAttributes['tax']['value_prefix']);
        $this->assertSame(
            'F0ska\\AutoGridTestBundle\\View\\TaxViewServiceExample',
            $fieldAttributes['tax']['view_service']
        );
    }

    private function parse(string $entityClass)
    {
        self::bootKernel();

        /** @var AttributeParserService $parser */
        $parser = self::getContainer()->get(AttributeParserService::class);

        return $parser->parse($entityClass);
    }
}
