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
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleTagExample;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class BlogArticleExampleFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager);

        $users = $manager->getRepository(BlogUserExample::class)->findAll();
        $tags = $manager->getRepository(BlogArticleTagExample::class)->findAll();

        $populator->addEntity(
            BlogArticleExample::class,
            30,
            [
                'author' => function () use ($generator, $users) {
                    return $generator->randomElement($users);
                },
                'tags' => function () use ($generator, $tags) {
                    return new ArrayCollection($generator->randomElements($tags));
                },
                'title' => function () use ($generator, $tags) {
                    return $generator->text(64);
                },
                'content' => function () use ($generator, $tags) {
                    return $generator->paragraph(10);
                },
            ]
        );
        $populator->execute();
    }

    public function getDependencies(): array
    {
        return [
            BlogArticleTagExampleFixture::class,
            BlogUserExampleFixture::class,
        ];
    }
}
