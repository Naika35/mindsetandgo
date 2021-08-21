<?php

namespace App\Form;

use App\Entity\Quote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', null, [
                'label' => 'Citation'
            ])
            ->add('author', null, [
                'label' => 'Auteur de la citation'
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    "Ambition" => "Ambition",
                    "Amitié" => "Amitié",
                    "Autre" => "Autre",
                    "Bonheur" => "Bonheur",
                    "Célèbre" => "Célèbre",
                    "Communication" => "Communication",
                    "Confiance" => "Confiance",
                    "Confiance en soi" => "Confiance en soi",
                    "Echec" => "Echec",
                    "Erreur" => "Erreur",
                    "Espoir" => "Espoir",
                    "Estime de soi" => "Estime de soi",
                    "Force" => "Force",
                    "Futur" => "Futtur",
                    "Gratitude" => "Gratitude",
                    "Générosité" => "Générosité",
                    "Motivation" => "Motivation",
                    "Pardon" => "Pardon",
                    "Passé" => "Passé",
                    "Peur" => "Peur",
                    "Présent" => "Présent",
                    "Persévérance" => "Persévérence",
                    "Respect" => "Respect",
                    "Séparation" => "Séparation",
                    "Solitude" => "Solitude",
                    "Succès" => "Succès",
                    "Tristesse" => "Tristesse",
                ],
                "multiple" => true,
                "expanded" => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
        ]);
    }
}
