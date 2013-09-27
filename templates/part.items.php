<div class="pull-refresh" ng-class="{refresh: refresh}"></div>

<ul>
	<li class="feed_item"
		ng-repeat="item in itemBusinessLayer.getAll() | orderBy:['-id'] "
		ng-class="{ read: item.isRead() }"
		data-id="{{ item.id }}">
		<h2 class="item_date">
			<span class="timeago" title="{{item.pubDate*1000|date:'dd-MM-yyyy'}}">
				{{ getRelativeDate(item.pubDate) }}
			</span>
		</h2>

		<div class="utils">
			<ul class="primary_item_utils">
				<li ng-class="{ important: item.isStarred() }"
					ng-click="itemBusinessLayer.toggleStarred(item.id)"
					class="star"
					>
				</li>
			</ul>
		</div>

		<h1 class="item_heading">{{ item.title }}</h1>
		<h1 class="item_title">
			<a ng-click="itemBusinessLayer.setRead(item.id)"
				target="_blank" ng-href="{{ item.url }}">
				{{ item.title }}
			</a>
			<a href="https://www.facebook.com/sharer/sharer.php?u={{ item.url }}" target="_blank"> <img src="<?php p(OCP\Util::imagePath('news','fb_icon_325x325.png'));?>" width=20 alt="share on facebook"> </a>
        	        <a target="_blank" href="https://plus.google.com/share?url={{ item.url }}"><img src="<?php p(OCP\Util::imagePath('news','g1.png'));?>"></a>
		</h1>

		<h2 class="item_author">
			<span ng-show="itemBusinessLayer.noFeedActive() && feedBusinessLayer.getFeedLink(item.feedId)">
				<?php p($l->t('from')) ?>
				<a 	target="_blank" ng-href="{{ feedBusinessLayer.getFeedLink(item.feedId) }}"
					class="from_feed">{{ itemBusinessLayer.getFeedTitle(item.id) }}</a>
			</span>
			<span ui-if="item.author">
				<?php p($l->t('by')) ?>
				{{ item.author }}
			</span>
	</h2>

		<div class="enclosure" ui-if="item.enclosureLink">
			<news-audio type="{{ item.enclosureType }}" src="{{ item.enclosureLink }}"/><?php
				p($l->t('Download'))
			?></audio>
		</div>

		<div class="body"
				ng-click="itemBusinessLayer.setRead(item.id)"
				ng-bind-html-unsafe="item.body">
		</div>

		<div class="bottom_utils">
			<ul class="secondary_item_utils"
				ng-class="{ show_keep_unread: itemBusinessLayer.isKeptUnread(item.id) }">
				<li ng-click="itemBusinessLayer.toggleKeepUnread(item.id)"
					class="keep_unread"><?php p($l->t('Keep unread')); ?>
					<input type="checkbox" ng-checked="itemBusinessLayer.isKeptUnread(item.id)"/>
				</li>
			</ul>
		</div>
	</li>
</ul>
