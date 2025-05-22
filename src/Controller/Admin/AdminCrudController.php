<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $repoUser;
    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $repoUser)
    {
        $this->passwordHasher = $passwordHasher;
        $this->repoUser = $repoUser;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        // return $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $reponse = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $reponse -> andWhere("entity.roles LIKE '%ROLE_ADMIN%'");        
        return $reponse;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->getPassword()
        );
        $entityInstance->setPassword($hashedPassword);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            // ->remove(Crud::PAGE_INDEX, Action::DELETE)

            // ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            //     return $action->setIcon('fa fa-trash')->setLabel('Supprimer')
            //         ->displayIf(static function ($entity) {
            //             foreach ($entity->getRoles() as $role) {
            //                 if ($role == "ROLE_ADMIN") {
            //                     return false;
            //                 }
            //             }
            //             return true;
            //         });;
            // })
            // ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
            //     return $action->setIcon('fa fa-trash')->setLabel('Supprimer')
            //         ->displayIf(static function ($entity) {
            //             foreach ($entity->getRoles() as $role) {
            //                 if ($role == "ROLE_ADMIN") {
            //                     return false;
            //                 }
            //             }
            //             return true;
            //         });;
            // })
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setFormOptions(['validation_groups' => ['register']]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            // TextField::new('title'),
            // TextEditorField::new('description'),

            EmailField::new('email'),
            TextField::new('lastName')->setLabel('Nom')->setRequired(true),
            TextField::new('firstName')->setLabel('Prénom')->setRequired(true),

            ChoiceField::new('roles')->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN'
            ])->allowMultipleChoices()->setLabel('Role(s)'),

            TextField::new('password')->onlyWhenCreating()->setFormType(PasswordType::class),
            TextField::new('confirmPassword')->onlyWhenCreating()->setFormType(PasswordType::class)->setRequired(true),


        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        $users = $this->repoUser->findAll();
        // dd($users);
        foreach($users as $user){
            $tabFirstName[$user->getFirstName()]=$user->getFirstName();
            $tabLastName[$user->getLastName()]=$user->getLastName();
            $tabEmail[$user->getEmail()]=$user->getEmail();
        }

        return $filters
            ->add('id')
            ->add(ChoiceFilter::new('firstName')->setChoices($tabFirstName))
            ->add(ChoiceFilter::new('lastName')->setChoices($tabLastName))
            ->add(ChoiceFilter::new('email')->setChoices($tabEmail))
            ->add(ArrayFilter::new('roles')->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN'
            ]))
        ;
    }
}
