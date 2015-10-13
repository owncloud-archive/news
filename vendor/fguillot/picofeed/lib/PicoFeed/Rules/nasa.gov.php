<?php
return array(
    'grabber' => array(
        '%.*%' => array(
            'test_url' => 'http://www.nasa.gov/image-feature/the-nile-at-night/',
            'body' => array(
	    '//img[@class="feature-image]',
	    '//div[@class="text"]',
	    ),
	 )
    )
);
