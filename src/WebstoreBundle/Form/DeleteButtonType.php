<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteButtonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('delete', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-danger',
                'onClick' => 'return confirm(\'Are you sure you want to delete this product?\')'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getName()
    {
        return 'webstore_bundle_delete_button_type';
    }
}
