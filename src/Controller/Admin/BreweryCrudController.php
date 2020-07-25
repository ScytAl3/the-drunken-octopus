<?php

namespace App\Controller\Admin;

use App\Entity\Brewery;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BreweryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brewery::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('label', 'Name of the brewery'),
        ];
    }
    
}
