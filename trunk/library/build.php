<?php
//snippet
//sudo pear uninstall __uri/Diggin
//sudo pear uninstall __uri/Diggin_Scraper_Adapter_Htmlscraping

$releaseVersion = '0.6.0';
$releasepath = dirname(__FILE__).DIRECTORY_SEPARATOR.$releaseVersion;

$librarypath = realpath(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'library');
$demospath = realpath(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'demos');
$testspath = realpath(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'tests');
if ($librarypath === false or 
    $demospath === false or 
    $testspath === false) {
	die('path is not valid');
}

//copy file release directory
dircopy($librarypath, $releasepath);
dircopy($demospath, $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'demos');
dircopy($testspath, $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'tests');
copy('makepackage_Diggin.php', $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'makepackage_Diggin.php');
// make package2.xml
include $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.'makepackage_Diggin.php';

//echo ("sudo pear package ");
//exec("sudo pear package $releasepath.DIRECTORY_SEPARATOR.'Diggin/'.package2.xml");

//copy from
//http://jp2.php.net/manual/ja/function.pathinfo.php
 function dircopy($src_dir, $dst_dir, $verbose = false, $use_cached_dir_trees = false)
    {   
        static $cached_src_dir;
        static $src_tree;
        static $dst_tree;
        $num = 0;

        if (($slash = substr($src_dir, -1)) == "\\" || $slash == "/") $src_dir = substr($src_dir, 0, strlen($src_dir) - 1);
        if (($slash = substr($dst_dir, -1)) == "\\" || $slash == "/") $dst_dir = substr($dst_dir, 0, strlen($dst_dir) - 1); 

        if (!$use_cached_dir_trees || !isset($src_tree) || $cached_src_dir != $src_dir)
        {
            $src_tree = get_dir_tree($src_dir);
            $cached_src_dir = $src_dir;
            $src_changed = true; 
        }
        if (!$use_cached_dir_trees || !isset($dst_tree) || $src_changed)
            $dst_tree = get_dir_tree($dst_dir);
        if (!is_dir($dst_dir)) mkdir($dst_dir, 0777, true); 

          foreach ($src_tree as $file => $src_mtime)
        {
            if (!isset($dst_tree[$file]) && $src_mtime === false) // dir
                mkdir("$dst_dir/$file");
            elseif (!isset($dst_tree[$file]) && $src_mtime || isset($dst_tree[$file]) && $src_mtime > $dst_tree[$file])  // file
            {
                if (copy("$src_dir/$file", "$dst_dir/$file"))
                {
                    if($verbose) echo "Copied '$src_dir/$file' to '$dst_dir/$file'<br>\r\n";
                    touch("$dst_dir/$file", $src_mtime);
                    $num++;
                } else
                    echo "<font color='red'>File '$src_dir/$file' could not be copied!</font><br>\r\n";
            }       
        }

        return $num;
}
function get_dir_tree($dir, $root = true)
    {
        static $tree;
        static $base_dir_length;

        if ($root)
        {
            $tree = array(); 
            $base_dir_length = strlen($dir) + 1; 
        }

        if (is_file($dir))
        {
            //if (substr($dir, -8) != "/CVS/Tag" && substr($dir, -9) != "/CVS/Root"  && substr($dir, -12) != "/CVS/Entries")
            $tree[substr($dir, $base_dir_length)] = filemtime($dir);
        } elseif (is_dir($dir) && $di = dir($dir)) // add after is_dir condition to ignore CVS folders: && substr($dir, -4) != "/CVS"
        {
            if (!$root) $tree[substr($dir, $base_dir_length)] = false; 
            while (($file = $di->read()) !== false)
                if ($file != "." && $file != "..")
                    get_dir_tree("$dir/$file", false); 
            $di->close();
        }

        if ($root)
            return $tree;    
    }
