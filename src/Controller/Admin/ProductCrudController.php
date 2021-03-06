<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\StyleRepository;
use App\Repository\BottleRepository;
use App\Repository\BreweryRepository;
use App\Repository\CountryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\PercentField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'ID')->onlyOnIndex();
        // Product image
        $imageFile = ImageField::new('imageFile')
            ->setLabel('Image (JPEG or PNG file)')
            ->setFormType(VichImageType::class)->setFormTypeOptions([
                'allow_delete' => true,
            ]);
        $image = ImageField::new('imageName', 'Picture')->setBasePath('uploads/products');
        // Product basic information
        $name = TextField::new('title', 'Beer name');
        $description = TextEditorField::new('description', 'Description')
            ->setNumOfRows(7);
            // ->setFormType(TextareaType::class);
        $color = TextField::new('color');
        $ibu = NumberField::new('ibu', 'IBU');
        $abv = PercentField::new('alcohol', 'ABV')
            ->setNumDecimals(2)
            ->setStoredAsFractional(false);
        $price = MoneyField::new('price')
            ->setCurrency('EUR')
            ->setNumDecimals(2)
            ->setStoredAsCents(false);
        $quantity = NumberField::new('quantity')
            ->setTextAlign('right');
        $availability = BooleanField::new('availability');
        // Product relation
        $style = AssociationField::new('style')
            ->setFormTypeOptions([
                'query_builder' => function (StyleRepository $sr) {
                    return $sr->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->setTextAlign('right');
        $country = AssociationField::new('country')
            ->setFormTypeOptions([
                'query_builder' => function (CountryRepository $cr) {
                    return $cr->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->setTextAlign('right');
        $brewery = AssociationField::new('brewery')
            ->setFormTypeOptions([
                'query_builder' => function (BreweryRepository $br) {
                    return $br->createQueryBuilder('p')
                        ->orderBy('p.label', 'ASC');
                },
            ])
            ->setTextAlign('right');
        $bottle = AssociationField::new('bottle')
            ->setFormTypeOptions([
                'query_builder' => function (BottleRepository $br) {
                    return $br->createQueryBuilder('p')
                        ->orderBy('p.capacity', 'ASC');
                },
            ])
            ->setTextAlign('right');

        // Si page index on affiche les informations que l'on souhaite
        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $image, $name, $description, $price, $quantity, $availability, $style, $country, $brewery, $bottle];
        }

        return [
            FormField::addPanel('Image'),
            $imageFile,
            FormField::addPanel('Basic information'),
            $name, $description, $color, $ibu, $abv, $price, $quantity, $availability,
            FormField::addPanel('Categories'),
            $style, $country, $brewery, $bottle,
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud;
    }
}
