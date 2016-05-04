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

namespace OCA\XRay\Controller;

use OCA\XRay\EventSource;
use OCA\XRay\Queue\IQueue;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class PageController extends Controller {
	/** @var  IQueue */
	private $queue;

	/**
	 * @param string $AppName
	 * @param IRequest $request
	 * @param IQueue $queue
	 */
	public function __construct($AppName, IRequest $request, IQueue $queue) {
		parent::__construct($AppName, $request);
		$this->queue = $queue;
	}

	/**
	 * @NoCSRFRequired
	 * @param int $historySize
	 */
	public function listen($historySize = 0) {
		$eventSource = new EventSource();

		if ($historySize > 0) {
			$history = $this->queue->retrieveHistory($historySize);
			foreach ($history as $item) {
				$eventSource->send($item['type'], $item['data']);
			}
		}

		$this->queue->listen(function ($item) use ($eventSource) {
			$eventSource->send($item['type'], $item['data']);
		});
	}

	/**
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function index() {
		$response = new TemplateResponse(
			$this->appName,
			'index',
			[
				'appId' => $this->appName
			]
		);

		return $response;
	}
}
