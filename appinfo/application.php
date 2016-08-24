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

namespace OCA\XRay\AppInfo;

use OCA\XRay\Controller\PageController;
use OCA\XRay\Queue\IQueue;
use OCA\XRay\Queue\RedisQueue;
use OCA\XRay\Source\Injector;
use OCA\XRay\Source\Transmitter;
use OCP\AppFramework\App;
use OC\AppFramework\Utility\SimpleContainer;

class Application extends App {
	public function __construct(array $urlParams = array()) {
		parent::__construct('xray', $urlParams);

		$container = $this->getContainer();
		/** @var \OC\Server $server */
		$server = $container->getServer();

		$container->registerService('Injector', function (SimpleContainer $c) use ($server) {
			return new Injector($server);
		});

		$container->registerService('Queue', function (SimpleContainer $c) use ($server) {
			$redis = $server->getGetRedisFactory()->getInstance();
			return new RedisQueue($redis, 'xray', 512);
		});

		$container->registerService('Transmitter', function (SimpleContainer $c) use ($server) {
			return new Transmitter($this->getInjector(), $this->getQueue(), $server->getRequest(), $server->getQueryLogger());
		});

		$container->registerService('PageController', function (SimpleContainer $c) use ($server) {
			/** @var \OC\Server $server */
			return new PageController(
				$c->query('AppName'),
				$server->getRequest(),
				$this->getQueue()
			);
		});
	}

	/**
	 * @return Injector
	 */
	private function getInjector() {
		$container = $this->getContainer();
		return $container->query('Injector');
	}

	/**
	 * @return IQueue
	 */
	private function getQueue() {
		$container = $this->getContainer();
		return $container->query('Queue');
	}

	/**
	 * @return Transmitter
	 */
	private function getTransmitter() {
		$container = $this->getContainer();
		return $container->query('Transmitter');
	}

	public function registerSources() {
		if (!\OC::$CLI) {
			$transmitter = $this->getTransmitter();
			$transmitter->startRequest();

			register_shutdown_function([$transmitter, 'endRequest']);

			\OCP\Util::connectHook('OC_Filesystem', 'preSetup', $transmitter, 'transmitLocks');
		}
	}
}
