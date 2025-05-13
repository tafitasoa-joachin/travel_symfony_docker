<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $actions->disable(Action::DELETE, Action::EDIT);
        }

        return $actions
            ->remove(Crud::PAGE_INDEX, actionName: Action::NEW);
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Admnistrateur')
            ->setEntityLabelInPlural('Admnistrateurs')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Pseudo'),
            TextField::new('address')->setLabel('Adress'),
            TextField::new('phone')->setLabel('Téléphone'),
            EmailField::new('email')->onlyOnIndex(), # afficher uniquement sur le liste
        ];
    }
}
