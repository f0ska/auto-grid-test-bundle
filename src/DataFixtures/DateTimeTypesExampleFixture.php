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

use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use F0ska\AutoGridTestBundle\Entity\DateTimeTypesExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class DateTimeTypesExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager);
        $populator->addEntity(
            DateTimeTypesExample::class,
            100,
            [
                'datetimetzType' => function () use ($generator) {
                    return $generator->dateTime();
                },
                'datetimetzImmutableType' => function () use ($generator) {
                    return date_create_immutable(
                        $generator->dateTime()->format(DateTimeInterface::ATOM)
                    );
                },
                'dateintervalType' => function () use ($generator) {
                    return $generator->dateTime()->diff($generator->dateTime());
                },
            ]
        );
        $populator->execute();
    }
}
