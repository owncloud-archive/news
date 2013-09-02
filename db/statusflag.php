<?php

/**
* ownCloud - News
*
* @author Alessandro Cosentino
* @author Bernhard Posselt
* @copyright 2012 Alessandro Cosentino cosenal@gmail.com
* @copyright 2012 Bernhard Posselt dev@bernhard-posselt.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\News\Db;

class StatusFlag {
	const UNREAD    = 0x02;
	const STARRED   = 0x04;
	const DELETED   = 0x08;
	const UPDATED   = 0x16;


	/**
	 * Get status for query
	 */
	public function typeToStatus($type, $showAll){
		if($type === FeedType::STARRED){
			return self::STARRED;
		} else {
			$status = 0;
		}

		if($showAll){
			$status &= ~self::UNREAD;
		} else {
			$status |= self::UNREAD;
		}

		return $status;
	}


}