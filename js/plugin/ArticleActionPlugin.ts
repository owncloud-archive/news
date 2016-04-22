/**
 * ownCloud - News
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Bernhard Posselt 2014
 */

interface Window { News: any; }

window.News = window.News || {};


(function (window, document, $, exports) {
    'use strict';

    var articleActionPlugins = [];

    exports.addArticleAction = function (action) {
        articleActionPlugins.push(action);
    };

    exports.getArticleActionPlugins = function () {
        return articleActionPlugins;
    };

})(window, document, jQuery, window.News);

