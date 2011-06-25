<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Security;

use Doctrine\ORM\Query;
use Doctrine\ORM\NoResultException;
use Doctrine\DBAL\LockMode;
use Kdyby;
use Nette;



/**
 * @author Filip Procházka
 */
class IdentityRepository extends Kdyby\Doctrine\ORM\EntityRepository
{

	/**
	 * Automaticly joins identity.info
	 *
	 * @param string $alias
	 * @return Kdyby\Doctrine\ORM\QueryBuilder
	 */
	public function createQueryBuilder($alias)
	{
		return parent::createQueryBuilder($alias)
			->addSelect('i')
			->leftJoin($alias . '.info', 'i');
	}



    /**
     * Finds an entity by its primary key / identifier.
     *
     * @param int $id
     * @return object
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null)
    {
		if (func_num_args() > 1) {
			throw new Nette\NotSupportedException("'" . __CLAS__ . "' doesn't support locking.");
		}

        // Check identity map first
        if ($entity = $this->_em->getUnitOfWork()->tryGetById($id, $this->_class->rootEntityName)) {
            if (!($entity instanceof $this->_class->name)) {
                return NULL;
            }

            return $entity; // Hit!
        }

		$qb = $this->createQueryBuilder('u')
			->where('u.id = :id')
			->setParameter('id', $id);

		try {
			return $qb->getQuery()->getSingleResult();

		} catch (NoResultException $e) {
			return NULL;
		}
    }



	/**
	 * @param string $nameOrEmail
	 * @return Nette\Security\IIdentity
	 */
	public function findByNameOrEmail($nameOrEmail)
	{
		$qb = $this->createQueryBuilder('u')
			->where('u.username = :nameOrEmail')
			->orWhere('u.email = :nameOrEmail')
			->setParameter('nameOrEmail', $nameOrEmail);

		try {
			return $qb->getQuery()->getSingleResult();

		} catch (NoResultException $e) {
			return NULL;
		}
	}

}