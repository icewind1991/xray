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

use OCA\XRay\Queue\IQueue;

class Transmitter {
	/** @var Injector */
	private $injector;
	/** @var IQueue */
	private $queue;

	const TYPE_LOCK = 'lock';
	const TYPE_STORAGE = 'storage';

	public function __construct(Injector $injector, IQueue $queue) {
		$this->injector = $injector;
		$this->queue = $queue;
	}

	public function transmitLocks() {
		$this->injector->injectStorageWrapper(function ($operation, $path, $type, $success, $stack) {
			$requestId = \OC::$server->getRequest()->getId();
			$this->queue->push([
				'type' => self::TYPE_LOCK,
				'data' => [
					'time' => microtime(true),
					'operation' => $operation,
					'path' => $path,
					'type' => $type,
					'success' => $success,
					'stack' => $stack,
					'request' => $requestId
				]
			]);
		}, function ($operation, $path, $stack) {
			$requestId = \OC::$server->getRequest()->getId();
			$this->queue->push([
				'type' => self::TYPE_STORAGE,
				'data' => [
					'time' => microtime(true),
					'operation' => $operation,
					'path' => $path,
					'stack' => $stack,
					'request' => $requestId
				]
			]);
		});
	}
}
