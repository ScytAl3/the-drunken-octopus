<?php

namespace App\Form;

use App\Entity\ShippingAddresses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class ShippingAddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('address')
            ->add('address2')
            ->add('zipcode')
            ->add('city')
            ->add('country', CountryType::class)
            ->add('phone')
            ->add('sameForBilling', null, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShippingAddresses::class,
            'translation_domain' => 'pagecontent',
        ]);
    }
}
