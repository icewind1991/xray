<?php
/**
 * @author Robin Appelman <icewind@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\XRay\Source\Lock;

use OC\Files\Storage\Wrapper\Wrapper;
use OCP\Lock\ILockingProvider;
use OCP\Lock\LockedException;

/**
 * Allows listening to lock changes
 */
class LockWrapper extends Wrapper {
	const LOCK_ACQUIRE = 1;
	const LOCK_RELEASE = 2;
	const LOCK_CHANGE = 3;

	/** @var callable */
	private $callback;

	/** @var mixed */
	private $mountPoint;

	/**
	 * @param array $arguments
	 */
	public function __construct($arguments) {
		$this->callback = $arguments['callback'];
		$this->mountPoint = $arguments['mountpoint'];
		parent::__construct($arguments);
	}

	/**
	 * @param string $path
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 * @throws \OCP\Lock\LockedException
	 */
	public function acquireLock($path, $type, ILockingProvider $provider) {
		$callback = $this->callback;
		try {
			$provider->acquireLock('files/' . md5($this->getId() . '::' . trim($path, '/')), $type);
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, true);
		} catch (LockedException $e) {
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, false);
			throw $e;
		}
	}

	/**
	 * @param string $path
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 */
	public function releaseLock($path, $type, ILockingProvider $provider) {
		$callback = $this->callback;
		try {
			$provider->releaseLock('files/' . md5($this->getId() . '::' . trim($path, '/')), $type);
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, true);
		} catch (LockedException $e) {
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, false);
			throw $e;
		}
	}

	/**
	 * @param string $path
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 */
	public function changeLock($path, $type, ILockingProvider $provider) {
		$callback = $this->callback;
		try {
			$provider->changeLock('files/' . md5($this->getId() . '::' . trim($path, '/')), $type);
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, true);
		} catch (LockedException $e) {
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, false);
			throw $e;
		}
	}

}
