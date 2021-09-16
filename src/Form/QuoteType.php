<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Quote;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Count;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Citation'
            ])
            ->add('author', null, [
                'label' => 'Auteur de la citation'
            ])
            ->add('categories', EntityType::class, [
                'by_reference' => false, // va chercher addCategories() de l'entité Quote et non setCatégories
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Veuillez sélectionner 3 catégories maximum',
                'constraints' => new Count([
                    'min' => 1,
                    'max' => 3,
                    'minMessage' => "Vous devez choisir au moins une catégorie. Si aucune catégorie ne correspond, veuillez sélectionner 'Autre'",
                    "maxMessage" => "Vous ne pouvez sélectionner que 3 catégories au maximum",
                ])
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
