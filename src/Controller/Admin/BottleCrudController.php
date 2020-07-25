<?php

namespace App\Controller\Admin;

use App\Entity\Bottle;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BottleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Bottle::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('capacity', 'The capacity'),
        ];
    }
    
}
