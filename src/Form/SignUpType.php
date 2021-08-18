<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
            ])
            ->add('password', PasswordType::class, [
                'constraints' => new NotBlank, 
                'label' => 'Mot de passe'

            ])
            ->add('lastname', null, [
                'label' => "Nom",
            ])
            ->add('firstname', null, [
                'label' => "Prénom",
            ])
            ->add('avatar')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
