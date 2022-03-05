<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
            [
                'label'=>"Product name",
                'required'=> true,
                'attr' =>
                [
                    'minlength' => 5,
                    'maxlength' => 50
                ]
            ])
            ->add('price', MoneyType::class,
            [
                'required'=> true,
                'currency'=>'USD'
            ])
            ->add('image',FileType::class,
            [
                'data_class' => null,
                'required' => is_null($builder->getData()->getImage()),

            ])
            ->add('description')
            ->add('date', DateType::class,
            [
                'widget' => 'single_text'
            ])
            ->add('quantity')
            ->add('category', EntityType::class,
            [
                'class' => Category::class,
                'choice_label' => 'name',
                'expanded' => true 
            ])
            ->add('brand')
            ->add('Submit', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
