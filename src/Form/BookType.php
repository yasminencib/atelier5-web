<?php

namespace App\Form;

use App\Entity\Author11;
use App\Entity\Book;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref')
         ->add('title')
            ->add('category', ChoiceType::class,[
                'choices'=>[
                    'science fiction'=>'science fiction',
                    'Mystery'=>'Mystery',
                    'Autobiography'=>'Autobiography',

                ]
            ])
            ->add('publicationDate')
            //->add('published')
            ->add('author' , EntityType::class,[
                'class'=>Author11::class,
                'choice_label'=>'username'
            ])
           // ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
