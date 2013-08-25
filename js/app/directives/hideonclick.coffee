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


# Used to hide elements on click via jquery fadeOut effect
# If a selector is passed as an option, then the effect is applied on
# on that selector, otherwise the effect is applied on the element itself
# example 1) <button  ... hide-on-click="{selector: '#tooltip'}" ... >
#		hides the 'tooltip' element
# example 2) <button  ... hide-on-click ...>
#		hides the button itself
angular.module('News').directive 'hideOnClick', ->

	return (scope, elm, attr) ->
		options = scope.$eval(attr.hideOnClick)

		if angular.isDefined(options) and angular.isDefined(options.selector)
			$(elm).click ->
				$(options.selector).fadeOut()
		else
			$(elm).click ->
				$(elm).fadeOut()