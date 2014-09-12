<div id="first-run">
    <div>
        <div class="helper"><h1><?php p($l->t('Add a feed')) ?></h1></div>
        <div class="title"><h1><?php p($l->t('There are no feeds yet. Go ahead and add some')) ?> :)</h1></div>
        <div class="helper"><h1><?php p($l->t('Import feeds and articles')) ?></h1></div>
    </div>
</div>

<div news-auto-focus="#app-content"
     ng-class="{compact: Content.isCompactView(), 'feed-view': Content.isFeed()}">
    <ul>
        <li class="item {{ Content.getFeed(item.feedId).cssClass }}"
            ng-repeat="item in Content.getItems() | orderBy:[Content.orderBy()] track by item.id"
            ng-click="Content.markRead(item.id)"
            ng-class="{read: !item.unread, open: item.show}"
            data-id="{{ item.id }}">

            <div class="utils" ng-click="Content.toggleItem(item)">
                <ul>
                    <li class="title"
                        title="{{ item.title }}"
                        ng-style="{ backgroundImage: 'url(' + Content.getFeed(item.feedId).faviconLink + ')'}">
                        <h1>{{ item.title }}</h1>
                    </li>
                    <li ng-click="Content.toggleStar(item.id)" class="util" news-stop-propagation>
                        <button class="star svg" ng-class="{'starred': item.starred}" title="<?php p($l->t('Star')); ?>"></button>
                    </li>
                    <li ng-click="Content.toggleKeepUnread(item.id)" class="util" news-stop-propagation>
                        <button class="icon-toggle toggle-keep-unread" ng-class="{'keep-unread': item.keepUnread}" title="<?php p($l->t('Keep unread')); ?>"></button>
                    </li>
                    <li class="util">
                        <a class="external icon-link"
                            target="_blank"
                            ng-href="{{ item.url }}"
                            title="<?php p($l->t('Open website')) ?>"
                            news-stop-propagation>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="article">

                <div class="subtitle">
                    <time class="date" class="date" title="{{ item.pubDate*1000|date:'yyyy-MM-dd HH:mm:ss' }}"
                        datetime="{{ item.pubDate*1000|date:'yyyy-MM-ddTHH:mm:ssZ' }}">{{ Content.getRelativeDate(item.pubDate) }}</time>,
                    <span class="author" ng-show="item.author"><?php p($l->t('by')) ?> {{ item.author }}</span>
                    <!--<?php p($l->t('from')) ?> <a ng-href="#/items/feeds/{{ item.feedId }}">{{ Content.getFeed(item.feedId).title }}</a>-->
                </div>

                <div class="enclosure" ng-if="item.enclosureLink">
                    <!--<news-audio type="{{ item.enclosureType }}"
                                ng-src="{{ item.enclosureLink|trustUrl }}">
                        <?php p($l->t('Download')) ?>
                    </news-audio>-->
                </div>

                <div class="body" news-bind-html-unsafe="item.body"></div>

            </div>
        </li>
    </ul>
</div>