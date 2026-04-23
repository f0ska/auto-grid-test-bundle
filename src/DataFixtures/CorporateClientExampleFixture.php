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

namespace F0ska\AutoGridTestBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use F0ska\AutoGridTestBundle\Entity\CorporateClientExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class CorporateClientExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager); // @phpstan-ignore argument.type
        $populator->addEntity(CorporateClientExample::class, $generator->numberBetween(50, 100), [
            'status' => fn() => $generator->randomElement(['active', 'pending', 'inactive', 'archived']),
            'revenue' => fn() => (string) $generator->randomFloat(2, 100000, 1000000),
            'name' => fn() => $generator->company(),
            'contactEmail' => fn() => $generator->companyEmail(),
        ]);
        $populator->execute();
    }
}
