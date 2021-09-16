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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' =>
                new NotBlank(),
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => "Si vous voulez changer de mot de passe, veuillez l'inscrire ci-dessous."
                ],
                'second_options' => [
                    'label' => 'Répéter le nouveau mot de passe'
                ],
                'invalid_message' => 'Les mots de passes ne sont pas identiques, veuillez recommencer',
                'mapped' => false,
            ])
            ->add('pseudo', null, [
                'label' => 'Pseudo',
            ])
            ->add('presentation', null, [
                'label' => 'Texte de présentation',
            ])
            ->add('lastname', null, [
                'label' => "Nom",
            ])
            ->add('firstname', null, [
                'label' => "Prénom",
            ])
            ->add('avatar', FileType::class, [
                /* 'data_class' => null, */
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
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
