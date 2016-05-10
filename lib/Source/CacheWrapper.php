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

use OCP\Files\Cache\ICache;
use OCP\Files\Cache\ICacheEntry;

class CacheWrapper extends \OC\Files\Cache\Wrapper\CacheWrapper {
	private $mountPoint;
	private $cacheCallback;

	/**
	 * @param \OCP\Files\Cache\ICache $cache
	 * @param string $mountPoint
	 * @param callable $cacheCallback
	 */
	public function __construct($cache, $mountPoint, callable $cacheCallback) {
		parent::__construct($cache);
		$this->mountPoint = $mountPoint;
		$this->cacheCallback = $cacheCallback;
	}

	private function getStack() {
		$stack = debug_backtrace();
		$stack = array_slice($stack, 2);
		$rootLength = strlen(\OC::$SERVERROOT);
		return array_map(function ($trace) use ($rootLength) {
			return [
				'file' => substr($trace['file'], $rootLength),
				'line' => $trace['line'],
				'class' => $trace['class'],
				'function' => $trace['function'],
				'type' => $trace['type']
			];
		}, $stack);
	}

	private function emitCache($operation, $path) {
		$callback = $this->cacheCallback;
		$callback($operation, $this->mountPoint . $path, $this->getStack());
	}


	private function emitCacheTwoPaths($operation, $path1, $path2) {
		$callback = $this->cacheCallback;
		$callback($operation, $this->mountPoint . $path1, $this->mountPoint . $path2, $this->getStack());
	}

	/**
	 * get the stored metadata of a file or folder
	 *
	 * @param string|int $file
	 * @return ICacheEntry|false
	 */
	public function get($file) {
		$this->emitCache('get', $file);
		return parent::get($file);
	}

	/**
	 * get the metadata of all files stored in $folder
	 *
	 * @param string $folder
	 * @return ICacheEntry[]
	 */
	public function getFolderContents($folder) {
		$this->emitCache('getFolderContents', $folder);
		return parent::getFolderContents($folder);
	}

	/**
	 * get the metadata of all files stored in $folder
	 *
	 * @param int $fileId the file id of the folder
	 * @return array
	 */
	public function getFolderContentsById($fileId) {
		$this->emitCache('getFolderContentsById', $fileId);
		return parent::getFolderContentsById($fileId);
	}

	/**
	 * insert or update meta data for a file or folder
	 *
	 * @param string $file
	 * @param array $data
	 *
	 * @return int file id
	 * @throws \RuntimeException
	 */
	public function put($file, array $data) {
		$this->emitCache('put', $file);
		return parent::put($file, $data);
	}

	/**
	 * insert meta data for a new file or folder
	 *
	 * @param string $file
	 * @param array $data
	 *
	 * @return int file id
	 * @throws \RuntimeException
	 */
	public function insert($file, array $data) {
		$this->emitCache('insert', $file);
		return parent::insert($file, $data);
	}

	/**
	 * update the metadata in the cache
	 *
	 * @param int $id
	 * @param array $data
	 */
	public function update($id, array $data) {
		$this->emitCache('update', $id);
		parent::update($id, $data);
	}

	/**
	 * get the file id for a file
	 *
	 * @param string $file
	 * @return int
	 */
	public function getId($file) {
		$this->emitCache('getId', $file);
		return parent::getId($file);
	}

	/**
	 * get the id of the parent folder of a file
	 *
	 * @param string $file
	 * @return int
	 */
	public function getParentId($file) {
		$this->emitCache('getParentId', $file);
		return parent::getParentId($file);
	}

	/**
	 * check if a file is available in the cache
	 *
	 * @param string $file
	 * @return bool
	 */
	public function inCache($file) {
		$this->emitCache('inCache', $file);
		return parent::inCache($file);
	}

	/**
	 * remove a file or folder from the cache
	 *
	 * @param string $file
	 */
	public function remove($file) {
		$this->emitCache('put', $file);
		parent::remove($file);
	}

	/**
	 * Move a file or folder in the cache
	 *
	 * @param string $source
	 * @param string $target
	 */
	public function move($source, $target) {
		$this->emitCacheTwoPaths('move', $source, $target);
		parent::move($source, $target);
	}

	public function moveFromCache(ICache $sourceCache, $sourcePath, $targetPath) {
		$this->emitCacheTwoPaths('moveFromCache', $sourcePath, $targetPath);
		parent::moveFromCache($sourceCache, $sourcePath, $targetPath);
	}

	/**
	 * @param string $file
	 *
	 * @return int Cache::NOT_FOUND, Cache::PARTIAL, Cache::SHALLOW or Cache::COMPLETE
	 */
	public function getStatus($file) {
		$this->emitCache('getStatus', $file);
		return parent::getStatus($file);
	}

	/**
	 * search for files matching $pattern
	 *
	 * @param string $pattern
	 * @return ICacheEntry[] an array of file data
	 */
	public function search($pattern) {
		$this->emitCache('search', $pattern);
		return parent::search($pattern);
	}

	/**
	 * search for files by mimetype
	 *
	 * @param string $mimetype
	 * @return ICacheEntry[]
	 */
	public function searchByMime($mimetype) {
		$this->emitCache('searchByMime', $mimetype);
		return parent::searchByMime($mimetype);
	}
	/**
	 * get the path of a file on this storage by it's id
	 *
	 * @param int $id
	 * @return string|null
	 */
	public function getPathById($id) {
		$this->emitCache('getPathById', $id);
		return parent::getPathById($id);
	}
}
