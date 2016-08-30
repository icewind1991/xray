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
		$stack = array_slice($stack, 2);
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

	private function emitStorage($operation, $path) {
		$callback = $this->storageCallback;
		$callback($operation, $this->mountPoint . $path, $this->getStack());
	}


	private function emitStorageTwoPaths($operation, $path1, $path2) {
		$callback = $this->storageCallback;
		$callback($operation, $this->mountPoint . $path1, $this->mountPoint . $path2, $this->getStack());
	}

	/**
	 * see http://php.net/manual/en/function.mkdir.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function mkdir($path) {
		$this->emitStorage('mkdir', $path);
		return parent::mkdir($path);
	}

	/**
	 * see http://php.net/manual/en/function.rmdir.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function rmdir($path) {
		$this->emitStorage('rmdir', $path);
		return parent::rmdir($path);
	}

	/**
	 * see http://php.net/manual/en/function.opendir.php
	 *
	 * @param string $path
	 * @return resource
	 */
	public function opendir($path) {
		$this->emitStorage('opendir', $path);
		return parent::opendir($path);
	}

	/**
	 * see http://php.net/manual/en/function.is_dir.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function is_dir($path) {
		$this->emitStorage('is_dir', $path);
		return parent::is_dir($path);
	}

	/**
	 * see http://php.net/manual/en/function.is_file.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function is_file($path) {
		$this->emitStorage('is_file', $path);
		return parent::is_file($path);
	}

	/**
	 * see http://php.net/manual/en/function.stat.php
	 * only the following keys are required in the result: size and mtime
	 *
	 * @param string $path
	 * @return array
	 */
	public function stat($path) {
		$this->emitStorage('stat', $path);
		return parent::stat($path);
	}

	/**
	 * see http://php.net/manual/en/function.filetype.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function filetype($path) {
		$this->emitStorage('filetype', $path);
		return parent::filetype($path);
	}

	/**
	 * see http://php.net/manual/en/function.filesize.php
	 * The result for filesize when called on a folder is required to be 0
	 *
	 * @param string $path
	 * @return int
	 */
	public function filesize($path) {
		$this->emitStorage('filesize', $path);
		return parent::filesize($path);
	}

	/**
	 * check if a file can be created in $path
	 *
	 * @param string $path
	 * @return bool
	 */
	public function isCreatable($path) {
		$this->emitStorage('isCreatable', $path);
		return parent::isCreatable($path);
	}

	/**
	 * check if a file can be read
	 *
	 * @param string $path
	 * @return bool
	 */
	public function isReadable($path) {
		$this->emitStorage('isReadable', $path);
		return parent::isReadable($path);
	}

	/**
	 * check if a file can be written to
	 *
	 * @param string $path
	 * @return bool
	 */
	public function isUpdatable($path) {
		$this->emitStorage('isUpdatable', $path);
		return parent::isUpdatable($path);
	}

	/**
	 * check if a file can be deleted
	 *
	 * @param string $path
	 * @return bool
	 */
	public function isDeletable($path) {
		$this->emitStorage('isDeletable', $path);
		return parent::isDeletable($path);
	}

	/**
	 * check if a file can be shared
	 *
	 * @param string $path
	 * @return bool
	 */
	public function isSharable($path) {
		$this->emitStorage('isSharable', $path);
		return parent::isSharable($path);
	}

	/**
	 * get the full permissions of a path.
	 * Should return a combination of the PERMISSION_ constants defined in lib/public/constants.php
	 *
	 * @param string $path
	 * @return int
	 */
	public function getPermissions($path) {
		$this->emitStorage('getPermissions', $path);
		return parent::getPermissions($path);
	}

	/**
	 * see http://php.net/manual/en/function.file_exists.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function file_exists($path) {
		$this->emitStorage('file_exists', $path);
		return parent::file_exists($path);
	}

	/**
	 * see http://php.net/manual/en/function.filemtime.php
	 *
	 * @param string $path
	 * @return int
	 */
	public function filemtime($path) {
		$this->emitStorage('filemtime', $path);
		return parent::filemtime($path);
	}

	/**
	 * see http://php.net/manual/en/function.file_get_contents.php
	 *
	 * @param string $path
	 * @return string
	 */
	public function file_get_contents($path) {
		$this->emitStorage('file_get_contents', $path);
		return parent::file_get_contents($path);
	}

	/**
	 * see http://php.net/manual/en/function.file_put_contents.php
	 *
	 * @param string $path
	 * @param string $data
	 * @return bool
	 */
	public function file_put_contents($path, $data) {
		$this->emitStorage('file_put_contents', $path);
		return parent::file_put_contents($path, $data);
	}

	/**
	 * see http://php.net/manual/en/function.unlink.php
	 *
	 * @param string $path
	 * @return bool
	 */
	public function unlink($path) {
		$this->emitStorage('unlink', $path);
		return parent::unlink($path);
	}

	/**
	 * see http://php.net/manual/en/function.rename.php
	 *
	 * @param string $path1
	 * @param string $path2
	 * @return bool
	 */
	public function rename($path1, $path2) {
		$this->emitStorageTwoPaths('rename', $path1, $path2);
		return parent::rename($path1, $path2);
	}

	/**
	 * see http://php.net/manual/en/function.copy.php
	 *
	 * @param string $path1
	 * @param string $path2
	 * @return bool
	 */
	public function copy($path1, $path2) {
		$this->emitStorageTwoPaths('copy', $path1, $path2);
		return parent::copy($path1, $path2);
	}

	/**
	 * see http://php.net/manual/en/function.fopen.php
	 *
	 * @param string $path
	 * @param string $mode
	 * @return resource
	 */
	public function fopen($path, $mode) {
		$this->emitStorage('fopen', $path);
		return parent::fopen($path, $mode);
	}

	/**
	 * get the mimetype for a file or folder
	 * The mimetype for a folder is required to be "httpd/unix-directory"
	 *
	 * @param string $path
	 * @return string
	 */
	public function getMimeType($path) {
		$this->emitStorage('getMimeType', $path);
		return parent::getMimeType($path);
	}

	/**
	 * see http://php.net/manual/en/function.hash.php
	 *
	 * @param string $type
	 * @param string $path
	 * @param bool $raw
	 * @return string
	 */
	public function hash($type, $path, $raw = false) {
		$this->emitStorage('hash', $path);
		return parent::hash($type, $path, $raw);
	}

	/**
	 * see http://php.net/manual/en/function.free_space.php
	 *
	 * @param string $path
	 * @return int
	 */
	public function free_space($path) {
		$this->emitStorage('free_space', $path);
		return parent::free_space($path);
	}

	/**
	 * search for occurrences of $query in file names
	 *
	 * @param string $query
	 * @return array
	 */
	public function search($query) {
		$this->emitStorage('search', $path);
		return parent::search($query);
	}

	/**
	 * see http://php.net/manual/en/function.touch.php
	 * If the backend does not support the operation, false should be returned
	 *
	 * @param string $path
	 * @param int $mtime
	 * @return bool
	 */
	public function touch($path, $mtime = null) {
		$this->emitStorage('touch', $path);
		return parent::touch($path, $mtime);
	}

	/**
	 * get the path to a local version of the file.
	 * The local version of the file can be temporary and doesn't have to be persistent across requests
	 *
	 * @param string $path
	 * @return string
	 */
	public function getLocalFile($path) {
		$this->emitStorage('getLocalFile', $path);
		return parent::getLocalFile($path);
	}

	/**
	 * check if a file or folder has been updated since $time
	 *
	 * @param string $path
	 * @param int $time
	 * @return bool
	 *
	 * hasUpdated for folders should return at least true if a file inside the folder is add, removed or renamed.
	 * returning true for other changes in the folder is optional
	 */
	public function hasUpdated($path, $time) {
		$this->emitStorage('hasUpdated', $path);
		return parent::hasUpdated($path, $time);
	}

	/**
	 * get the ETag for a file or folder
	 *
	 * @param string $path
	 * @return string
	 */
	public function getETag($path) {
		$this->emitStorage('getETag', $path);
		return parent::getETag($path);
	}

	/**
	 * Returns true
	 *
	 * @return true
	 */
	public function test() {
		return parent::test();
	}

	/**
	 * @param \OCP\Files\Storage $sourceStorage
	 * @param string $sourceInternalPath
	 * @param string $targetInternalPath
	 * @return bool
	 */
	public function copyFromStorage(\OCP\Files\Storage $sourceStorage, $sourceInternalPath, $targetInternalPath) {
		$this->emitStorageTwoPaths('copyFromStorage', $sourceInternalPath, $targetInternalPath);
		return parent::copyFromStorage($sourceStorage, $sourceInternalPath, $targetInternalPath);
	}

	/**
	 * @param \OCP\Files\Storage $sourceStorage
	 * @param string $sourceInternalPath
	 * @param string $targetInternalPath
	 * @return bool
	 */
	public function moveFromStorage(\OCP\Files\Storage $sourceStorage, $sourceInternalPath, $targetInternalPath) {
		$this->emitStorageTwoPaths('moveFromStorage', $sourceInternalPath, $targetInternalPath);
		return parent::moveFromStorage($sourceStorage, $sourceInternalPath, $targetInternalPath);
	}

	public function getCache($path = '', $storage = null) {
		$parentCache = parent::getCache($path, $storage);
		return new CacheWrapper($parentCache, $this->mountPoint, $this->cacheCallback);
	}

	/**
	 * @param string $path
	 * @return array
	 */
	public function getMetaData($path) {
		$this->emitStorage('getMetaData', $path);
		return parent::getMetaData($path);
	}


	/**
	 * @param string $path
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 * @throws \OCP\Lock\LockedException
	 */
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

	/**
	 * @param string $path
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 */
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

	/**
	 * @param string $path
	 * @param int $type \OCP\Lock\ILockingProvider::LOCK_SHARED or \OCP\Lock\ILockingProvider::LOCK_EXCLUSIVE
	 * @param \OCP\Lock\ILockingProvider $provider
	 */
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
