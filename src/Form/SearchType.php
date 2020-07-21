<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Brewery;
use App\Entity\Country;
use App\Entity\Style;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Search',
                ]
            ])
            ->add('style', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Style::class,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('country', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Country::class,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('brewery', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Brewery::class,
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}