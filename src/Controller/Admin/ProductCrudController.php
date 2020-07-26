<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Beer name'),
            TextEditorField::new('description', 'Description'),
            // TextField::new('color'),
            // NumberField::new('ibu', 'IBU'),
            PercentField::new('alcohol', 'ABV')
                ->setNumDecimals(2)
                ->setStoredAsFractional(false),
            MoneyField::new('price', 'Price')
                ->setCurrency('EUR')
                ->setNumDecimals(2)
                ->setStoredAsCents(false),
            NumberField::new('quantity', 'Quantity')
                ->setTextAlign('right'),
            BooleanField::new('availability', 'Availability'),
            AssociationField::new('style')
                ->setTextAlign('right'),
            AssociationField::new('country')
                ->setTextAlign('right'),
            AssociationField::new('brewery')
                ->setTextAlign('right'),
            AssociationField::new('bottle')
                ->setTextAlign('right'),
        ];
    }

    /*
    public function configureCrud(Crud $crud): Crud
    {
         return $crud
            // used to format numbers before rendering them on templates
             ->setNumberFormat('%.2d');
    }
    */
}
