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
use F0ska\AutoGridTestBundle\Entity\BasicExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class BasicExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager); // @phpstan-ignore argument.type
        $populator->addEntity(BasicExample::class, $generator->numberBetween(9000, 10000));
        $populator->execute();
    }
}
