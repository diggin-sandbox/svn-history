<?php
require_once 'Diggin/Scraper.php';

$section = new Diggin_Scraper_Process();
$section->process('h3.title', 'title => TEXT')
        ->process('h3.title a', 'link => @href');

$scraper = new Diggin_Scraper();
$scraper->process('div.section', array('section[]' => $section))
        ->scrape('http://d.hatena.ne.jp/amachang');
        
print_r($scraper->results);

/*****
Web_Scraper.php's result

array(8) {
  [0]=>
  array(2) {
    ["text"]=>
    string(27) "主に金沢関係の人へ"
    ["attributes"]=>
    array(1) {
      ["href"]=>
      string(29) "/amachang/20081006/1223277205"
    }
  }
  [1]=>
  array(2) {
    ["text"]=>
    string(75) "テンプレートのメンバ関数がインスタンス化される箇所"
    ["attributes"]=>
    array(1) {
      ["href"]=>
      string(29) "/amachang/20081005/1223225282"
    }
  }
 */

/**
Diggin Scraper's result
Array
(
    [section] => Array
        (
            [0] => Array
                (
                    [title] => 主に金沢関係の人へ
                    [link] => http://d.hatena.ne.jp/amachang/20081006/1223277205
                )

            [1] => Array
                (
                    [title] => テンプレートのメンバ関数がインスタンス化される箇所
                    [link] => http://d.hatena.ne.jp/amachang/20081005/1223225282
                )
'
 */
