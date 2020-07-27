<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('User')
            ->setPageTitle(Crud::PAGE_INDEX, 'Customers')
            ->setSearchFields(['id', 'email', 'isVerified', 'firstName', 'lastName', 'createdAt']);
    }

    
    public function configureFields(string $pageName): iterable
    {
        // Critical informations
        $id = IntegerField::new('id', 'ID')->onlyOnIndex();
        $email = TextField::new('email', 'email address');
        $isVerified = BooleanField::new('isVerified', 'Is Verified');
        // Basic information
        $firstName = TextField::new('firstName', 'First name');
        $lastName = TextField::new('lastName', 'Last Name');
        $createdAt = DateTimeField::new('createdAt', 'Created At');

        // Si page index on affiche les informations que l'on souhaite
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $email, $isVerified, $createdAt];
        }

        return [
            FormField::addPanel('Account Information'),
            $email, $isVerified, $firstName, $lastName, $createdAt,

        ];
    }
    
}
