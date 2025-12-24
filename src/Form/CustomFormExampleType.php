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

namespace F0ska\AutoGridTestBundle\Form;

use F0ska\AutoGridTestBundle\Entity\CustomFormExample;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class CustomFormExampleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'file',
            FileType::class,
            [
                'label' => 'Select an image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        extensions: ['jpg', 'jpeg', 'png', 'webp'],
                        extensionsMessage: 'Please upload an image file (jpg, jpeg, png, webp)',
                    ),
                ],
            ]
        );

        $entity = $options['data'] ?? null;
        if ($entity instanceof CustomFormExample and null !== $entity->getFile()) {
            $size = round($entity->getFileSize() / 1024, 1);
            $builder->add(
                'delete',
                CheckboxType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Delete file ( ' . $size . ' KB )',
                ]
            );
        }
    }
}
