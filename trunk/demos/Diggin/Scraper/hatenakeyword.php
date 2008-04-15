<?php
require_once 'Diggin/Scraper.php';
$scraper = new Diggin_Scraper();
$scraper->changeStrategy("Diggin_Scraper_Strategy_Selector")
        ->process('span.title > a:first-child', "title => 'TEXT'", "url => '@href'")
        ->process('span.furigana', "furigana => 'TEXT'")
        ->process('ul.list-circle > li:first-child > a', "category => 'TEXT'")
        ->scrape("http://d.hatena.ne.jp/keyword/%BA%B0%CC%EE%A4%A2%A4%B5%C8%FE");
print_r($scraper->results);

/* print_r results:
Array
(
    [title] => 紺野あさ美
    [url] => http://d.hatena.ne.jp/keyword/%ba%b0%cc%ee%a4%a2%a4%b5%c8%fe
    [furigana] => こんのあさみ
    [category] => アイドル
)
*/

//Web::Scraper's eg ....

//my $keyword = scraper {
//    process 'span.title > a:first-child', title => 'TEXT', url => '@href';
//    process 'span.furigana', furigana => 'TEXT';
//    process 'ul.list-circle > li:first-child > a', category => 'TEXT';
//};
//
//my $res = $keyword->scrape(URI->new("http://d.hatena.ne.jp/keyword/%BA%B0%CC%EE%A4%A2%A4%B5%C8%FE"));
//
//use YAML;
//warn Dump $res;
//
//__END__
//---
//category: アイドル
//furigana: こんのあさみ
//title: 紺野あさ美
//url: /keyword/%ba%b0%cc%ee%a4%a2%a4%b5%c8%fe?kid=800
