<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebstoreBundle\Entity\Promotion;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('startDate', DateTimeType::class,
            [
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
                'placeholder' => array(
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                )
            ])
            ->add('endDate', DateTimeType::class,
                [
                    'placeholder' => array(
                        'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                        'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                    )
                ])
            ->add('percent')
            ->add('category',
                null,
                [
                    'placeholder' => 'Global promotion',
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Promotion::class]);
    }

    public function getBlockPrefix()
    {
        return 'webstore_bundle_promotion_type';
    }
}
