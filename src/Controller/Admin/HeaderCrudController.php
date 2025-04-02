<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
