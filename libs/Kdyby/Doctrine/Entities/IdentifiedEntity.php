<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Doctrine\Entities;

use Nette;
use Kdyby;



/**
 * @author Filip Procházka
 *
 * @MappedSuperClass
 *
 * @property-read int $id
 */
abstract class IdentifiedEntity extends BaseEntity
{

	/** @Id @Column(type="integer") @GeneratedValue */
	private $id;



	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

}