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

namespace OCA\XRay;

/**
 *
 * @todo Replace with a library
 */
class EventSource implements \OCP\IEventSource {

	/**
	 * @var bool
	 */
	private $started = false;

	protected function init() {
		if ($this->started) {
			return;
		}
		$this->started = true;

		// prevent php output buffering, caching and nginx buffering
		while (ob_get_level()) {
			ob_end_clean();
		}
		header('Cache-Control: no-cache');
		header('X-Accel-Buffering: no');
		header("Content-Type: text/event-stream");
		flush();
	}

	/**
	 * Sends a message to the client
	 *
	 * If only one parameter is given, a typeless message will be sent with that parameter as data
	 *
	 * @param string $type
	 * @param mixed $data
	 *
	 * @throws \BadMethodCallException
	 */
	public function send($type, $data = null) {
		if ($data && !preg_match('/^[A-Za-z0-9_]+$/', $type)) {
			throw new \BadMethodCallException('Type needs to be alphanumeric (' . $type . ')');
		}
		$this->init();
		if (is_null($data)) {
			$data = $type;
			$type = null;
		}

		if (!empty($type)) {
			echo 'event: ' . $type . PHP_EOL;
		}
		echo 'data: ' . json_encode($data) . PHP_EOL;

		echo PHP_EOL;
		flush();
	}

	/**
	 * Closes the connection of the event source
	 *
	 * It's best to let the client close the stream
	 */
	public function close() {
		$this->send(
			'__internal__', 'close'
		);
	}
}
