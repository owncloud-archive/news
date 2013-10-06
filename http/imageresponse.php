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
	* @var Image data
	*/
	protected $image;

	/**
	* @param OCP\Image $image
	*/
	public function __construct($image = null) {
		if(!is_null($image)) {
			$this->setImage($image);
		}
	}

	/**
	* @param OCP\Image $image
	*/
	public function setImage(\OC_Image $image) {
		if(!$image->valid()) {
			throw new InvalidArgumentException(__METHOD__. ' The image resource is not valid.');
		}
		$this->image = $image->data();
		$this->addHeader('Content-Type', $image->mimeType());
	}

	/**
	* @param String $contentType
	*/
	public function setContentType(String $contentType) {
		$this->addHeader('Content-Type', $contentType);
	}

	/**
	* @param Image $image
	*/
	public function setImageData(String $imageData) {
		$this->image = $imageData;
	}

	/**
	* Return the image data stream
	* @return Image data
	*/
	public function render() {
		if(is_null($this->image)) {
			throw new BadMethodCallException(__METHOD__. ' Image must be set either in constructor or with setImage()');
		}
		return $this->image;
	}

}