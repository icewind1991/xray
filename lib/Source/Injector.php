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

use OC\Files\Filesystem;
use OC\Server;
use OCA\XRay\Source\Lock\LockWrapper;
use OCP\Files\Storage\IStorage;

class Injector {
	/** @var  Server */
	private $server;

	/**
	 * Injector constructor.
	 *
	 * @param Server $server
	 */
	public function __construct(Server $server) {
		$this->server = $server;
	}

	public function injectLock(callable $callback) {
		Filesystem::addStorageWrapper('xray_lock', function ($mountPoint, IStorage $storage) use ($callback) {
			return new LockWrapper([
				'storage' => $storage,
				'mountpoint' => $mountPoint,
				'callback' => $callback
			]);
		}, 99999); // always apply first
	}
}
