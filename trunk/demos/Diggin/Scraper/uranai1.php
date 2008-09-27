<?php
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$url = 'http://www.fujitv.co.jp/meza/uranai/';

try {
        $ranking = new Diggin_Scraper_Process();
        $ranking->addProcess('.', 'rank => [@background, "Digits"]')
                ->addProcess('img', 'star => @alt', 'image => @src')
                ->addProcess('td.text', 'text => TEXT')
                ->addProcess('.lucky', 'lucky => TEXT');

        $scraper = new Diggin_Scraper();
        $scraper->process('//td[@class="day" and @height < 100]', 'date => "TEXT"')
                ->process('//table[@width="306"]', array('ranking[]' => $ranking))
                ->scrape($url);
} catch (Diggin_Scraper_Exception $e) {
         die($e->getMessage());
}

Zend_Debug::dump($scraper->results);

