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
		// wrap everything inside a <div> because otherwise multi sibling documents are broken (<p>ABC</p><p>DEF</p>
		// will become <p>ABC</p>)
		@$dom->loadHTML('<div>' . $item->getBody() . '</div>');
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

		// remove <!DOCTYPE
		$dom->removeChild($dom->firstChild);

		// remove <html><body></body></html>
		$dom->replaceChild($dom->firstChild->firstChild->firstChild, $dom->firstChild);

		// save all changes back to the item
		$item->setBody(trim($dom->saveHTML()));

		return $item;
	}


}