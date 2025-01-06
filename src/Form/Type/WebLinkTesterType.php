<?php

namespace App\Form\Type;

use App\Entity\WebLinkTester;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WebLinkTesterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('link', UrlType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'https://...',
                    'class' => 'form-inline mt-5'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Check',
                'attr' => [
                    'class' => 'btn fw-bold border-light bg',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebLinkTester::class,
        ]);
    }
}
