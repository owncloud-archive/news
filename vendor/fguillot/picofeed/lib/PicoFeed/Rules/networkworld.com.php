<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.networkworld.com/article/2986764/smartphones/samsung-tried-to-troll-apple-fans-waiting-in-line-for-the-iphone-6s.html#tk.rss_all',
            'body' => array(
            '//figure/img',
            '//section[@class="deck"]',
            '//div[@itemprop="articleBody"]',
            ),
            'strip' => array(
            '//aside',
            ),
        )
    )
);
