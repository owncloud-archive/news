<?php
/**
 * ownCloud - News
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author    Alessandro Cosentino <cosenal@gmail.com>
 * @author    Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Alessandro Cosentino 2012
 * @copyright Bernhard Posselt 2012, 2014
 */

namespace OCA\News\Controller;

use OCP\IRequest;
use OCP\IUserSession;
use OCP\AppFramework\ApiController as BaseApiController;

class ApiController extends BaseApiController {
    private $userSession;

    public function __construct($appName,
                                IRequest $request,
                                IUserSession $userSession){
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
    }

    protected function getUser() {
        return $this->userSession->getUser();
    }

    protected function getUserId() {
        return $this->getUser()->getUID();
    }

    /**
     * @PublicPage
     * @NoCSRFRequired
     * @CORS
     */
    public function index() {
        return [
            'apiLevels' => ['v1-2']
        ];
    }

}
