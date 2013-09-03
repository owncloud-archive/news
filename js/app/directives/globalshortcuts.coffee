###

ownCloud - News

@author Alessandro Cosentino
@copyright 2013 Alessandro Cosentino cosenal@gmail.com

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

angular.module('News').directive 'globalShortcuts', ['$window', ($window) ->

	return (scope, elm, attr) ->

		jumpTo = ($scrollArea, $item) ->
			position = $item.offset().top - $scrollArea.offset().top +
				$scrollArea.scrollTop()
			$scrollArea.scrollTop(position)


		$($window.document).keydown (e) ->
			# only activate if no input elements is focused
			focused = $(':focus')

			if not (focused.is('input') or
			focused.is('select') or
			focused.is('textarea') or
			focused.is('checkbox') or
			focused.is('button'))

				# ? key
				if e.keyCode == 191
					jumpToNextItem(scrollArea)

]