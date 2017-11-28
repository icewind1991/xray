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

use OC\AppFramework\Utility\TimeFactory;
use OCA\XRay\Queue\DatabaseLog;
use OCA\XRay\Queue\IQueue;
use OCA\XRay\Queue\MultiPush;
use OCA\XRay\Queue\NullQueue;
use OCA\XRay\Queue\RedisQueue;
use OCA\XRay\Source\Injector;
use OCA\XRay\Source\Transmitter;
use OCP\AppFramework\App;
use OC\AppFramework\Utility\SimpleContainer;

class Application extends App {
	public function __construct(array $urlParams = []) {
		parent::__construct('xray', $urlParams);

		$container = $this->getContainer();
		/** @var \OC\Server $server */
		$server = $container->getServer();

		$container->registerService(Injector::class, function (SimpleContainer $c) use ($server) {
			return new Injector($server);
		});

		$container->registerService(IQueue::class, function (SimpleContainer $c) use ($server) {
			$redisFactory = $server->getGetRedisFactory();
			if ($redisFactory->isAvailable()) {
				return new RedisQueue($redisFactory->getInstance(), 'xray', 512);
			} else {
				return new NullQueue();
			}
		});

		$container->registerService(Transmitter::class, function (SimpleContainer $c) use ($server) {
			return new Transmitter($this->getInjector(), new MultiPush([
				$this->getQueue(),
				$this->getLog()
			]), $server->getRequest(), $server->getQueryLogger());
		});

		$container->registerService(DatabaseLog::class, function (SimpleContainer $c) use ($server) {
			return new DatabaseLog($server->getDatabaseConnection(), $server->getRequest(), new TimeFactory());
		});
	}

	/**
	 * @return Injector
	 */
	private function getInjector() {
		$container = $this->getContainer();
		return $container->query(Injector::class);
	}

	/**
	 * @return IQueue
	 */
	private function getQueue() {
		$container = $this->getContainer();
		return $container->query(IQueue::class);
	}

	/**
	 * @return Transmitter
	 */
	private function getTransmitter() {
		$container = $this->getContainer();
		return $container->query(Transmitter::class);
	}

	/**
	 * @return DatabaseLog
	 */
	private function getLog() {
		$container = $this->getContainer();
		return $container->query(DatabaseLog::class);
	}

	public function registerSources() {
		if (!\OC::$CLI) {
			$request = $this->getContainer()->getServer()->getRequest();
			// dont pollute the logs with our own requests
			if (strpos($request->getRequestUri(), 'apps/xray/') !== false) {
				return;
			}
			$transmitter = $this->getTransmitter();
			$transmitter->startRequest();

			register_shutdown_function([$transmitter, 'endRequest']);

			\OCP\Util::connectHook('OC_Filesystem', 'preSetup', $transmitter, 'transmitLocks');
		}
	}
}
