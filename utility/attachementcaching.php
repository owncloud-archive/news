<?php

/**
* ownCloud - News
*
* @author 
* @copyright 
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

namespace OCA\News\Utility;

use \OCA\AppFramework\Core\API;

class AttachementCaching {

	private $api;

	public function __construct(API $api) {
		$this->api = $api;
		$this->imgCacheDir = $this->api->getSystemValue('datadirectory') . '/news/imgcache/';
	}


	/**
	* Fetch and save the images of a feed-item and replace the appendant image source-URLs in its body
	* @param Item item which body's image should be saved
	* @return Item item with replaced (relative) image URLs, if the download was successfull
	*/
	public function replaceAttachements($item){

		// wrap it in an element, so it is a xml-"file", we will strip it later
		$bodyXml = simplexml_load_string("<body>".$item->getBody()."</body>");

		foreach ($bodyXml->xpath('//img') as $imgElement) {
			$imgSrc = $imgElement->attributes()->src;


			// check if it is an absolute URL
			if(!filter_var($imgSrc, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
				$itemURL = parse_url($item->getUrl() );
				$imgURL = parse_url($imgSrc);

				if( $imgURL["path"][0] != "/" )
					$imgSrc = $itemURL["scheme"]."://".$itemURL["host"] . $itemURL["path"] . "/" . $imgURL["path"];
				else
					$imgSrc = $itemURL["scheme"]."://".$itemURL["host"] . $imgURL["path"];		
			}

			$filename = basename($imgSrc);
			$filename = strtolower(trim(preg_replace('/[^A-Za-z0-9-.]+/', '-', $filename)));

			// a unique, "secret" id for the item
			$secretId = md5($item->getId() . $item->getFeedId() . $item->getGuidHash());

			$filePath = $this->imgCacheDir . $item->getFeedId() . "/" . $secretId . "/";

			if(is_file($filePath . $filename)) continue;

			if (!is_dir($filePath)) {
				mkdir($filePath, 0755, true);
			}
			
			// let's download the image
			if( !file_put_contents( $filePath . $filename , file_get_contents($imgSrc) ) ) continue;

			// compress the file
			$image = imagecreatefromjpeg($filePath . $filename);

			$imgInfo = getimagesize($filePath . $filename);
			$width = $imgInfo[0];
			$height = $imgInfo[1];

			if($width > 500) {
				$newwidth=500;
				$newheight=($height/$width)*500;
				$tmp=imagecreatetruecolor($newwidth,$newheight);
				imagecopyresampled($tmp,$image,0,0,0,0,$newwidth,$newheight,$width,$height);
				$image = $tmp;
			}

			$imgInfo = getimagesize($filePath . $filename);
			if ($imgInfo['mime'] == 'image/jpeg') {
				imagejpeg($image, $filePath . $filename, 55);
			} elseif ($imgInfo['mime'] == 'image/png') {
				unlink($filePath . $filename);
				$filename = substr($filename, 0, -4) . ".jpg";
				imagejpeg($image, $filePath . $filename, 70);
				//imagepng($image, $this->imgCacheDir . $item->guidHash . "/0" . basename($imgSrc), 9);
			}
			imagedestroy($image);
			imagedestroy($tmp);

			$imgElement->attributes()->src = '/owncloud/index.php/apps/news/api/v1-2/items/'.$item->getFeedId().'/'.$secretId.'/'.$filename;
		}

		// remove the wrapper and attach the new body
		$body = strstr($bodyXml->asXML(), "<body>");
		$body = substr($body, 6, -8);
		$item->setBody( $body );

		return $item;
	}



	/**
	* Fetch and save the favicon of a feed
	* @param 
	* @return 
	*/
	public function replaceFavicon($url, $feedId){
		$filename = basename($url);
		$filename = strtolower(trim(preg_replace('/[^A-Za-z0-9-.]+/', '-', $filename)));

		// add some extra to the filename
		$filename = md5(time()) . "_" . $filename;

		$filePath = $this->imgCacheDir . $feedId;

		if (!is_dir($filePath)) {
			mkdir($filePath, 0755, true);
		}

		// let's download the image
		file_put_contents( $filePath . "/" . $filename , file_get_contents($url) );
		return "/owncloud/index.php/apps/news/api/v1-2/feeds/" . $feedId . "/" . $filename;
	}



	/**
	* @IsAdminExemption
	* @IsSubAdminExemption
	* @IsLoggedInExemption
	* @CSRFExemption
	* @Ajax
	* @API
	*/
	public function purgeDeleted($feedId, $secretId=null) {
		if(is_null($secretId)) {
			if( is_dir( $this->imgCacheDir . $feedId ) )
				$this->delTree( $this->imgCacheDir . $feedId );
		} else {
			if( is_dir( $this->imgCacheDir . $feedId . "/" . $secretId ) )
				$this->delTree( $this->imgCacheDir . $feedId . "/" . $secretId );
		}
	}





	/**
	* Delete a whole directory.
	* http://php.net/manual/de/function.rmdir.php
	*/
	private function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
	}

}
