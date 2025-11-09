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
use F0ska\AutoGridTestBundle\Entity\MainTypesExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class MainTypesExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager);
        $populator->addEntity(
            MainTypesExample::class,
            100,
            [
                'asciiStringType' => function () use ($generator) {
                    return $generator->asciify();
                },
                'smallintType' => function () use ($generator) {
                    return $generator->randomNumber(4);
                },
            ]
        );
        $populator->execute();
    }
}
