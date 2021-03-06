<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Persistence;



/**
 * @author Filip Procházka
 */
interface IObjectFactory
{

	/**
	 * @return object
	 */
	function createNew($arguments = array());

}