<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
              ->add('file', DropzoneType::class, [
                'label' => 'Bild',
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
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => [
                    'class' => 'btn btn-primary',
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
