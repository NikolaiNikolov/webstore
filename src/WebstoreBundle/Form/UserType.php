<?php

namespace WebstoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebstoreBundle\Entity\User;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username", TextType::class,
                [
                'label' => 'Username*'
                ])
            ->add("firstName", TextType::class,
                [
                    'label' => 'First Name',
                    'empty_data' => '',
                    'required' => false
                ])
            ->add("lastName", TextType::class,
                [
                    'label' => 'Last Name',
                    'empty_data' => '',
                    'required' => false
                ])
            ->add("password", RepeatedType::class,
                [
                    'first_options' => [
                        'label' => 'Password*',
                    ],
                    'type' => PasswordType::class,
                    'invalid_message' => 'Passwords should match',
                    'second_options' => [
                        'label' => 'Confirm Password*'
                    ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data-class' => User::class]);
    }

    public function getName()
    {
        return 'webstore_bundle_user_type';
    }
}
