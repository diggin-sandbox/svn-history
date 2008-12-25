<?php
//@see http://subtech.g.hatena.ne.jp/miyagawa/20080808/1218136260
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$url = 'http://www.fujitv.co.jp/meza/uranai/';

try {
    $ranking = new Diggin_Scraper_Process();
    $ranking->process('.', 'rank => [@background, "Digits"]')
            ->process('img', 'star => @alt', 'image => @src')
            ->process('td.text', 'text => TEXT')
            ->process('.//td[contains(@class, "lucky") and (not(contains(@valign, "bottom")))]', 'lucky => TEXT');

    $scraper = new Diggin_Scraper();
    $scraper->process('//td[@class="day" and @height < 100]', 'date => "TEXT"')
            ->process('//table[contains(@background, "item/rank")]', array('ranking[]' => $ranking))
            ->scrape($url);
} catch (Diggin_Scraper_Exception $e) {
    die($e->getMessage());
}

Zend_Debug::dump($scraper->results);

/*
array(2) {
  ["date"] => string(9) "9月29日"
  ["ranking"] => array(12) {
    [0] => array(5) {
      ["rank"] => string(2) "01"
      ["star"] => string(15) "おひつじ座"
      ["image"] => string(57) "http://www.fujitv.co.jp/meza/uranai/item/conste_aries.gif"
      ["text"] => string(126) "新しい恋の一大チャンス到来。友人からの誘いがきっかけに。さわやかな笑顔で振る舞って。"
      ["lucky"] => string(9) "内緒話"
    }
    [1] => array(5) {
      ["rank"] => string(2) "02"
      ["star"] => string(15) "てんびん座"
      ["image"] => string(57) "http://www.fujitv.co.jp/meza/uranai/item/conste_libra.gif"
      ["text"] => string(72) "大胆な発想で人気急上昇。常識よりも感性を最重視。"
      ["lucky"] => string(12) "メモ用紙"

*/


