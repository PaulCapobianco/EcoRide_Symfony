<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire des préférences conducteur (boutons + champ libre).
 */
class ProfilePreferencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Préférences "bonus" (non obligatoires dans l’énoncé, mais utiles)
            ->add('music', CheckboxType::class, [
                'label'    => 'Musique pendant le trajet',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12 col-md-6 col-lg-4',
                ],
            ])
            ->add('ac', CheckboxType::class, [
                'label'    => 'Climatisation',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12 col-md-6 col-lg-4',
                ],
            ])

            // US8 : Animal / pas d’animal
            ->add('pets', CheckboxType::class, [
                'label'    => 'Animaux acceptés',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12 col-md-6 col-lg-4',
                ],
            ])

            ->add('baggage', CheckboxType::class, [
                'label'    => 'Bagages volumineux acceptés',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12 col-md-6 col-lg-4',
                ],
            ])

            // US8 : Fumeur / non-fumeur → on stocke "Non-fumeur"
            ->add('noSmoking', CheckboxType::class, [
                'label'    => 'Non-fumeur (aucune cigarette dans le véhicule)',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-12 col-md-6 col-lg-4',
                ],
            ])

            // Préférence personnalisée
            ->add('customLabel', TextType::class, [
                'label'    => 'Ajouter une préférence personnalisée',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Ex : Pas de nourriture, Silence complet…',
                ],
                'row_attr' => [
                    'class' => 'col-12 mt-2',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Tableau simple (pas d’entité liée directement)
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
