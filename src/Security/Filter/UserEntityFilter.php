<?php

namespace App\Security\Filter;

use App\Entity\ACL\Admin;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\ChoiceList\DoctrineChoiceLoader;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;

class UserEntityFilter
{
    public function createLazyLoader(EntityManager $em, Admin $admin = null)
    {
        $qb = $this->filterListQueryBuilder(
            $this->createQueryBuilder($em),
            $admin
        );

        return new \Symfony\Component\Form\ChoiceList\LazyChoiceList(
            new DoctrineChoiceLoader(
                $em,
                User::class,
                null,
                new ORMQueryBuilderLoader($qb)
            ),
            function ($choice) {
                /** @var User $choice */
                return $choice->getId();
            }
        );
    }

    public function filterListQueryBuilder(
        QueryBuilder $qb,
        Admin $admin = null
    ) {
        return $this->filterQueryBuilder($qb, $admin);
    }

    public function filterSearchQueryBuilder(
        QueryBuilder $qb,
        Admin $admin = null
    ) {
        return $this->filterQueryBuilder($qb, $admin);
    }

    private function filterQueryBuilder(
        QueryBuilder $qb,
        Admin $admin = null
    ) {
        // do not show soft deleted
        $qb->andWhere('entity.dDeletedAt IS NULL');

        return $qb;
    }

    private function createQueryBuilder(EntityManager $em)
    {
        $qb = new QueryBuilder($em);

        $qb->select('entity')
            ->from(User::class, 'entity');

        return $qb;
    }
}
