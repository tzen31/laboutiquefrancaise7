<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInSingular('Produit')
        ->setEntityLabelInPlural('Produits');
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;
        
        if ($pageName == 'edit') {
            $required = false;
        }

        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom de votre produit'),
            BooleanField::new('isHomepage')->setLabel('Produit à la une')->setHelp('Afficher un produit sur la HomePage'),
            
            SlugField::new('slug')
                     ->setTargetFieldName('name')->setLabel('URL')->setHelp('URLde votre produit'),
            TextEditorField::new('description')->setHelp('Description de votre produit'),
            ImageField::new('illustration')
                      ->setLabel('Image du produit')
                      ->setBasePath('uploads/')
                      ->setUploadDir('public/uploads/')
                      ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
                      ->setRequired($required),
            MoneyField::new('price')
                      ->setLabel('Prix')
                      ->setCurrency('EUR'), 
            ChoiceField::new('tva')
                      ->setLabel('Taux de TVA')
                      ->setChoices([
                        '5.5%' => '5.5',
                        '10%' => '10',
                        '20%' => '20',
                      ]),                
            AssociationField::new('category','Catégorie associé'),       
            //TextField::new('subtitle')->setLabel('Sous-titre'),            
            //BooleanField::new('isHomepage')->setLabel('Top vente')
            
        ];
    }
}
