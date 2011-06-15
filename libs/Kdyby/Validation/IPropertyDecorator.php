<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Validation;

use Kdyby;
use Nette;



/**
 * @author Filip Procházka
 */
interface IPropertyDecorator
{

	/**
	 * @return Container
	 */
	function getEntity();

	/**
	 * @return string
	 */
	function getName();

	/**
	 * @param string $property
	 * @return mixed
	 */
	function getValue($property);

	/**
	 * @param object $entity
	 * @return IPropertyDecorator
	 */
	function decorate($entity);

}