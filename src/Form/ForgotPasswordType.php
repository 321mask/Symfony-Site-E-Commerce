<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('newPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'first_options' => [
                'label' => 'Nouveau mot de passe',
                'attr' => ['class' => 'form-control'],
            ],
            'second_options' => [
                'label' => 'Confirmer le mot de passe',
                'attr' => ['class' => 'form-control'],
            ],
            'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['forgotPassword']
        ]);
    }
}
