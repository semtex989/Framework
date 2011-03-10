<?php

/**
 * This file is part of the Framework - Content Managing System (CMF) Kdyby.
 *
 * Copyright (c) 2008, 2010 Filip Procházka (http://hosiplan.kdyby.org)
 *
 * For more information please see http://www.kdyby.org
 *
 * @package CMF Kdyby-Common
 */


namespace Kdyby\Environment;

use Nette;
use Nette\Environment;
use Kdyby;



/**
 * @author Filip Procházka <hosiplan@kdyby.org>
 */
class Configurator extends Nette\Configurator
{

	/** @var string */
	private static $kdybyConfigFile = "%kdybyDir%/config.kdyby.neon";

	/** @var array */
	private static $configHooks = array(
		"Nette-Security-IIdentity" => "Kdyby\\Identity"
	);

	/** @var array */
	private $configFiles = array();



	public function __construct()
	{
		foreach (array(self::$kdybyConfigFile, $this->defaultConfigFile) as $file) {
			$file = realpath(Nette\Environment::expand($file));
			if (file_exists($file)) {
				$this->configFiles[$file] = array($file, TRUE, array());
			}
		}
	}



	/**
	 * @param string $file
	 * @param bool $environment
	 * @param string|array $prefixPath
	 * @return Kdyby\Environment\Configurator
	 */
	public function addConfigFile($file, $environments = TRUE, $prefixPath = NULL)
	{
		$file = realpath(Nette\Environment::expand($file));
		$this->configFiles[$file] = array($file, (bool)$environments, $prefixPath ? (array)$prefixPath : array());
		return $this;
	}



	/**
	 * Detect environment mode.
	 * @param  string mode name
	 * @return bool
	 */
	public function detect($name)
	{
		switch ($name) {
			case 'production':
				// detects production mode by server IP address
				if (isset($_SERVER['SERVER_ADDR']) || isset($_SERVER['LOCAL_ADDR'])) {
					$addr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
					if (substr($addr, -4) === '.loc') {
						return FALSE;
					}
				}
		}

		return parent::detect($name);
	}



	/**
	 * @param array $options
	 * @return Kdyby\Application\PresenterFactoryChain
	 */
	public static function createPresenterFactory(array $options)
	{
		$presenterFactoryChain = new Kdyby\Application\PresenterFactoryChain($options['context']);
		$presenterFactoryChain->addPresenterLoader(new Kdyby\Application\PresenterLoaders\AppPresenterLoader);
		$presenterFactoryChain->addPresenterLoader(new Kdyby\Application\PresenterLoaders\AdminPresenterLoader);

		return $presenterFactoryChain;
	}



	/**
	 * @return Nette\Application\Application
	 */
	public static function createApplication(array $options = NULL)
	{
		$options['class'] = "Kdyby\Application\Application"; // yes hardcode!

		$application = parent::createApplication($options);
		$application->getContext()->addService('Nette\\Application\\IRouter', array(Environment::getConfigurator(), 'createRouter'));

		return $application;
	}



	/**
	 * @return Nette\Application\MultiRouter
	 */
	public function createRouter()
	{
		$router = new Nette\Application\MultiRouter;
		$router[] = new Kdyby\Application\AdminRouter;

		return $router;
	}



	/**
	 * @param string|NULL $file
	 * @return Nette\Config\Config
	 */
	public function loadConfig($file)
	{
		if ($file) {
			$file = realpath(Nette\Environment::expand($file));

			if (isset($this->configFiles[$file])) {
				$this->configFiles[$file][0] = $file;

			} else {
				$this->configFiles[$file][0] = array($file, TRUE, array());
			}
		}

		return parent::loadConfig($this->loadConfigs());
	}



	/**
	 * @return Nette\Config\Config
	 */
	public function loadConfigs()
	{
		$name = Environment::getName();
		$configs = array();

		// read and return according to actual environment name
		foreach ($this->configFiles as $file => $config) {
			$configs[$file] = Nette\Config\Config::fromFile(Nette\Environment::expand($config[0]), $config[1] ? $name : NULL);
		}

		$mergedConfig = array();
		foreach ($this->configFiles as $file => $config) {
			$appendConfig = array();

			$prefixed = &$appendConfig;
			foreach ($config[2] as $prefix) {
				$prefixed = &$prefixed[$prefix];
			}
			$prefixed = $configs[$file]->toArray();

			$mergedConfig = array_replace_recursive($mergedConfig, $appendConfig);
		}

		return new Nette\Config\Config($mergedConfig);
	}



	/**
	 * Get initial instance of context.
	 * @param array $services
	 * @return Kdyby\Injection\IServiceContainer
	 */
	public function createContext(array $services = array())
	{
		$container = new Kdyby\Injection\ServiceContainer();
		$container->setServiceBuilder(new Kdyby\Injection\ServiceBuilder($container));

		foreach ($services as $name => $configuration) {
			$container->addService($name, $configuration);
		}

		foreach ($this->defaultServices as $name => $service) {
			$container->addService($name, $service);
		}

		return $container;
	}



	/**
	 * @param Nette\Web\Session $session
	 */
	public function setupSession(Nette\Web\Session $session)
	{
		// setup session
		if (!$session->isStarted()) {
			if (!Environment::isConsole()){
				$domain = Kdyby\Web\HttpHelpers::getDomain()->domain;
				$session->setCookieParams('/', '.'.$domain);
			}
			$session->setExpiration(Nette\Tools::YEAR);
			if (!$session->exists()) {
				$session->start();
			}
		}
	}



	public function setupTemplateMacros()
	{
		// templates
		Kdyby\Templates\KdybyMacros::register();
	}



	/**
	 * @return Nette\Web\IUser
	 */
	public static function createIUser()
	{
		return $user = new Kdyby\Security\User;
	}



	/**
	 * @return Kdyby\ConfigHooks
	 */
	public static function createConfigHooks()
	{
		$hooks = self::$configHooks;

		$kdybyConfig = Environment::getConfig('Kdyby');
		if (isset($kdybyConfig['Core'])) {
			$hooks = $kdybyConfig['Core']->toArray() + $hooks;
		}

		return $configHooks = new Kdyby\ConfigHooks($hooks);
	}



	/**
	 * @return Nette\Caching\FileJournal
	 */
	public static function createCacheStorage()
	{
		$dir = Kdyby\Tools\FileSystem::prepareWritableDir('%varDir%/cache');

		$journal = Environment::getService('Nette\\Caching\\ICacheJournal');
		return new Kdyby\Caching\FileStorage($dir, $journal);
	}



	/**
	 * @return Nette\Caching\MemcachedStorage
	 */
	public static function createMemcacheStorage($options)
	{
		$config = Environment::getConfig('memcache');

		$journal = Environment::getService('Nette\Caching\IMemcacheJournal');
		return new Nette\Caching\MemcachedStorage($config['host'], $config['port'], $options['prefix'], $journal);
	}



	/**
	 * @return Nette\Caching\ICacheJournal
	 */
	public static function createMemcacheJournal()
	{
		/*if (Nette\Caching\SqliteJournal::isAvailable()) {
			return new Nette\Caching\SqliteJournal(Environment::getVariable('tempDir') . '/cachejournal.db');
		} else*/ {
			$dir = Kdyby\Tools\FileSystem::prepareWritableDir('%tempDir%/memcache');
			return new Nette\Caching\FileJournal($dir);
		}
	}

}