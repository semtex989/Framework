<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Components\Grinder\Actions;

use Kdyby;
use Nette;
use Nette\Application\UI\Link;
use Nette\Utils\Html;



/**
 * @author Filip Procházka
 */
class LinkAction extends BaseAction
{

	/** @var boolean */
	public $handlerPassEntity = FALSE;

	/** @var callback */
	private $handler;

	/** @var string|callback */
	private $link;

	/** @var Html */
	private $image;

	/** @var array */
	private $mask = array();



	public function __construct()
	{
		parent::__construct();

		$this->image = Html::el('img');
	}



	/**
	 * @param Link $link
	 * @param array $mask
	 * @return LinkAction
	 */
	public function setLink($link, array $mask = array())
	{
		if (!is_callable($link) && !$link instanceof Link) {
			throw new \InvalidArgumentException("Link must be callable or instance of Nette\\Application\\Link");
		}

		$this->mask = $mask;
		$this->link = $link;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getLink()
	{
		$record = $this->getGrid()->getCurrentRecord();

		// custom link
		if ($this->link) {
			$args = array();
			foreach ($this->mask as $argName => $paramName) {
				if (method_exists($record, $method = 'get' . ucfirst($paramName))) {
					$args[$argName] = $record->$method();

				} elseif (isset($record->$paramName)) {
					$args[$argName] = $record->$paramName;

				} else {
					throw new Nette\InvalidStateException("Record " . (is_object($record) ? "of entity " . get_class($record) . ' ' : NULL) . "has no parameter named '" . $paramName . "'.");
				}
			}

			if (is_callable($this->link)) {
				return call_user_func($this->link, $args);
			}

			$link = clone $this->link;
			foreach ($args as $argName => $value) {
				$link->setParam($argName, $value);
			}
			return (string)$link;
		}

		return $this->getGrid()->link('action!', array(
			'action' => $this->name,
			'token' => $this->getGrid()->getSecurityToken(),
			'id' => $this->getGrid()->getModel()->getUniqueId($record) ?: NULL,
		));
	}



	/**
	 * Get handler
	 * @return callback
	 */
	public function getHandler()
	{
		return $this->handler;
	}



	/**
	 * Set handler
	 * @param callback handler
	 * @return LinkAction
	 */
	public function setHandler($handler)
	{
		$handler = callback($handler);
		if (!is_callable($handler)) {
			throw new \InvalidArgumentException("Handler is not callable.");
		}

		$this->handler = $handler;
		return $this;
	}



	/**
	 * @param int $id
	 */
	public function handleClick($id = NULL)
	{
		if (!is_callable($this->getHandler())) {
			throw new Nette\InvalidStateException("Handler for action '" . $this->name . "' is not callable.");
		}

		$id = $id ?: NULL;
		if ($this->handlerPassEntity === TRUE) {
			if (!$id) {
				throw new Nette\InvalidStateException("Missing argument 'id' in action '" . $this->name . "'.");
			}

			$id = $this->getGrid()->getModel()->getItemByUniqueId($id);
		}

		call_user_func($this->getHandler(), $this, $id);
	}



	/**
	 * @param string|callable|Html $image
	 * @return LinkAction
	 */
	public function setImage($image)
	{
		if (!is_string($image) && !is_callable($image)) {
			throw new Nette\InvalidArgumentException("Given image must be either path or callback.");
		}

		$this->image->src = $image;
		return $this;
	}



	/**
	 * @return string|Html|callable
	 */
	public function getImagePrototype()
	{
		return $this->image;
	}



	/**
	 * @return string|Html
	 */
	public function getImage()
	{
		if (!$this->image->src) {
			return NULL;
		}

		$image = clone $this->image;

		$expand = callback($this->getGrid()->getContext(), 'expand');
		$expand = function (Html $image) use ($expand) {
			$value = is_callable($image->src) ? call_user_func($image->src, $image) : $image->src;
			return $expand(substr_count($value, '%') ? $value : '%basePath%/' . $value);
		};

		$image->src = $expand($image);
		$image->alt = $this->getCaption();

		return $image;
	}



	/**
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
		$link = Html::el('a', array(
				'href' => $this->getLink()
			));

		if ($image = $this->getImage()) {
			$link->add($image);

		} else {
			$caption = $this->getCaption();
			$link->{$caption instanceof Html ? 'add' : 'setText'}($caption);
		}

		return $link;
	}

}