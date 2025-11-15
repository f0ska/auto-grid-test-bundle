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
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class BlogUserExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager); // @phpstan-ignore argument.type
        $populator->addEntity(
            BlogUserExample::class,
            20,
            [
                'lastIp' => function () use ($generator) {
                    return $generator->ipv4();
                },
            ]
        );
        $populator->execute();
    }
}
