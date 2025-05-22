<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'min' => 1,
                    'max' => 5,
                    'placeholder' => 'Entrez votre note'
                    ]
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'label' => false,
                'attr' => ['placeholder' => 'Entrez votre commentaire']
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer votre commentaire', 'attr' => ['class' => 'btn btn-success bg-gradient col-12']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
