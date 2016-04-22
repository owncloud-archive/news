/**
 * ownCloud - News
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Bernhard Posselt <dev@bernhard-posselt.com>
 * @copyright Bernhard Posselt 2012, 2014
 */

interface Notification {
    hide(): void;
    showHtml(message: string): void;
}

interface OwnCloud {
    Notification: Notification;
    generateUrl(path: string): string;
}

declare var OC: OwnCloud;
declare var oc_requesttoken: string;
declare function t(app: string, text: string): string;

// temporary fixes
interface News {
    getArticleActionPlugins(): Array<any>;
}

declare var News: News;
declare var moment: any;
