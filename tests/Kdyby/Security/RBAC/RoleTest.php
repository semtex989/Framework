<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008, 2011 Filip Procházka (filip.prochazka@kdyby.org)
 *
 * @license http://www.kdyby.org/license
 */

namespace Kdyby\Testing\Security\RBAC;

use Kdyby;
use Kdyby\Security\RBAC\Role;
use Kdyby\Security\RBAC\Division;
use Nette;



/**
 * @author Filip Procházka
 */
class RoleTest extends Kdyby\Testing\TestCase
{

	/** @var Role */
	private $role;

	/** @var Division */
	private $division;


	public function setUp()
	{
		$this->division = new Division('administration');
		$this->role = new Role('admin', $this->division);
	}



	public function testImplementsIRole()
	{
		$this->assertInstanceOf('Nette\Security\IRole', $this->role);
	}



	public function testSettingName()
	{
		$this->assertEquals('admin', $this->role->getName());
		$this->assertEquals('', $this->role->getRoleId());
	}



	public function testSettingDescription()
	{
		$this->role->setDescription("The God");
		$this->assertEquals("The God", $this->role->getDescription());
	}



	public function testProvidesDivision()
	{
		$this->assertSame($this->division, $this->role->getDivision());
	}

}