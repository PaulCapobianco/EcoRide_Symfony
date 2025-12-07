<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Formulaire minimal pour l’authentification (username/password).
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('_username', EmailType::class, [
                'label' => 'E-mail',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'vous@exemple.com',
                    'autocomplete' => 'email',
                    'id' => 'login-email',
                ],
                'row_attr' => [
                    'class' => 'col-12',
                ],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'current-password',
                    'id' => 'login-password',
                ],
                'row_attr' => [
                    'class' => 'col-12',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => null,
            'csrf_protection'  => true,
            'csrf_field_name'  => '_csrf_token',  
            'csrf_token_id'    => 'authenticate',
        ]);
    }

    public function getBlockPrefix(): string
    {
        // pour avoir name="_username" et name="_password"
        return '';
    }
}
