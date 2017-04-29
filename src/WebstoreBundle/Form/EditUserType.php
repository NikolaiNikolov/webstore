<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebstoreBundle\Entity\User;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName')
            ->add('lastName')
            ->add('image', FileType::class,
                [
                    'data_class' => null,
                    'required' => false,
                    'label_attr' => ['class' => 'btn btn-block btn-primary btn-file'],
                    'attr' => [
                        'class' => 'hidden',
                        'accept' => 'image/*'
                    ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }

    public function getBlockPrefix()
    {
        return 'webstore_bundle_edit_user_type';
    }
}
