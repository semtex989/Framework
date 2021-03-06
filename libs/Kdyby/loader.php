<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

use Nette\Diagnostics\Debugger;
use Kdyby\Loaders\SplClassLoader;

@header('X-Powered-By: Nette Framework with Kdyby'); // @ - headers may be sent

define('KDYBY', TRUE);
define('KDYBY_FRAMEWORK_DIR', __DIR__);

if (!defined('NETTE')) {
	if (!defined('LIBS_DIR')) {
		throw new RuntimeException("Nette Framework cannot be loaded! Missing constant LIBS_DIR");
	}

	// Load Nette Framework
	require_once LIBS_DIR . '/Nette/loader.php';
}

// Require shorcut functions
require_once KDYBY_FRAMEWORK_DIR . '/functions.php';


// Configure environment
Debugger::enable(Nette\Configurator::detectProductionMode());
Debugger::$strictMode = TRUE;


// Kdyby loader
require_once KDYBY_FRAMEWORK_DIR . '/Loaders/SplClassLoader.php';
$loader = SplClassLoader::getInstance()->addNamespaces(array(
	'Kdyby' => KDYBY_FRAMEWORK_DIR,
))->register();


// Create Configurator
$configurator = new Kdyby\DI\Configurator;
Nette\Environment::setConfigurator(new Kdyby\DI\Configurator);
