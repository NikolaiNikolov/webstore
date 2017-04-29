<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebstoreBundle\Entity\Product;

class UserProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('price', MoneyType::class)
            ->add('available', ChoiceType::class, [
                'choices' => [
                    'Put on the market' => true,
                    'Save in storage' => false
                ],
                'label' => 'Status'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            ['data_class' => Product::class]
        );
    }

    public function getBlockPrefix()
    {
        return 'webstore_bundle_user_product_type';
    }
}
