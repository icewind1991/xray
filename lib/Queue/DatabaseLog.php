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

use OCP\AppFramework\Utility\ITimeFactory;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IRequest;

class DatabaseLog {
	/** @var IDBConnection */
	private $connection;
	/** @var IRequest */
	private $request;
	/** @var ITimeFactory */
	private $timeFactory;

	public function __construct(IDBConnection $connection, IRequest $request, ITimeFactory $timeFactory) {
		$this->connection = $connection;
		$this->request = $request;
		$this->timeFactory = $timeFactory;
	}

	public function push($item) {
		$query = $this->connection->getQueryBuilder();

		$query->insert('xray_log')
			->values([
				'data' => $query->createNamedParameter(json_encode($item)),
				'request_id' => $query->createNamedParameter($this->request->getId()),
				'timestamp' => $query->createNamedParameter($this->timeFactory->getTime(), IQueryBuilder::PARAM_INT)
			]);
		$query->execute();
	}

	public function getHistory($before = 0, $count = 500) {
		$query = $this->connection->getQueryBuilder();

		$query->select('data')
			->from('xray_log')
			->orderBy('id', 'DESC')
			->setMaxResults($count);

		if ($before) {
			$query->where($query->expr()->lt('id', $query->createNamedParameter($before, IQueryBuilder::PARAM_INT)));
		}

		$result = $query->execute();

		$items = $result->fetchAll(\PDO::FETCH_COLUMN);
		return array_map(function ($item) {
			return json_decode($item, true);
		}, $items);
	}
}
