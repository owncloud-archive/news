<?php
/**
 * ownCloud - News
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Alessandro Cosentino <cosenal@gmail.com>
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Alessandro Cosentino 2012
 * @copyright Bernhard Posselt 2012, 2014
 */

namespace OCA\News\ArticleEnhancer;

use \OCA\News\Db\Item;


class GlobalArticleEnhancer implements ArticleEnhancer {


	/**
	 * This method is run after all enhancers and for every item
	 */
	public function enhance(Item $item) {
		$dom = new \DOMDocument();
		if(PHP_VERSION_ID >= 50400 && LIBXML_VERSION >= 20708) { // major * 10000 + minor * 1000 + release
			// these options are available since 5.4.0
			@$dom->loadHTML($item->getBody(), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		} else {
			// fallback
			@$dom->loadHTML($item->getBody());
		}
		$xpath = new \DOMXpath($dom);

		// remove youtube autoplay
		// NOTE: PHP supports only XPath 1.0 so no matches() function :(
		$youtubeIframes = "//iframe[contains(@src, 'youtube.com')]";

		$elements = $xpath->query($youtubeIframes);
		foreach ($elements as $element) {

			// src needs to be matched against regex to prevent false positives
			// and because theres no XPath matches function available
			$src = $element->getAttribute('src');
			$regex = '%^(http://|https://|//)(www\.)?youtube.com/.*\?.*autoplay=1.*%i';

			if (preg_match($regex, $src)) {
				$replaced = str_replace('autoplay=1', 'autoplay=0', $src);
				$element->setAttribute('src', $replaced);
			}
		}

		if(PHP_VERSION_ID < 50400 || LIBXML_VERSION < 20708) { // major * 10000 + minor * 1000 + release
			// we need to do this, because the options to load HTML without them wasn't available

			// remove <!DOCTYPE
			$dom->removeChild($dom->firstChild);

			// remove <html><body></body></html>
			$dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);
		}

		// save all changes back to the item
		$item->setBody(trim($dom->saveHTML()));

		return $item;
	}


}