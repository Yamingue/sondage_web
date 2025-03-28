<?php

namespace App\Controller\Admin;

use App\Entity\Alert;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AlertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Alert::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),
            TextField::new('contact')
                ->hideOnForm(),
            TextField::new('categorie'),
            ImageField::new('photo')
                ->setUploadDir('public/Image/alert')
                ->hideOnForm(),
            TextField::new('lat')
                ->hideOnForm(),
            TextField::new('lng')
                ->hideOnForm(),
            ChoiceField::new('Status')
                ->setChoices([
                    'En attente' => 'En attente',
                    'En cours' => 'En cours',
                    'Résolu' => 'Résolu',
                    'Non résolu' => 'Non résolu',
                    'Rejeté' => 'Rejeté',
                ]),
        ];
    }
}
