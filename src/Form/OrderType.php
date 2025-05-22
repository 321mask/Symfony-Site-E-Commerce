<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        
        $builder
            ->add('delivery', EntityType::class, [  
                'choices' => $user->getAddresses(), 
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => false,
                'class' => Address::class, 
                'placeholder' => 'Choisissez une adresse de livraison',
            ])
            ->add('carriers', EntityType::class, [
                'class' => Carrier::class,  
                //'choices' => $carrier,
                //'choice_label' => 'name',
                'placeholder' => 'Choisissez un transporteur',
            ])
           
            ->add('submit', SubmitType::class, [
                'label' => 'Passer au paiement',
                'attr' => ['class' => 'btn btn-success bg-gradient col-12']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
            'user' => null,
            
        ]);
    }
}
