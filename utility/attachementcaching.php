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
	private $fileSystem;
	private $fileFactory;
	private $maximumTimeout;

	public function __construct(API $api, $fileSystem, SimplePieFileFactory $fileFactory, $maximumTimeout=10) {
		$this->api = $api;
		$this->fileSystem = $fileSystem;
		$this->fileFactory = $fileFactory;
		$this->maximumTimeout = $maximumTimeout;
	}


	/**
	* Fetch and save the images of a feed-item and replace the appendant image source-URLs in its body
	* @param Item item which body's image should be saved
	* @return Item item with replaced (relative) image URLs, if the download was successfull
	*/
	public function replaceAttachements($item){
		$dom = new \DOMDocument();
		$dom->preserveWhiteSpace = false;

		$body = mb_convert_encoding($item->getBody(), 'HTML-ENTITIES', 'UTF-8, ISO-8859-1');

		// return, if body is empty or loading the HTML fails
		if( trim($body) == "" || !@$dom->loadHTML($body) ) {
			return $item;
		}

		// remove <!DOCTYPE 
		$dom->removeChild($dom->firstChild);            
		// remove <html></html> 
		$dom->replaceChild($dom->firstChild->firstChild, $dom->firstChild);

		$xpath = new \DOMXpath($dom);
		$xpathResult = $xpath->query('//img[@src]');

		foreach ($xpathResult as $imgElement) {
			$urlElement = $imgElement->attributes->getNamedItem("src");
			$imgSrc = $urlElement->nodeValue;

			// check if it is an absolute URL
			if(!filter_var($imgSrc, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
				$itemURL = parse_url($item->getUrl());
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

			$filePath = $item->getFeedId() . "/" . $secretId . "/";

			if( $this->fileSystem->is_file($filePath . $filename) ) continue;

			if ( !$this->fileSystem->is_dir($filePath) ) {
				if ( !$this->fileSystem->is_dir($item->getFeedId()) ) {
					$this->fileSystem->mkdir($item->getFeedId());
				}
				$this->fileSystem->mkdir($filePath);
			}

			// let's download the image
			$file = $this->fileFactory->getFile($imgSrc, $this->maximumTimeout, "Mozilla/5.0 AppleWebKit");
			if( !$this->fileSystem->file_put_contents( $filePath . $filename , $file->body ) ) continue;

			// compress if the image is > 5 kByte
			if( $this->fileSystem->filesize($filePath . $filename) > 5000 ) {
				
				$image = new \OC_Image( $this->fileSystem->file_get_contents( $filePath . $filename ) );
				$this->fileSystem->unlink($filePath . $filename);
				
				if( !$image || !$image->valid() ) continue;

				if($image->width() > 500) {
					$image->resize(500);
				}			

				$info = pathinfo($filename);
				if(isset($info['extension'])) $filename = basename($filename, '.'.$info['extension']) . ".jpg";

				imagejpeg($image->resource(), $this->fileSystem->getLocalFile($filePath . $filename), 65);

				$image->destroy();
			}

			$urlElement->nodeValue = $this->api->linkToRoute("news_api_items_image", array('feedId' => $item->getFeedId(),'secretId' => $secretId, 'imageFile' => $filename));
		}

		// save dom to string and remove <body></body>
		$xmlString = substr(trim($dom->saveHTML()), 6, -7);

		$item->setBody( $xmlString );

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
		$filename = time() . "_" . $filename;

		if (!$this->fileSystem->is_dir($feedId)) {
			$this->fileSystem->mkdir($feedId);
		}

		// let's download the image
		$this->fileSystem->file_put_contents( $feedId . "/" . $filename, file_get_contents($url) );
		return $this->api->linkToRoute("news_api_feeds_image", array('feedId' => $feedId,'imageFile' => $filename));
	}




	public function convertRelativeImgUrls($body) {
		$dom = new \DOMDocument();
		$dom->preserveWhiteSpace = false;

		$body = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8, ISO-8859-1');

		// return, if body is empty or loading the HTML fails
		if( trim($body) == "" || !@$dom->loadHTML($body) ) {
			return $body;
		}

		// remove <!DOCTYPE 
		$dom->removeChild($dom->firstChild);            
		// remove <html></html> 
		$dom->replaceChild($dom->firstChild->firstChild, $dom->firstChild);

		$xpath = new \DOMXpath($dom);
		$xpathResult = $xpath->query('//img[@src and not(starts-with(@src, "http"))]');

		foreach ($xpathResult as $imgElement) {
			$urlElement = $imgElement->attributes->getNamedItem("src");
			$imgSrc = $urlElement->nodeValue;

			if(!filter_var($imgSrc, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
				$urlElement->nodeValue = $this->api->getAbsoluteURL( $imgSrc );
			}
		}

		// save dom to string and remove <body></body>
		$body = substr(trim($dom->saveHTML()), 6, -7);

		return $body;
	}



	public function purgeDeletedItem($item) {
		$secretId = md5($item->getId() . $item->getFeedId() . $item->getGuidHash());
		$this->purgeDeleted($item->getFeedId(), $secretId);
	}


	public function purgeDeleted($feedId, $secretId=null) {
		if(is_null($secretId)) {
			//if( is_dir( $this->imgCacheDirectory . $feedId ) )
				$this->fileSystem->deleteAll($feedId);
		} else {
			//if( is_dir( $this->imgCacheDirectory . $feedId . "/" . $secretId ) )
				$this->fileSystem->deleteAll($feedId . "/" . $secretId);
		}
	}

}
