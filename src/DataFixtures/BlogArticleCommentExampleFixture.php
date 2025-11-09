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
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use F0ska\AutoGridTestBundle\Entity\BlogArticleCommentExample;
use F0ska\AutoGridTestBundle\Entity\BlogArticleExample;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class BlogArticleCommentExampleFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();
        $populator = new Populator($generator, $manager);

        $users = $manager->getRepository(BlogUserExample::class)->findAll();
        $articles = $manager->getRepository(BlogArticleExample::class)->findAll();

        $populator->addEntity(
            BlogArticleCommentExample::class,
            100,
            [
                'author' => function () use ($generator, $users) {
                    return $generator->randomElement($users);
                },
                'article' => function () use ($generator, $articles) {
                    return $generator->randomElement($articles);
                },
                'comment' => function () use ($generator, $articles) {
                    return $generator->sentence();
                },
            ]
        );
        $populator->execute();
    }

    public function getDependencies(): array
    {
        return [
            BlogArticleExampleFixture::class,
            BlogUserExampleFixture::class,
        ];
    }
}
