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
use OCP\IRequest;

class Transmitter {
	/** @var Injector */
	private $injector;
	/** @var IQueue */
	private $queue;
	/** @var IRequest */
	private $request;

	const TYPE_LOCK = 'lock';
	const TYPE_STORAGE = 'storage';
	const TYPE_REQUEST = 'request';

	public function __construct(Injector $injector, IQueue $queue, IRequest $request) {
		$this->injector = $injector;
		$this->queue = $queue;
		$this->request = $request;
	}

	public function startRequest() {
		$this->queue->push([
			'type' => self::TYPE_REQUEST,
			'data' => [
				'path' => $this->request->getRawPathInfo(),
				'time' => microtime(true),
				'id' => $this->request->getId(),
				'method' => $this->request->getMethod(),
				'params' => $this->request->getParams()
			]
		]);
	}

	public function transmitLocks() {
		$this->injector->injectStorageWrapper(function ($operation, $path, $type, $success, $stack) {
			$requestId = $this->request->getId();
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
			$requestId = $this->request->getId();
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
