<?php

/**
 * This file is part of the Framework - Content Managing System (F-CMS) Kdyby.
 *
 * Copyright (c) 2008, 2010 Filip Procházka (http://hosiplan.kdyby.org)
 *
 * For more information please see http://www.kdyby.org
 *
 * @package F-CMS Kdyby-Common
 */


namespace Kdyby;

use Nette;



/**
 * @author Filip Procházka <hosiplan@kdyby.org>
 */
class FileSystem extends Nette\Object
{


	/**
	 * @param string $dir
	 * @return string
	 */
	public static function prepareWritableDir($dir)
	{
		$dir = Nette\Environment::expand($dir);

		umask(0000);
		@mkdir($dir, 0755); // @ - directory may exists

		return $dir;
	}

}
