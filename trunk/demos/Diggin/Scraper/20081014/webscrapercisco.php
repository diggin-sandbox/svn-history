<?php
//http://blog.hide-k.net/archives/2007/09/webscrapercisco.php
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

$link = new Diggin_Scraper_Process();
$link->process('.', 'name => TEXT')
     ->process('.', 'uri => @href');

$genre = new Diggin_Scraper_Process();
$genre->process('//a[1]', array('top' => $link))
      ->process('//a[2]', array('style' => $link));

     
function star(SimpleXMLElement $s){
    return (string) $s;
}
      
      
$item = new Diggin_Scraper();
$item->process('td.de_title', 'title  => TEXT')
     ->process('td.de_artist', 'artist => TEXT')
     ->process('td.nm_jacket>img', 'image => @src')
     ->process('td.de_price', 'price => DECODE')
     ->process('td.de_label>a', array('label' => $link))
     ->process('td.de_genre', array('genre' => $genre))
     ->process('p.de_star', 'star => RAW, star')
     ->scrape('http://www.cisco-records.co.jp/html/item/003/100/item355640.html');
     
print_r($item);

/**
Diggin_Scraper Object
(
    [results] => Array
        (
            [title] => Capsule Rmx EP
            [artist] => Capsule
            [image] => http://www.cisco-records.co.jp/upimages/003/100/item355640p1.jpg
            [price] => ¥1,365
            [label] => Array
                (
                    [name] => Contemode
                    [uri] => http://www.cisco-records.co.jp/html/label/L348/labelL34869_0desc.html
                )

            [genre] => Array
                (
                    [top] => Array
                        (
                            [name] => HOUSE
                            [uri] => http://www.cisco-records.co.jp/html/genretop/genretop_4.html
                        )

                    [style] => Array
                        (
                            [name] => POPDANCE
                            [uri] => http://www.cisco-records.co.jp/list/style.php?qGenre=4&qStyle=128
                        )

                )

            [star] => ★★★★★
        )

    [_url:protected] => http://www.cisco-records.co.jp/html/item/003/100/item355640.html
)

 */