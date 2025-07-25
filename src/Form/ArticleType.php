<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\CategoryArticle;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => CategoryArticle::class,
                'choice_label' => 'name',
            ])
            ->add('imageFile', VichFileType::class, [
                'required' => !$options['is_edit'],
                'label' => $options['is_edit'] ? 'Modifier l\'image (optionnel)' : 'Télécharger une image',
                'allow_delete' => false, // Optionnel : désactive la suppression de fichiers
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'is_edit' => true,
        ]);
    }
}
