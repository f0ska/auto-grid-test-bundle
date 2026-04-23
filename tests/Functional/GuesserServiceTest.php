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

use F0ska\AutoGridBundle\Condition\ContainsCondition;
use F0ska\AutoGridBundle\Condition\RangeCondition;
use F0ska\AutoGridBundle\Model\FieldParameter;
use F0ska\AutoGridBundle\Service\GuesserService;
use F0ska\AutoGridBundle\Service\MetaDataService;
use F0ska\AutoGridBundle\Service\ParametersService;
use F0ska\AutoGridTestBundle\Config\ExampleEnum;
use F0ska\AutoGridTestBundle\Entity\ArrayObjectTypesExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use F0ska\AutoGridTestBundle\Entity\DateTimeTypesExample;
use F0ska\AutoGridTestBundle\Entity\OtherTypesExample;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class GuesserServiceTest extends KernelTestCase
{
    public function testGuessesEnumFieldType(): void
    {
        [$guesser, $metaData] = $this->services();
        $field = $this->createScalarField($metaData, OtherTypesExample::class, 'enumType');

        $guesser->guessFieldFormType($field, $field->agId);

        $this->assertSame(EnumType::class, $field->attributes['form']['type']);
        $this->assertSame(ExampleEnum::class, $field->attributes['form']['options']['class']);
    }

    public function testGuessesDateFieldAsSingleTextAndRangeFilter(): void
    {
        [$guesser, $metaData] = $this->services();
        $field = $this->createScalarField($metaData, DateTimeTypesExample::class, 'datetimeType');
        $field->canFilter = true;

        $guesser->guessFieldFormType($field, $field->agId);
        $guesser->guessFilterCondition($field);

        $this->assertSame(DateTimeType::class, $field->attributes['form']['type']);
        $this->assertSame('single_text', $field->attributes['form']['options']['widget']);
        $this->assertSame(RangeCondition::class, $field->filterCondition);
    }

    public function testGuessesJsonAndSimpleArrayTransformers(): void
    {
        [$guesser, $metaData] = $this->services();

        $jsonField = $this->createScalarField($metaData, ArrayObjectTypesExample::class, 'jsonType');
        $jsonField->canFilter = true;
        $guesser->guessFieldFormType($jsonField, $jsonField->agId);
        $guesser->guessFilterCondition($jsonField);

        $this->assertSame(TextareaType::class, $jsonField->attributes['form']['type']);
        $this->assertSame(5, $jsonField->attributes['form']['options']['attr']['rows']);
        $this->assertSame(ContainsCondition::class, $jsonField->filterCondition);
        $this->assertSame(
            ['x' => 1],
            $jsonField->attributes['form']['transformer']->reverseTransform("{\"x\":1}")
        );

        $arrayField = $this->createScalarField($metaData, ArrayObjectTypesExample::class, 'simpleArray');
        $arrayField->canFilter = true;
        $guesser->guessFieldFormType($arrayField, $arrayField->agId);
        $guesser->guessFilterCondition($arrayField);

        $this->assertSame(TextareaType::class, $arrayField->attributes['form']['type']);
        $this->assertSame(5, $arrayField->attributes['form']['options']['attr']['rows']);
        $this->assertSame(ContainsCondition::class, $arrayField->filterCondition);
        $this->assertSame(
            ['a', 'b'],
            $arrayField->attributes['form']['transformer']->reverseTransform("a\nb")
        );
    }

    public function testBinaryAndBlobFieldsAreNotEditableSortableOrFilterable(): void
    {
        [$guesser, $metaData] = $this->services();

        $binaryField = $this->createScalarField($metaData, ArrayObjectTypesExample::class, 'binaryType');
        $binaryField->canFilter = true;
        $binaryField->canSort = true;
        $guesser->guessFieldFormType($binaryField, $binaryField->agId);

        $this->assertFalse($binaryField->canEdit);
        $this->assertFalse($binaryField->canFilter);
        $this->assertFalse($binaryField->canSort);

        $blobField = $this->createScalarField($metaData, ArrayObjectTypesExample::class, 'blobType');
        $blobField->canFilter = true;
        $blobField->canSort = true;
        $guesser->guessFieldFormType($blobField, $blobField->agId);

        $this->assertFalse($blobField->canEdit);
        $this->assertFalse($blobField->canFilter);
        $this->assertFalse($blobField->canSort);
    }

    public function testGuessesAssociatedFieldChoiceLabel(): void
    {
        [$guesser, $metaData] = $this->services();
        $agId = $metaData->add(BlogArticleExample::class, 'guesser-associated');
        $metadata = $metaData->getMetadata($agId);
        $agSubId = $metaData->add($metadata->getAssociationTargetClass('author'), 'guesser-associated-sub', true);

        $field = new FieldParameter([
            'name' => 'author',
            'mappingType' => ParametersService::MAPPING_ASSOC,
            'agId' => $agId,
            'agSubId' => $agSubId,
        ]);
        $field->associationMapping = $metadata->getAssociationMapping('author');

        $guesser->guessAssociatedFormType($field);

        $this->assertSame(EntityType::class, $field->attributes['form']['type']);
        $this->assertSame(
            $field->associationMapping->targetEntity,
            $field->attributes['form']['options']['class']
        );
        $this->assertSame('username', $field->attributes['form']['options']['choice_label']);
    }

    private function services(): array
    {
        self::bootKernel();

        return [
            self::getContainer()->get(GuesserService::class),
            self::getContainer()->get(MetaDataService::class),
        ];
    }

    private function createScalarField(MetaDataService $metaData, string $entityClass, string $fieldName): FieldParameter
    {
        $agId = $metaData->add($entityClass, sprintf('guesser-%s-%s', str_replace('\\', '-', $entityClass), $fieldName));
        $metadata = $metaData->getMetadata($agId);

        $field = new FieldParameter([
            'name' => $fieldName,
            'mappingType' => ParametersService::MAPPING_FIELD,
            'agId' => $agId,
        ]);
        $field->fieldMapping = $metadata->getFieldMapping($fieldName);

        return $field;
    }
}
