###

ownCloud - News

@author Bernhard Posselt
@copyright 2012 Bernhard Posselt dev@bernhard-posselt.com

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
License as published by the Free Software Foundation; either
version 3 of the License, or any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU AFFERO GENERAL PUBLIC LICENSE for more details.

You should have received a copy of the GNU Affero General Public
License along with this library.  If not, see <http://www.gnu.org/licenses/>.

###


describe 'StarredBusinessLayer', ->

	beforeEach module 'News'

	beforeEach module ($provide) =>
		@persistence =
			test: 'starredbusinesslayer'

		$provide.value 'Persistence', @persistence
		return

	beforeEach inject (@StarredBusinessLayer, @StarredCount, @ActiveFeed,
	                   @FeedType) =>
		@ActiveFeed.handle({type: @FeedType.Feed, id:0})
		@StarredCount.setStarredCount(0)


	it 'should not be visible if starredCount is 0', =>
		expect(@StarredBusinessLayer.isVisible()).toBe(false)

		@StarredCount.setStarredCount(144)
		expect(@StarredBusinessLayer.isVisible()).toBe(true)


	it 'should always be visible if its the active feed', =>
		@ActiveFeed.handle({type: @FeedType.Starred, id:0})
		expect(@StarredBusinessLayer.isVisible()).toBe(true)


	it 'should get the correct unread count', =>
		@StarredCount.setStarredCount(144)

		expect(@StarredBusinessLayer.getUnreadCount()).toBe(144)


	it 'should increase the starred count', =>
		expect(@StarredBusinessLayer.increaseCount()).toBe(1)


	it 'should decrease the starred count', =>
		expect(@StarredBusinessLayer.decreaseCount()).toBe(-1)