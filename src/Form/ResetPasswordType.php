<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => false
            ])
            //  ->add('roles')
            ->add('firstName', TextType::class, [
                'disabled' => true,
                'label' => false
            ])
            ->add('lastName', TextType::class, [
                'disabled' => true,
                'label' => false
            ])
            ->add('oldPassword', PasswordType::class, [
                'required' => false
            ])
            ->add('newPassword', PasswordType::class, [
                'required' => false
            ])
            ->add('confirmNewPassword', PasswordType::class, [
                'required' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'Modifier le mot de passe', 'attr' => ['class' => 'btn btn-success col-12']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
