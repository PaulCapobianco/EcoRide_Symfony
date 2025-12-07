<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire d’inscription (données utilisateur + mot de passe).
 */
class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // NOM
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'row_attr' => ['class' => 'col-12 col-md-6'],
                'attr' => [
                    'autocomplete' => 'family-name',
                ],
            ])

            // PRÉNOM
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'row_attr' => ['class' => 'col-12 col-md-6'],
                'attr' => [
                    'autocomplete' => 'given-name',
                ],
            ])

            // EMAIL
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L’adresse e-mail est obligatoire.']),
                    new Assert\Email(['message' => 'Adresse e-mail invalide.']),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'L’e-mail ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'row_attr' => ['class' => 'col-12'],
                'attr' => [
                    'autocomplete' => 'email',
                ],
            ])

            // PSEUDO (OPTIONNEL)
            ->add('username', TextType::class, [
                'label' => 'Pseudo (optionnel)',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'Le pseudo ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'row_attr' => ['class' => 'col-12 col-md-6'],
            ])

            // TÉLÉPHONE (OPTIONNEL)
            ->add('phone', TelType::class, [
                'label' => 'Téléphone (optionnel)',
                'required' => false,
                'row_attr' => ['class' => 'col-12 col-md-6'],
                'attr' => [
                    'autocomplete' => 'tel',
                ],
            ])

            // DATE NAISSANCE (OPTIONNEL, on laisse simple pour l’instant)
            ->add('birthdate', TextType::class, [
                'label' => 'Date de naissance (optionnel)',
                'required' => false,
                'row_attr' => ['class' => 'col-12 col-md-6'],
                'attr' => [
                    'placeholder' => 'JJ/MM/AAAA',
                ],
            ])

            // ADRESSE / CP / VILLE
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'row_attr' => ['class' => 'col-12'],
            ])
            ->add('zip', TextType::class, [
                'label' => 'Code postal',
                'required' => false,
                'row_attr' => ['class' => 'col-6 col-md-3'],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'row_attr' => ['class' => 'col-6 col-md-3'],
            ])

            // MOT DE PASSE (RÉPÉTÉ)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',

                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire.']),
                    new Assert\Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                        'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],

                'row_attr' => ['class' => 'col-12'],
            ])

            // CONDITIONS D’UTILISATION
            ->add('terms', CheckboxType::class, [
                'label' => 'J’accepte les conditions d’utilisation',
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les conditions d’utilisation.',
                    ]),
                ],
                'row_attr' => ['class' => 'col-12'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // On récupère les données sous forme de tableau (pas lié directement à l’entité)
            'data_class'      => null,

            // ✅ Protection CSRF pour l’inscription
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'register',
        ]);
    }
}
