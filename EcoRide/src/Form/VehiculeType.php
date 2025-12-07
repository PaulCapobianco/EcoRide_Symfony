<?php

namespace App\Form;

use App\Entity\Voiture;
use App\Entity\Marque;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire CRUD des véhicules côté profil (marque, modèle, énergie...).
 */
class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Marque (liste Doctrine)
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => function (?Marque $marque) {
                    if (!$marque) {
                        return '';
                    }

                    // On essaie d’abord libelle, puis nom, sinon fallback sur l’ID
                    if (method_exists($marque, 'getLibelle') && $marque->getLibelle()) {
                        return $marque->getLibelle();
                    }

                    if (method_exists($marque, 'getNom') && $marque->getNom()) {
                        return $marque->getNom();
                    }

                    return 'Marque #' . $marque->getId();
                },
                'label' => 'Marque',
                'placeholder' => 'Choisir une marque',
                'attr' => [
                    'class' => 'form-select',
                    'data-controller' => 'select-scroll',
                    'data-select-scroll-size-value' => 6,
                ],
            ])

            ->add('modele', TextType::class, [
                'label' => 'Modèle',
            ])

            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation',
            ])

            ->add('energie', ChoiceType::class, [
                'label' => 'Énergie',
                'choices' => [
                    'Essence'              => 'Essence',
                    'Diesel'               => 'Diesel',
                    'Hybride'              => 'Hybride',
                    'Hybride rechargeable' => 'Hybride rechargeable',
                    'Électrique'           => 'Électrique',
                    'Hydrogène'            => 'Hydrogène',
                    'GNV'                  => 'GNV',
                ],
                'placeholder' => 'Choisir une énergie',
            ])

            ->add('couleur', TextType::class, [
                'label' => 'Couleur',
            ])

            // Dans ton entité, c’est un string (pas DateTime), donc on reste sur TextType
            ->add('datePremiereImmatriculation', TextType::class, [
                'label'    => 'Date 1ère immatriculation',
                'required' => false,
                'help'     => 'Format libre, ex : 01/03/2020',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
