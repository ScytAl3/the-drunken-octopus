<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
        // Product image
        $image = ImageField::new('imageFile')
            ->setFormType(VichImageType::class)->setFormTypeOptions([
                'label' => 'Image (JPEG or PNG file)',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                // 'imagine_pattern' => 'squared_thumb_small',
            ]);
        // Product basic information
        $name = TextField::new('title', 'Beer name');
        $description = TextEditorField::new('description', 'Description');
        $color = TextField::new('color');
        $ibu = NumberField::new('ibu', 'IBU');
        $abv = PercentField::new('alcohol', 'ABV')
            ->setNumDecimals(2)
            ->setStoredAsFractional(false);
        $price = MoneyField::new('price', 'Price')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false);
        $quantity = NumberField::new('quantity', 'Quantity')
            ->setTextAlign('right');
        $availability = BooleanField::new('availability', 'Availability');
        // Product relation
        $style = AssociationField::new('style')
            ->setTextAlign('right');
        $country = AssociationField::new('country')
            ->setTextAlign('right');
        $brewery = AssociationField::new('brewery')
            ->setTextAlign('right');
        $bottle = AssociationField::new('bottle')
            ->setTextAlign('right');

        // Si page index on affiche les informations que l'on souhaite
        if (Crud::PAGE_INDEX === $pageName) {
            return [$image, $name, $description, $price, $quantity, $availability, $style, $country, $brewery, $bottle];
        }

        return [
            FormField::addPanel('Image'),
            $image,
            FormField::addPanel('Basic information'),
            $name, $description, $color, $ibu, $abv, $price, $quantity, $availability,
            FormField::addPanel('Categories'),
            $style, $country, $brewery, $bottle,
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // defines the initial sorting applied to the list of entities
            ->setDefaultSort(['title' => 'ASC']);
    }
}
