<?php
require_once 'Diggin/Scraper.php';

$scraper = new Diggin_Scraper();
$scraper->changeStrategy("Diggin_Scraper_Strategy_Preg");
$scraper->process('#<h2>(.*)</h2>#m', 'entry => TEXT')
        ->process("#<li>募集人数:(\d{1,2})</li>#m", 'capa => TEXT')
        ->scrape('http://events.php.gr.jp/event.php/event_show/37');

print_r($scraper);