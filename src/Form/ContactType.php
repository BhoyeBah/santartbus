<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom est requis',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire',
                    ]),
                    new Assert\Email([
                        'message' => 'Veuillez entrer une adresse email valide',
                    ]),
                ]
            ])
            ->add('subject', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le sujet est requis',
                    ]),
                    new Assert\Length([
                        'min' => 5,
                        'minMessage' => 'Le sujet doit contenir au moins {{ limit }} caractères',
                        'max' => 100,
                        'maxMessage' => 'Le sujet ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le message est requis',
                    ]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('envoyer', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
