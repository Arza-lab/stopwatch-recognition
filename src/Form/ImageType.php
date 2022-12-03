<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
              ->add('file', FileType::class, [
                'label' => 'label.image',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'capture' => 'camera',
                ],
                'constraints' => [
                     new File([
                          'maxSize' => '10M',
                          'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                          ],
                          'mimeTypesMessage' => 'Please upload a valid image',
                     ]),
                ],
              ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
