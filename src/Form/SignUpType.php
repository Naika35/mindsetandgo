<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('email', EmailType::class, [
                'constraints' =>
                new NotBlank,
                new Email(),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class, [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank(),
                        new Length([
                            'min' => 8
                        ]),
                    ]
                ],
                'first_options'  => [
                    'label' => "Mot de passe"
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe'
                ],
                'invalid_message' => 'Les mots de passes ne sont pas identiques, veuillez recommencer',
            ])
            ->add('lastname', null, [
                'label' => "Nom",
            ])
            ->add('firstname', null, [
                'label' => "Prénom",
            ])
            ->add('avatar', FileType::class, [
                'data_class' => null,
                'required' => false,
                'mapped' => false,
                'label' => 'Avatar',
                'constraints' => new File([
                    'mimeTypes' => [
                        'image/jpg',
                        'image/jpeg',
                        'image/png',
                    ]

                ])
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
