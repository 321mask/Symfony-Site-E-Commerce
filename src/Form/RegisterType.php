<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('firstName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeHolder' => 'Entrer votre prénom',
                ],
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeHolder' => 'Entrer votre nom',

                ],
                'required' => false
            ])
            ->add('email', TextType::class, [
                'required' => false

            ])
            //  ->add('roles')
            ->add('password', PasswordType::class, [
                'required' => false
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'placeHolder' => 'Confirmation du mdp ',
                ],
                'required' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'S\'inscrire', 'attr' => ['class' => 'btn btn-success col-12']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['register'],
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'register_item',
        ]);
    }
}
