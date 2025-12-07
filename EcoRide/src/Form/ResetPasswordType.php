<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Formulaire saisi après clic sur le lien de réinitialisation.
 */
class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('password', RepeatedType::class, [
            'type'            => PasswordType::class,
            'invalid_message' => 'Les deux mots de passe doivent être identiques.',
            'first_options'   => [
                'label' => 'Nouveau mot de passe',
                'attr'  => ['autocomplete' => 'new-password'],
            ],
            'second_options'  => [
                'label' => 'Confirmation',
                'attr'  => ['autocomplete' => 'new-password'],
            ],
            'constraints'     => [
                new NotBlank(['message' => 'Merci de choisir un mot de passe.']),
                new Length([
                    'min'        => 8,
                    'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                ]),
                new Regex([
                    'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                    'message' => 'Ajoutez au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
        ]);
    }
}
