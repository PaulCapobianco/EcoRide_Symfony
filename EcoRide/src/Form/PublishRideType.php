<?php

namespace App\Form;

use App\Entity\Voiture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire publication de covoiturage (trajet, véhicule, prix...).
 */
class PublishRideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Voiture[] $vehicules */
        $vehicules   = $options['vehicules'];
        $vehicleHelp = $options['vehicle_help'];

        $builder
            // ================== Départ ==================
            ->add('from', TextType::class, [
                'label' => 'Ville de départ',
                'attr' => [
                    'placeholder' => 'Ex : Lyon',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-5',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci de renseigner une ville de départ.'),
                    new Assert\Length(max: 255),
                ],
            ])

            ->add('fromAddress', TextType::class, [
                'label' => 'Adresse de départ',
                'attr' => [
                    'placeholder' => 'Ex : 12 rue de la République, Lyon',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-7',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci de renseigner une adresse de départ.'),
                    new Assert\Length(max: 255),
                ],
            ])

            // ================== Arrivée ==================
            ->add('to', TextType::class, [
                'label' => 'Ville d\'arrivée',
                'attr' => [
                    'placeholder' => 'Ex : Grenoble',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-5',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci de renseigner une ville d’arrivée.'),
                    new Assert\Length(max: 255),
                ],
            ])

            ->add('toAddress', TextType::class, [
                'label' => 'Adresse d’arrivée',
                'attr' => [
                    'placeholder' => 'Ex : 5 avenue Alsace-Lorraine, Grenoble',
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-7',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci de renseigner une adresse d’arrivée.'),
                    new Assert\Length(max: 255),
                ],
            ])

            // ================== Date / heures ==================
            ->add('date', DateType::class, [
                'label'  => 'Date',
                'widget' => 'single_text',
                // On attend un \DateTime (mutable) dans les données du formulaire
                'input'  => 'datetime',
                'row_attr' => [
                    'class' => 'col-12 col-md-3',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci de choisir une date.'),
                ],
            ])

            ->add('timeStart', TimeType::class, [
                'label'  => 'Heure départ',
                'widget' => 'single_text',
                'input'  => 'datetime',
                'row_attr' => [
                    'class' => 'col-6 col-md-3',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci de préciser l’heure de départ.'),
                ],
            ])

            ->add('timeEnd', TimeType::class, [
                'label'    => 'Heure arrivée (optionnel)',
                'widget'   => 'single_text',
                'input'    => 'datetime',
                'required' => false,
                'row_attr' => [
                    'class' => 'col-6 col-md-3',
                ],
            ])

            // ================== Véhicule ==================
            ->add('vehicle', EntityType::class, [
                'label'       => 'Véhicule utilisé',
                'class'       => Voiture::class,
                'choices'     => $vehicules,
                'placeholder' => empty($vehicules)
                    ? 'Aucun véhicule disponible'
                    : 'Choisir…',
                'required'   => true,
                'help'       => $vehicleHelp,
                'choice_label' => function (?Voiture $v): string {
                    if (!$v) {
                        return '';
                    }

                    $marqueLabel = 'Véhicule';
                    $marque      = method_exists($v, 'getMarque') ? $v->getMarque() : null;

                    if ($marque) {
                        if (method_exists($marque, 'getLibelle') && $marque->getLibelle()) {
                            $marqueLabel = $marque->getLibelle();
                        } elseif (method_exists($marque, 'getNom') && $marque->getNom()) {
                            $marqueLabel = $marque->getNom();
                        }
                    }

                    $parts = [$marqueLabel];

                    if (method_exists($v, 'getModele') && $v->getModele()) {
                        $parts[] = $v->getModele();
                    }
                    if (method_exists($v, 'getEnergie') && $v->getEnergie()) {
                        $parts[] = $v->getEnergie();
                    }

                    return implode(' — ', $parts);
                },
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
                'constraints' => [
                    new Assert\NotNull(
                        message: empty($vehicules)
                            ? 'Vous devez d’abord enregistrer un véhicule dans votre profil.'
                            : 'Merci de choisir un véhicule.'
                    ),
                ],
            ])

            // ================== Places / prix ==================
            ->add('seats', IntegerType::class, [
                'label' => 'Places dispo',
                'attr' => [
                    'step'        => 1,
                    'min'         => 1,
                    'max'         => 6,
                    'inputmode'   => 'numeric',
                    'placeholder' => 'Ex : 3',
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-3',
                ],
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci d’indiquer le nombre de places disponibles.'),
                    new Assert\Range(
                        min: 1,
                        max: 6,
                        notInRangeMessage: 'Le nombre de places doit être compris entre {{ min }} et {{ max }}.'
                    ),
                ],
            ])

            ->add('price', IntegerType::class, [
                'label' => 'Crédit(s) / personne',
                'attr' => [
                    'step'        => 1,
                    'min'         => 1,
                    'inputmode'   => 'numeric',
                    'placeholder' => 'Ex : 12',
                ],
                'row_attr' => [
                    'class' => 'col-6 col-md-3',
                ],
                'help' => '<span class="text-success fw-semibold">Pour chaque trajet, 2 crédits sont conservés par la plateforme pensez à en tenir compte.</span>',
                'help_html' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'Merci d’indiquer le nombre de crédits par personne.'),
                    new Assert\Range(
                        min: 1,
                        notInRangeMessage: 'Le nombre de crédits par personne doit être au moins {{ min }}.'
                    ),
                ],
            ])

            // ================== CGU ==================
            ->add('terms', CheckboxType::class, [
                'label'  => 'J’accepte les conditions d’utilisation et je m’engage à respecter le code de la route.',
                'mapped' => false,
                'row_attr' => [
                    'class' => 'col-12 mb-2',
                ],
                'constraints' => [
                    new Assert\IsTrue(message: 'Vous devez accepter les conditions d’utilisation.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'         => null,
            'csrf_protection'    => true,
            'csrf_field_name'    => '_token',
            'csrf_token_id'      => 'publish_ride',
            'vehicules'          => [],
            'vehicle_help'       => null,
            'translation_domain' => false,
        ]);

        $resolver->setAllowedTypes('vehicules', 'array');
        $resolver->setAllowedTypes('vehicle_help', ['null', 'string']);
    }
}
