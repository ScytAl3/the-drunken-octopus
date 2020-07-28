<?php

namespace App\Controller\Admin;

use App\Entity\Bottle;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BottleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bottle::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Capacity')
            ->setEntityLabelInPlural('Capacities')
            ->setPageTitle(Crud::PAGE_INDEX, 'Beer Bottle Capacities');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', 'ID')->hideOnForm(),
            NumberField::new('capacity', 'Bottle capacity'),
        ];
    }
    
}
