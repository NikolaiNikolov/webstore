<?php

namespace WebstoreBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebstoreBundle\Entity\Product;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)

            ->add('description', TextareaType::class)
            ->add('image', FileType::class,
                [
                    'data_class' => null,
                    'required' => false,
                    'label_attr' => ['class' => 'btn btn-block btn-primary btn-file'],
                    'attr' => [
                        'class' => 'hidden',
                        'accept' => 'image/*'
                    ]
                ])
            ->add('price', MoneyType::class,
                [
                    'currency' => 'BGN'
                ])
            ->add('quantity', IntegerType::class)

            ->add('category',
                null,
                [
                    'placeholder' => 'Choose category',
                ]
            )
            ->add('available', ChoiceType::class, [
                    'choices' => [
                        'Put on the market' => true,
                        'Save in storage' => false
                    ],
                    'label' => 'Status'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Product::class]);
    }

    public function getName()
    {
        return 'webstore_bundle_product_type';
    }
}
