<?php

namespace App\Form;

use App\Entity\ShippingAddresses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class ShippingAddressFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'my_account.address.form.name',
                'attr' => ['autofocus' => true]
            ])
            ->add('address', null, [
                'label' => 'my_account.address.form.address',
            ])
            ->add('address2', null, [
                'label' => 'my_account.address.form.address2',
            ])
            ->add('zipcode', null, [
                'label' => 'my_account.address.form.zip',
            ])
            ->add('city', null, [
                'label' => 'my_account.address.form.city',
            ])
            ->add('country', CountryType::class, [
                'label' => 'my_account.address.form.country',
                'data' => 'FR',
            ])
            ->add('phone', TelType::class, [
                'label' => 'my_account.address.form.phone',
                'required' => false,
            ])
            ->add('sameForBilling', null, [
                'label' => 'my_account.address.form.same_billing',
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
