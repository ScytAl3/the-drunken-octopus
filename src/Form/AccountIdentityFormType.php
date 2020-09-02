<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AccountIdentityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'my_account.identity.form.firstname',
                'attr' => ['autofocus' => true]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'my_account.identity.form.lastname',
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'my_account.identity.form.birth',
                'widget' => 'single_text'
            ])
            ->add('email', EmailType::class, [
                'label' => 'my_account.identity.form.email'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'pagecontent',
        ]);
    }
}
