<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Validation\Constraints;

use Kdyby;
use Kdyby\Validation;
use Nette;



/**
 * @author Filip Procházka
 */
class FileExists extends Validation\BaseConstraint
{

	/**
	 * @param mixed $other
	 * @return bool
	 */
	public function evaluate($other)
	{
		return file_exists($other);
	}



	/**
	 * @return FileExists
	 */
	public static function create()
	{
		return new static();
	}



	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'file exists';
	}

}