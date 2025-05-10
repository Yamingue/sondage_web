<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function persistEntity(EntityManagerInterface $entityManager, $user): void
    {
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));
        $entityManager->persist($user);
        $entityManager->flush();
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('username'),
            TextField::new('email'),
            TextEditorField::new('password')
                ->setFormType(PasswordType::class)
                ->setHelp('Password is hashed and cannot be changed here.')
                ->onlyWhenCreating(),
            ChoiceField::new('roles')
                ->setFormType(ChoiceType::class)
                ->setFormTypeOptions([
                    'choices' => [
                        'USER' => 'ROLE_USER',
                        'ADMIN' => 'ROLE_ADMIN',
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ]),

        ];
    }
}
