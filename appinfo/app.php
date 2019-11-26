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

$appId = 'xray';
$appName = 'XRay';

/** @var Application $app */
$app = \OC::$server->query(Application::class);
$app->registerSources();

\OC::$server->getNavigationManager()->add(function () use ($appId, $appName) {
	return [
		'id' => $appId,
		'order' => 22,
		'name' => $appName,
		'href' => \OC::$server->getURLGenerator()->linkToRoute($appId . '.page.index'),
		'icon' => \OC::$server->getURLGenerator()->imagePath($appId, 'app.svg')
	];
});
