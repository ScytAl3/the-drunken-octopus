<?php

namespace App\Controller\Admin;

use App\Entity\Brewery;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BreweryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brewery::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Brewery')
            ->setEntityLabelInPlural('Breweries')
            ->setPageTitle(Crud::PAGE_INDEX, 'Beer Breweries');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', 'ID')->hideOnForm(),
            TextField::new('label', 'Name of the brewery'),
        ];
    }
    
}
