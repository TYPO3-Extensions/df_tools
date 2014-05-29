<?php

namespace SGalinski\DfTools\Tests\Unit\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class ControllerTestCase
 */
abstract class ControllerTestCase extends UnitTestCase {
	/**
	 * @var object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * Adds a mocked call to persist all
	 *
	 * @return void
	 */
	protected function addMockedCallToPersistAll() {
		$class = 'TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager';
		$mockPersistenceManager = $this->getMock($class, array('persistAll'));
		$mockObjectManager = $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager', array('get'));

		/** @noinspection PhpUndefinedMethodInspection */
		$mockObjectManager->expects($this->once())->method('get')
			->will($this->returnValue($mockPersistenceManager));
		$mockPersistenceManager->expects($this->once())->method('persistAll');

		$this->fixture->_set('objectManager', $mockObjectManager);
	}
}

?>