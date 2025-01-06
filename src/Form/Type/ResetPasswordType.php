<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PasswordStrength;

final class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'error_bubbling' => true,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'label' => false,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'first-input'
                    ],
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                    'attr' => [
                        'class' => 'second-input'
                    ],
                ],
                'mapped' => false,
                'constraints' => [
                    new PasswordStrength([
                        'minScore' => PasswordStrength::STRENGTH_WEAK,
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Change password'
            ])
        ;

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }
}