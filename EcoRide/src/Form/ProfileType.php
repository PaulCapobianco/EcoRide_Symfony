<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Formulaire principal d’édition de profil (coordonnées, photo...).
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Photo de profil (Vich)
            ->add('photoFile', VichImageType::class, [
                'label' => 'Photo de profil',
                'required' => false,
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => false,
                'row_attr' => [
                    'class' => 'col-12',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            // Nom
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de renseigner votre nom.',
                    ]),
                    new Assert\Length(['max' => 50]),
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
            ])

            // Prénom
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de renseigner votre prénom.',
                    ]),
                    new Assert\Length(['max' => 50]),
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
            ])

            // Email
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de renseigner votre e-mail.',
                    ]),
                    new Assert\Email([
                        'message' => 'Adresse e-mail invalide.',
                    ]),
                    new Assert\Length(['max' => 50]),
                ],
                'row_attr' => [
                    'class' => 'col-12',
                ],
            ])

            // Téléphone (optionnel)
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 50]),
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
            ])

            // Pseudo (optionnel)
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 50]),
                ],
                'row_attr' => [
                    'class' => 'col-12 col-md-6',
                ],
            ])

        ;

        if ($options['include_profile_type']) {
            $builder->add('profilType', ChoiceType::class, [
                'label' => 'Type de profil',
                'choices' => [
                    'Je suis passager'          => 'passenger',
                    'Je suis conducteur'        => 'driver',
                    'Je suis conducteur & passager' => 'both',
                ],
                'expanded' => true,
                'multiple' => false,
                'row_attr' => [
                    'class' => 'col-12 mt-3',
                ],
                'help' => 'Si vous choisissez conducteur ou conducteur & passager, vous devrez renseigner au moins un véhicule et vos préférences de trajet.',
            ]);
        }

        $builder
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => 50]),
                ],
                'attr' => [
                    'rows' => 2,
                ],
                'row_attr' => [
                    'class' => 'col-12',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'include_profile_type' => false,
        ]);
    }
}
