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

namespace OCA\XRay\Queue;

class RedisQueue implements IQueue {
	/** @var int */
	private $size;

	/** @var \Redis */
	private $redis;

	/** @var string */
	private $channel;

	/**
	 * @param \Redis $redis
	 * @param string $channel
	 * @param int $size
	 */
	public function __construct(\Redis $redis, $channel, $size = 256) {
		$this->redis = $redis;
		$this->channel = $channel;
		$this->size = $size;
	}

	/**
	 * Listen for new incoming items
	 *
	 * @param callable $callback
	 */
	public function listen(callable $callback) {
		$this->redis->subscribe([$this->channel], function ($redis, $channel, $message) use ($callback) {
			$item = json_decode($message, true);
			$callback($item);
		});
	}

	/**
	 * Retrieve past items from the queue
	 *
	 * @param int $length (optional) the maximum amount of items to be retrieved
	 * @return array
	 */
	public function retrieveHistory($length = -1) {
		$key = $this->channel . '_history';
		if ($length === -1) {
			$length = $this->size;
		} else {
			$length = max($length, $this->size);
		}
		$items = $this->redis->lRange($key, 0, $length);
		return array_map(function ($item) {
			return json_decode($item, true);
		}, array_reverse($items));
	}

	/**
	 * Push a new item on the queue
	 *
	 * @param mixed $item a json serializable object to push on the queue
	 */
	public function push($item) {
		$message = json_encode($item);
		$key = $this->channel . '_history';
		$this->redis->publish($this->channel, $message);
		$this->redis->lPush($key, $message);
		$this->redis->lTrim($key, 0, $this->size);
	}


}
