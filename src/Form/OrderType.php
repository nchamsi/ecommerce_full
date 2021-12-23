<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Carrier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('adresses', EntityType::class, [
                'label' => 'Choisissez votre adresse de livraison',
                'required' => true,
                'class' => Address::class,
                'choices'=> $user->getAddresses(),
                'multiple' => false,
                'expanded' => true,
                /*'attr' => [
                    'class' => 'mt-4'
                ]*/
            ])
            ->add('carriers', EntityType::class, [
                'label' => 'Choisissez votre mode de livraison',
                'required' => true,
                'class' => Carrier::class,
                'multiple' => false,
                'expanded' => true,
                /*'attr' => [
                    'class' => 'mt-4'
                ]*/
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider ma commande',
                'attr' =>[
                    'class' => 'btn btn-success mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user'=>array()
        ]);
    }
}
