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
	private $registeredRequest = false;
	private $requestNumericId;

	public function __construct(IDBConnection $connection, IRequest $request, ITimeFactory $timeFactory) {
		$this->connection = $connection;
		$this->request = $request;
		$this->timeFactory = $timeFactory;
	}

	public function push($item) {
		$query = $this->connection->getQueryBuilder();

		if (!$this->registeredRequest) {
			$this->registeredRequest = true;
			$this->registerRequest();
		}
		$query->insert('xray_log')
			->values([
				'data' => $query->createNamedParameter(json_encode($item)),
				'request_id' => $query->createNamedParameter($this->request->getId()),
				'request_numeric_id' => $query->createNamedParameter($this->requestNumericId, IQueryBuilder::PARAM_INT),
				'timestamp' => $query->createNamedParameter($this->timeFactory->getTime(), IQueryBuilder::PARAM_INT)
			]);
		$query->execute();
	}

	private function registerRequest() {
		$query = $this->connection->getQueryBuilder();

		$query->insert('xray_requests')
			->values([
				'request_id' => $query->createNamedParameter($this->request->getId()),
				'timestamp' => $query->createNamedParameter($this->timeFactory->getTime(), IQueryBuilder::PARAM_INT)
			]);
		$query->execute();

		$this->requestNumericId = $this->connection->lastInsertId('*PREFIX*xray_requests');
	}

	/**
	 * @param string $id
	 * @return int
	 */
	private function getRequestNumericId($id) {
		$query = $this->connection->getQueryBuilder();

		$query->select('id')
			->from('xray_requests')
			->orderBy('id', 'DESC')
			->setMaxResults(1);

		if ($id) {
			$query->where($query->expr()->eq('request_id', $query->createNamedParameter($id)));
		}

		$result = $query->execute();

		return (int)$result->fetchColumn();
	}

	/**
	 * @param string $before
	 * @param int $count
	 * @return array
	 */
	public function getHistory($before = '', $count = 20) {
		$beforeNumericId = $this->getRequestNumericId($before);

		$query = $this->connection->getQueryBuilder();

		$query->select('data')
			->from('xray_log')
			->orderBy('id', 'DESC')
			->where($query->expr()->lt('request_numeric_id', $query->createNamedParameter($beforeNumericId, \PDO::PARAM_INT)))
			->andWhere($query->expr()->gte('request_numeric_id', $query->createNamedParameter($beforeNumericId - $count, \PDO::PARAM_INT)));

		$result = $query->execute();

		$items = $result->fetchAll(\PDO::FETCH_COLUMN);
		return array_map(function ($item) {
			return json_decode($item, true);
		}, $items);
	}

	public function cleanup() {
		$query = $this->connection->getQueryBuilder();

		$cutOff = $this->timeFactory->getTime() - 3600;

		$query->delete('xray_log')
			->where($query->expr()->lt('timestamp', $query->createNamedParameter($cutOff, \PDO::PARAM_INT)));
		$query->execute()->closeCursor();


		$query = $this->connection->getQueryBuilder();

		$cutOff = $this->timeFactory->getTime() - 3600;

		$query->delete('xray_requests')
			->where($query->expr()->lt('timestamp', $query->createNamedParameter($cutOff, \PDO::PARAM_INT)));
		$query->execute();
	}
}
