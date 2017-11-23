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

namespace OCA\XRay\Source;

use OC\Files\Storage\Wrapper\Wrapper;
use OCP\Files\Storage\IStorage;
use OCP\Lock\ILockingProvider;
use OCP\Lock\LockedException;

/**
 * Allows listening to lock changes
 */
class StorageWrapper extends Wrapper {
	const LOCK_ACQUIRE = 1;
	const LOCK_RELEASE = 2;
	const LOCK_CHANGE = 3;

	/** @var callable */
	private $lockCallback;

	/** @var callable */
	private $storageCallback;

	/** @var mixed */
	private $mountPoint;

	/** @var callable */
	private $cacheCallback;

	/**
	 * @param array $arguments
	 */
	public function __construct($arguments) {
		$this->storageCallback = $arguments['storageCallback'];
		$this->lockCallback = $arguments['lockCallback'];
		$this->cacheCallback = $arguments['cacheCallback'];
		$this->mountPoint = $arguments['mountpoint'];
		parent::__construct($arguments);
	}

	private function getStack() {
		$stack = debug_backtrace();
		$stack = array_slice($stack, 3);
		$rootLength = strlen(\OC::$SERVERROOT);
		return array_map(function ($trace) use ($rootLength) {
			return [
				'file' => isset($trace['file']) ? substr($trace['file'], $rootLength) : '',
				'line' => isset($trace['line']) ? $trace['line'] : 0,
				'class' => isset($trace['class']) ? $trace['class'] : '',
				'function' => $trace['function'],
				'type' => isset($trace['type']) ? $trace['type'] : ''
			];
		}, $stack);
	}

	private function emitStorage($operation, $path, $duration) {
		$callback = $this->storageCallback;
		$callback($operation, $this->mountPoint . $path, $this->getStack(), $duration);
	}

	private function wrapMethod($operation, $path, array $arguments) {
		$start = microtime(true);
		$result = call_user_func_array(['parent', $operation], $arguments);
		$end = microtime(true);
		$this->emitStorage($operation, $path, $end - $start);
		return $result;

	}

	public function mkdir($path) {
		return $this->wrapMethod('mkdir', $path, [$path]);
	}

	public function rmdir($path) {
		return $this->wrapMethod('rmdir', $path, [$path]);
	}

	public function opendir($path) {
		return $this->wrapMethod('opendir', $path, [$path]);
	}

	public function is_dir($path) {
		return $this->wrapMethod('is_dir', $path, [$path]);
	}

	public function is_file($path) {
		return $this->wrapMethod('is_file', $path, [$path]);
	}

	public function stat($path) {
		return $this->wrapMethod('stat', $path, [$path]);
	}

	/**
	 * see http://php.net/manual/en/function.filetype.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function filetype($path) {
		return $this->wrapMethod('filetype', $path, [$path]);
	}

	public function filesize($path) {
		return $this->wrapMethod('filesize', $path, [$path]);
	}

	public function isCreatable($path) {
		return $this->wrapMethod('isCreatable', $path, [$path]);
	}

	public function isReadable($path) {
		return $this->wrapMethod('isReadable', $path, [$path]);
	}

	public function isUpdatable($path) {
		return $this->wrapMethod('isUpdatable', $path, [$path]);
	}

	public function isDeletable($path) {
		return $this->wrapMethod('isDeletable', $path, [$path]);
	}

	public function isSharable($path) {
		return $this->wrapMethod('isSharable', $path, [$path]);
	}

	public function getPermissions($path) {
		return $this->wrapMethod('getPermissions', $path, [$path]);
	}

	public function file_exists($path) {
		return $this->wrapMethod('file_exists', $path, [$path]);
	}

	public function filemtime($path) {
		return $this->wrapMethod('filemtime', $path, [$path]);
	}

	public function file_get_contents($path) {
		return $this->wrapMethod('file_get_contents', $path, [$path]);
	}

	public function file_put_contents($path, $data) {
		return $this->wrapMethod('file_put_contents', $path, [$path, $data]);
	}

	public function unlink($path) {
		return $this->wrapMethod('unlink', $path, [$path]);
	}

	public function rename($path1, $path2) {
		return $this->wrapMethod('rename', $path1, [$path1, $path2]);
	}

	public function copy($path1, $path2) {
		return $this->wrapMethod('copy', $path1, [$path1, $path2]);
	}

	public function fopen($path, $mode) {
		return $this->wrapMethod('fopen', $path, [$path, $mode]);
	}

	public function getMimeType($path) {
		return $this->wrapMethod('getMimeType', $path, [$path]);
	}

	public function hash($type, $path, $raw = false) {
		return $this->wrapMethod('hash', $path, [$type, $path, $raw]);
	}

	public function free_space($path) {
		return $this->wrapMethod('free_space', $path, [$path]);
	}

	public function search($query) {
		return $this->wrapMethod('search', $query, [$query]);
	}

	public function touch($path, $mtime = null) {
		return $this->wrapMethod('touch', $path, [$path, $mtime]);
	}

	public function getLocalFile($path) {
		return $this->wrapMethod('getLocalFile', $path, [$path]);
	}

	public function hasUpdated($path, $time) {
		return $this->wrapMethod('hasUpdated', $path, [$path, $mtime]);
	}

	public function getETag($path) {
		return $this->wrapMethod('getETag', $path, [$path]);
	}

	public function test() {
		return parent::test();
	}

	public function copyFromStorage(IStorage $sourceStorage, $sourceInternalPath, $targetInternalPath) {
		return $this->wrapMethod('copyFromStorage', $sourceInternalPath, [$sourceStorage, $sourceInternalPath, $targetInternalPath]);
	}

	public function moveFromStorage(IStorage $sourceStorage, $sourceInternalPath, $targetInternalPath) {
		return $this->wrapMethod('moveFromStorage', $sourceInternalPath, [$sourceStorage, $sourceInternalPath, $targetInternalPath]);
	}

	public function getCache($path = '', $storage = null) {
		$parentCache = parent::getCache($path, $storage);
		return new CacheWrapper($parentCache, $this->mountPoint, $this->cacheCallback);
	}

	public function getMetaData($path) {
		return $this->wrapMethod('getMetaData', $path, [$path]);
	}

	public function acquireLock($path, $type, ILockingProvider $provider) {
		$callback = $this->lockCallback;
		try {
			$provider->acquireLock('files/' . md5($this->getId() . '::' . trim($path, '/')), $type);
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, true, $this->getStack());
		} catch (LockedException $e) {
			$callback(self::LOCK_ACQUIRE, $this->mountPoint . $path, $type, false, $this->getStack());
			throw $e;
		}
	}

	public function releaseLock($path, $type, ILockingProvider $provider) {
		$callback = $this->lockCallback;
		try {
			$provider->releaseLock('files/' . md5($this->getId() . '::' . trim($path, '/')), $type);
			$callback(self::LOCK_RELEASE, $this->mountPoint . $path, $type, true, $this->getStack());
		} catch (LockedException $e) {
			$callback(self::LOCK_RELEASE, $this->mountPoint . $path, $type, false, $this->getStack());
			throw $e;
		}
	}

	public function changeLock($path, $type, ILockingProvider $provider) {
		$callback = $this->lockCallback;
		try {
			$provider->changeLock('files/' . md5($this->getId() . '::' . trim($path, '/')), $type);
			$callback(self::LOCK_CHANGE, $this->mountPoint . $path, $type, true, $this->getStack());
		} catch (LockedException $e) {
			$callback(self::LOCK_CHANGE, $this->mountPoint . $path, $type, false, $this->getStack());
			throw $e;
		}
	}
}
