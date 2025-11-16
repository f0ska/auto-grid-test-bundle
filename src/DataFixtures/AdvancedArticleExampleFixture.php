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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use F0ska\AutoGridTestBundle\Entity\AdvancedArticleExample;
use F0ska\AutoGridTestBundle\Entity\AdvancedUserExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleTagExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class AdvancedArticleExampleFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager); // @phpstan-ignore argument.type

        $users = $manager->getRepository(AdvancedUserExample::class)->findAll();
        $tags = $manager->getRepository(BlogArticleTagExample::class)->findAll();

        $populator->addEntity(
            AdvancedArticleExample::class,
            50,
            [
                'author' => function () use ($generator, $users) {
                    return $generator->randomElement($users);
                },
                'tags' => function () use ($generator, $tags) {
                    return new ArrayCollection($generator->randomElements($tags, 3));
                },
                'title' => function () use ($generator) {
                    return $generator->text(40);
                },
                'content' => function () use ($generator) {
                    return $generator->paragraph(50);
                },
            ]
        );
        $populator->execute();
    }

    /**
     * @return class-string[]
     */
    public function getDependencies(): array
    {
        return [
            BlogArticleTagExampleFixture::class,
            AdvancedUserExampleFixture::class,
        ];
    }
}
