<?php

namespace App\Controller\Admin;


use App\Entity\Order;



use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

/*
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        
        $queryBuilder = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
    
      
        if (isset($filters['status'])) {
            $statusFilter = $filters->get('status');
            $statusValue = $statusFilter->getData();
    
            
            if ($statusValue !== null) {
                $queryBuilder
                    ->andWhere('entity.status = :status')
                    ->setParameter('status', $statusValue);
            }
        }
    
       
        $queryBuilder->andWhere("entity.roles NOT LIKE '%ROLE_ADMIN%'");
    
        
        return $queryBuilder;
    }

*/

public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
{
    // return $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);

    $reponse = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
    $reponse -> andWhere("entity.status = 0");        
    return $reponse;
}


public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add(ChoiceFilter::new('status')
                ->setChoices([
                    'Commandes Payées' => 1,
                    'Commandes Non Payées' => 0
                ])
            )
            ->add(DateTimeFilter::new('createdAt'));
    }
    public function configureFields(string $pageName): iterable
    {   
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('reference', 'Référence'),
            DateTimeField::new('createdAt', 'Date de création'),
            TextField::new('user.lastName', 'Nom'),
            TextField::new('carrier.name', 'Transporteur'),
            TextField::new('delivery.address', 'Adresse'),
            ArrayField::new('orderDetails'),
            MoneyField::new('total')
            ->setCurrency('EUR') 
            ->setLabel('Total'),
            ChoiceField::new('status', 'Statut')
                ->setChoices([
                    'Non Payé' => 0,
                    'Payé' => 1,
                ])
                ->renderAsBadges([
                    0 => 'danger',
                    1 => 'success',
                ]),
        ];  
    }
        
}
