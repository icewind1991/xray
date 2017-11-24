<?php
/**
 * @copyright Copyright (c) 2017 Robin Appelman <robin@icewind.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\XRay\Queue;


class MultiPush implements IQueue {
	/** @var (IQueue|DatabaseLog)[] */
	private $backends;

	public function __construct(array $backends) {
		$this->backends = $backends;
	}

	public function push($item) {
		foreach ($this->backends as $backend) {
			/** @var IQueue|DatabaseLog $backend */
			$backend->push($item);
		}
	}

	public function listen(callable $callback) {
		throw new \Exception();
	}

	public function retrieveHistory($length = -1) {
		throw new \Exception();
	}
}
