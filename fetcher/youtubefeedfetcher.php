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
 * @copyright David Luhmer 2014
 */

namespace OCA\News\Fetcher;

class YoutubeFeedFetcher implements IFeedFetcher {

	/**
	 * This fetcher handles youtube urls
	 */
	public function canHandle($url) {
		return $this->checkForPlaylist($url) ||
			$this->checkForChannel($url);
	}

	private function checkForPlaylist($url) {
		//Playlist
		$matches = [];
		$pattern = "/[(http:\/\/|https:\/\/|\/\/)](www.)?youtube.com.*?list=([^&]*)/";
		if(preg_match($pattern, $url, $matches)) {
			return "http://gdata.youtube.com/feeds/api/playlists/" . $matches[2];
		}
		return false;
	}

	private function checkForChannel($url) {
		//Channel
		$matches = [];
		$pattern = "/[(http:\/\/|https:\/\/|\/\/)](www.)?youtube.com/channel/(.*)/";
		if(preg_match($pattern, $url, $matches)) {
			return "http://gdata.youtube.com/feeds/users/" . $matches[2] . "/uploads";
		}	
		return false;
	}


	/**
	 * Fetch a feed from remote
	 * @param string $url remote url of the feed
	 * @param boolean $getFavicon if the favicon should also be fetched, defaults
	 * to true
	 * @throws FetcherException if simple pie fails
	 * @return array(\OCA\News\Db\Feed, \OCA\News\Db\Item[]) an array containing
	 * the new feed and its items
	 */
	public function fetch($url, $getFavicon=true) {
		$url = $this->checkForPlaylist($url);
	
		//If no playlist was detected, check for channel url
		if(!$url) {
			$url = $this->checkForChannel($url);
		}

		return $this->feedFetcher->fetch($url, $getFavicon);
	}


}
