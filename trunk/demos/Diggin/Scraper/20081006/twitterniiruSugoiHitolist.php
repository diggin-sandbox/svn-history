<?php
function getusername($var)
{
    return trim(parse_url($var, PHP_URL_PATH), '/');
}
require_once 'Diggin/Scraper.php';
$scraper = new Diggin_Scraper();
$scraper->process('//div[@class="post-content"]/ul/li/a', 'phper[] => ["@href", "getusername"]')
        ->scrape('http://www.1x1.jp/blog/2008/05/twitter_japanese_phper.html');
var_dump($scraper->phper);

/**
array(38) {
  [0]=>
  string(4) "LIND"
  [1]=>
  string(6) "akiyan"
  [2]=>
  string(3) "bto"
  [3]=>
  string(10) "cocoitiban"
  [4]=>
  string(3) "elf"
  [5]=>
  string(4) "halt"
  [6]=>
  string(6) "hiro_y"
  [7]=>
  string(3) "hnw"
  [8]=>
  string(8) "ichii386"
  [9]=>
  string(4) "iogi"
  [10]=>
  string(6) "iteman"
  [11]=>
  string(5) "junya"
  [12]=>
  string(6) "kensuu"
  [13]=>
  string(8) "komagata"
  [14]=>
  string(7) "koyhoge"
  [15]=>
  string(7) "kumatch"
  [16]=>
  string(5) "kunit"
  [17]=>
  string(15) "masaki_fujimoto"
  [18]=>
  string(8) "masugata"
  [19]=>
  string(8) "memokami"
  [20]=>
  string(9) "moriyoshi"
  [21]=>
  string(6) "mumumu"
  [22]=>
  string(8) "nowelium"
  [23]=>
  string(6) "p4life"
  [24]=>
  string(5) "rhaco"
  [25]=>
  string(4) "rsky"
  [26]=>
  string(8) "shigepon"
  [27]=>
  string(8) "shimooka"
  [28]=>
  string(7) "shin1x1"
  [29]=>
  string(5) "shoma"
  [30]=>
  string(7) "sotarok"
  [31]=>
  string(6) "suzuki"
  [32]=>
  string(6) "takagi"
  [33]=>
  string(9) "tsukimiya"
  [34]=>
  string(5) "yando"
  [35]=>
  string(7) "yohgaki"
  [36]=>
  string(8) "yonekawa"
  [37]=>
  string(7) "yudoufu"
}

 */