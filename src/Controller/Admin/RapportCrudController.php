<?php

namespace App\Controller\Admin;

use App\Entity\Rapport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\Validator\Constraints\File;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RapportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rapport::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('alert',"ID de l'alert"),
            ImageField::new('fichier')
                ->setUploadDir('public/rapports')
                ->setBasePath('/rapports/')
                // ->setFormType(FileType::class)
                ->setFileConstraints([
                    new File(mimeTypes:['application/pdf','image/*'])
                ]),
            
        ];
    }

    
    public function configureActions(Actions $actions): Actions
    {
        $download = Action::new('Télécharger')
        ->linkToUrl(function (Rapport $rapport) {
            return '/rapports/'.$rapport->getFichier();
        })
        ->setHtmlAttributes(['target' => '_blank'])
        ;

        return $actions
        ->add(Action::INDEX,$download)
        ->remove(Action::INDEX,Action::EDIT);
    }
    
}
