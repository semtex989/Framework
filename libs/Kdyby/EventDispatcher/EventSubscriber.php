<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\EventDispatcher;

use Kdyby;
use Nette;



/**
 * @author Filip Procházka
 */
interface EventSubscriber
{

	/**
	 * @return array
	 */
	function getSubscribedEvents();

}