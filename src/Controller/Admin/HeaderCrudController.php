<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $required = true;

        if($pageName == 'edit') {
            $required = false;
        }

        return [
            TextField::new('title','Titre du header'),
            BooleanField::new('isHomepage')->setLabel('Produit à la une')->setHelp("Vous permet d'afficher un produit sur la homepage"),
            TextareaField::new('content','Contenu de notre header'),
            TextField::new('button_title', 'Titre du bouton'),
            TextField::new('button_link', 'URL du bouton'),
            ImageField::new('illustration')
                ->setLabel('Image de fond du header')
                ->setHelp('Image de fond du header en JPG')
                ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash]-[extension]')
                ->setBasePath('/uploads')
                ->setUploadDir('/public/uploads')
                ->setRequired($required),
        ];
    }
}
