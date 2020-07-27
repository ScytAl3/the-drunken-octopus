<?php

namespace App\Controller\Admin;

use App\Entity\Style;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class StyleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Style::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Style')
            ->setEntityLabelInPlural('Styles')
            ->setPageTitle(Crud::PAGE_INDEX, 'Beer Styles');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('label', 'Name of the style'),
        ];
    }
}
