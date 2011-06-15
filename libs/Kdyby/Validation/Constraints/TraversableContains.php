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
use SplObjectStorage;



/**
 * @author Filip Procházka
 */
class TraversableContains extends Validation\BaseConstraint
{

	/** @var mixed */
	protected $value;



	/**
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}



	/**
	 * @param mixed $other
	 * @return bool
	 */
	public function evaluate($other)
	{
		if ($other instanceof SplObjectStorage) {
			return $other->contains($this->value);
		}

		if (is_object($this->value)) {
			foreach ($other as $element) {
				if ($element === $this->value) {
					return TRUE;
				}
			}

		} else {
			foreach ($other as $element) {
				if ($element == $this->value) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}



	/**
	 * @return TraversableContains
	 */
	public static function create($name, $property, $value)
	{
		return new static($value);
	}



	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'contains ' . Kdyby\Tools\Mixed::toString($this->value, FALSE);
	}

}