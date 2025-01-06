<?php

namespace App\Form\Type;

use App\Entity\WebLinkSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WebLinkScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Name your website',
                    'class' => 'form-inline'
                ]
            ])
            ->add('link', UrlType::class, [
                'label' => 'Link',
                'required' => true,
                'attr' => [
                    'placeholder' => 'https://...',
                    'class' => 'form-inline'
                ]
            ])
            ->add('statusCodeVerifying', ChoiceType::class, [
                'label' => "Email me if status code is not :",
                'attr' => [
                    'class' => 'form-inline'
                ],
                'choices'  => [
                    '2xx' => 2,
                    '3xx' => 3,
                    '4xx' => 4,
                    '5xx' => 5
                ]
            ])
            ->add('cronInSeconds', ChoiceType::class, [
                'label' => "Verification Interval :",
                'attr' => [
                    'class' => 'form-inline'
                ],
                'choices'  => [
                    'Five minutes' => 300,
                    'One hour' => 3600,
                    'Twice day' => 43200,
                    'One day' => 86400
                ],
                'data' => 3600
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Add',
                'attr' => [
                    'class' => 'btn fw-bold border-light bg',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebLinkSchedule::class,
        ]);
    }
}
