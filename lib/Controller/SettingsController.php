<?php
/**
 * @copyright Copyright (c) 2019 Gary Kim <gary@garykim.dev>
 *
 * @author Gary Kim <gary@garykim.dev>
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

namespace OCA\Recommendations\Controller;

use OCA\Recommendations\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;
use Exception;

class SettingsController extends Controller {

	/** @var IConfig */
	private $config;

	/** @var IUserSession */
	private $userSession;

	public function __construct($appName, IRequest $request, IConfig $config, IUserSession $userSession) {
		parent::__construct($appName, $request);

		$this->config = $config;
		$this->userSession = $userSession;
	}

	/**
	 * @NoAdminRequired
	 * 
	 * @return JSONResponse
	 */
	public function getSettings (): JSONResponse {
		$user = $this->userSession->getUser();
		if (is_null($user)) {
			throw new Exception("Not logged in");
		}
		return new JSONResponse([
			'enabled' => $this->config->getUserValue($user->getUID(), Application::APP_ID, 'enabled', 'true') === 'true',
		]);
	}

	/**
	 * @NoAdminRequired
	 * 
	 * @param $key
	 * @return JSONResponse
	 */
	public function setSetting (string $key, string $value): JSONResponse {
		$user = $this->userSession->getUser();
		if (is_null($user)) {
			throw new Exception("Not logged in");
		}

		$avaliableSetttings = ['enabled'];

		if (!in_array($key, $avaliableSetttings)) {
			return new JSONResponse([
				'message' => 'parameter does not exist',
			], Http::STATUS_UNPROCESSABLE_ENTITY);
		}

		$this->config->setUserValue($user->getUID(), Application::APP_ID, $key, $value);

		return new JSONResponse([
			'key' => $key,
			'value' => $value,
		]);
	}

}
