<?php

namespace App\Controller\Admin;

use App\Entity\Product;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

use Symfony\Component\Validator\Constraints\Image;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            // TextField::new('title'),
            // TextEditorField::new('description'),

            TextField::new('name')->setLabel("Nom"),
            SlugField::new('slug')
                ->setTargetFieldName('name')
                ->setLabel("Url")
                ->onlyOnIndex(),
            ImageField::new('image')
                ->setBasePath('uploads')
                ->setUploadDir('public/uploads')
                ->setRequired(false)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setFileConstraints(new Image(maxSize: '500K', mimeTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])),
            TextField::new('subtitle')
                ->setLabel("Sous-Description"),
            TextEditorField::new('description'),
            MoneyField::new('price')
                ->setCurrency('EUR')
                ->setLabel("Prix"),
            AssociationField::new('category')
                ->setLabel("Catégorie"),
            BooleanField::new('onHomePage')
                ->setLabel('Afficher sur la page home')
        ];
    }
}
