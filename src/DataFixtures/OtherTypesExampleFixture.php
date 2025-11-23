<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace F0ska\AutoGridTestBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use F0ska\AutoGridTestBundle\Config\ExampleEnum;
use F0ska\AutoGridTestBundle\Entity\OtherTypesExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

/* Symfony7 only *
use DateTimeInterface;
use Symfony\Component\Clock\DatePoint;
*/

class OtherTypesExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager); // @phpstan-ignore argument.type
        $populator->addEntity(
            OtherTypesExample::class,
            100,
            [
                'enumType' => function () use ($generator) {
                    return $generator->randomElement([ExampleEnum::Red, ExampleEnum::Green, ExampleEnum::Blue]);
                },
                'decimalType' => function () use ($generator) {
                    return $generator->randomFloat(2, 0, 99999);
                },
                'guidType' => function () use ($generator) {
                    return $generator->uuid();
                },
                /* Symfony7 only *
                'datePointType' => function () use ($generator) {
                    return new DatePoint($generator->dateTime()->format(DateTimeInterface::ATOM));
                },
                */
            ]
        );
        $populator->execute();
    }
}
