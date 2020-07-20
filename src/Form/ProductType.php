<?php

namespace App\Form;

use App\Entity\Style;
use App\Entity\Brewery;
use App\Entity\Country;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('color')
            ->add('ibu')
            ->add('alcohol', PercentType::class, [
                'scale' => 2,
                'type' => 'integer',
            ])
            ->add('price', MoneyType::class, [
                'scale' => 2,
            ])
            ->add('quantity')
            ->add('availability')
            ->add('style', EntityType::class, [
                'class' => Style::class,
                'placeholder' => '-- Select a Style of beer --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.label', 'ASC');
                },
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'placeholder' => '-- Select a Country --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.label', 'ASC');
                },
            ])
            ->add('brewery', EntityType::class, [
                'class' => Brewery::class,
                'placeholder' => '-- Select a Brewery --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.label', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
