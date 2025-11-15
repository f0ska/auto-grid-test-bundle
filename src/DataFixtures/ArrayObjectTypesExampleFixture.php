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
use F0ska\AutoGridTestBundle\Entity\ArrayObjectTypesExample;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class ArrayObjectTypesExampleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $generator = Factory::create();
        $populator = new Populator($generator, $manager); // @phpstan-ignore argument.type
        $randArray = function () use ($generator) {
            $arr = [];
            for ($i = 0; $i < $generator->numberBetween(2, 10); ++$i) {
                $arr[$generator->word()] = $generator->word();
            }
            return $arr;
        };
        $binArray = $this->getBinaryImgArray();
        $populator->addEntity(
            ArrayObjectTypesExample::class,
            100,
            [
                'simpleArray' => function () use ($generator) {
                    $arr = [];
                    for ($i = 0; $i < $generator->numberBetween(1, 3); ++$i) {
                        $arr[] = $generator->word();
                    }
                    return $arr;
                },
                'binaryType' => function () {
                    return hash('sha1', uniqid(), true);
                },
                'blobType' => function () use ($generator, $binArray) {
                    return $generator->randomElement($binArray);
                },
                'arrayType' => $randArray,
                'jsonType' => $randArray,
                'objectType' => function () use ($randArray) {
                    return (object) $randArray();
                },
            ]
        );
        $populator->execute();
    }

    private function getBinaryImgArray(): array
    {
        return [
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAA2ElEQVRoQ2MM/Z3b/oB7cvBnAYZM7flnZKO+7hRbmL7qVr5'
                . 'Wq9k+dundt3bOcxsK8owaYgzOQ9kDsAhgXLuqtWwoxgB6CmLca72lbSglIVxZgHE0jwyyQmA0jwy2Yno0jwy2inI0j4zmERq15Ub'
                . 'bWoOttTza1hps/ZXRPDKaR2jUpR5ta422tWjUIRutR0brERoN/I32R0b7I6P9EfyD66N5ZDSPjOaR0TwytKbgRvsjo/2R0f4I/mn'
                . 'y0TwymkdG88hoHhkai2lG16IMtsFr2LDUaD0y2OoRAPJxTvo5LALtAAAAAElFTkSuQmCC'
            ),
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAA2ElEQVRoQ2PcvFnp21ZF+0M8U14cerj9nMvehV6XnvnvKXe'
                . '7amqr2rAlP8BfxnkoyDOWZHhWDWUPwCKAUehA4oShGAPoKYjR7kmQy1BKQriyAONoHhlkhcBoHhlsxfRoHhlsFeVoHhnNIzRqy42'
                . '2tQZba3m0rTXY+iujeWQ0j9CoSz3a1hpta9GoQzZaj4zWIzQa+Bvtj4z2R0b7I/gH10fzyGgeGc0jo3lkaE3BjfZHRvsjo/0R/NP'
                . 'ko3lkNI+M5pHRPDI0FtOMrkUZbIPXsGGp0XpksNUjALPtjVqgQSLiAAAAAElFTkSuQmCC'
            ),
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAA2ElEQVRoQ2Pck9njsNJk4+Vr+yI6fzau0cgK+1Ls9t2U+dZ'
                . 'Bw6kT9ROYcwMm5g0FeUbr6KlzhrIHYBHAOM/I1HwoxgB6CmLc92r2+aGUhHBlAcbRPDLICoHRPDLYiunRPDLYKsrRPDKaR2jUlht'
                . 'taw221vJoW2uw9VdG88hoHqFRl3q0rTXa1qJRh2y0HhmtR2g08DfaHxntj4z2R/APro/mkdE8MppHRvPI0JqCG+2PjPZHRvsj+Kf'
                . 'JR/PIaB4ZzSOjeWRoLKYZXYsy2AavYcNSo/XIYKtHADeRgVO+dhpzAAAAAElFTkSuQmCC'
            ),
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAA2UlEQVRoQ2PMfG7jfzh6wrp3du/eued1zLZ3iHM/UHxdXcu'
                . '//8m6faH3DC0fzhwK8oxSDbteD2UPwCKAcYHegrNDMQbQUxBj1we2G0MpCeHKAoyjeWSQFQKjeWSwFdOjeWSwVZSjeWQ0j9CoLTf'
                . 'a1hpsreXRttZg66+M5pHRPEKjLvVoW2u0rUWjDtloPTJaj9Bo4G+0PzLaHxntj+AfXB/NI6N5ZDSPjOaRoTUFN9ofGe2PjPZH8E+'
                . 'Tj+aR0TwymkdG88jQWEwzuhZlsA1ew4alRuuRwVaPAADkr8n5mOQA4wAAAABJRU5ErkJggg=='
            ),
            base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAA2ElEQVRoQ2OMyLS+f0JYOJzz1sk3ppK8Sc1rBRPEF2S82u3'
                . 'ywHeG0axrvOYq74aCPKNmmLjSUPYALAIYH8qYPhmKMYCeghg3M9RKD6UkhCsLMI7mkUFWCIzmkcFWTI/mkcFWUY7mkdE8QqO23Gh'
                . 'ba7C1lkfbWoOtvzKaR0bzCI261KNtrdG2Fo06ZKP1yGg9QqOBv9H+yGh/ZLQ/gn9wfTSPjOaR0TwymkeG1hTcaH9ktD8y2h/BP00'
                . '+mkdG88hoHhnNI0NjMc3oWpTBNngNG5YarUcGWz0CAKMoAHP77sYRAAAAAElFTkSuQmCC'
            ),
        ];
    }
}
