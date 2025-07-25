<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom *',
                'attr' => [
                    'placeholder' => 'Votre nom'
                ]
            ])
            ->add('prenom',null, [
                'label' => 'Prénom *',
                'attr' => [
                    'placeholder' => 'Votre prénom'
                ]
            ])
            ->add('phone', null, [
                'label' => 'Téléphone *',  
                'attr' => [
                    'placeholder' => 'Votre numéro de téléphone'
                ]
            ])
            ->add('adresse', null, [
                 'label' => 'Aresse *',
                'attr' => [
                    'placeholder' => 'Votre adresse'
                ]
            ])
            ->add('ville', null, [
                 'label' => 'Ville *',
                'attr' => [
                    'placeholder' => 'Votre ville'
                ]
            ])
            ->add('email', EmailType::class, [
                 'label' => 'Email *',
                'attr' => [
                    'placeholder' => 'Votre email'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => Order::class
        ]);
    }
}
