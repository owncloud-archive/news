<?php

/**
* ownCloud - News
*
* @author 
* @author 
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

namespace OCA\News\Http;

use \OCA\AppFramework\Http\Response;

/**
* A renderer for images
*/
class ImageResponse extends Response {
	/**
	* @var String path to the image
	*/
	protected $imagePath;

	/**
	* @param OCP\Image $image
	*/
	public function __construct($imagePath = null) {
		if(is_string($imagePath) && file_exists($imagePath)) {
			$this->imagePath = $imagePath;
		}
	}

	/**
	* Return the image data stream
	* @return Image data
	*/
	public function render() {

		if (filesize($this->imagePath) > 11) {
			$imageType = exif_imagetype($this->imagePath);
		}
		else {
			$imageType = false;
		}
		
		$mimeType = $imageType ? image_type_to_mime_type($imageType) : '';

		if($mimeType) {
			header("Content-type: " . $mimeType);
			readfile($this->imagePath);
		} else {
			$this->setStatus(404);
		}

	}

}