<?php
/**
 * Diggin - Simplicity PHP Library
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license.
 * http://framework.zend.com/license/new-bsd
 * 
 * @category   Diggin
 * @package    Diggin_CDDB
 * @subpackage Application_CDex
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
/**
 * @see Diggin_CDDB_Disc_Encoder
 */
require_once 'Diggin/CDDB/Disc/Encoder.php';
/**
 * @see Diggin_CDDB_Disc_Decoder
 */
require_once 'Diggin/CDDB/Disc/Decoder.php';

class Diggin_CDDB_Application_CDex
{
    protected $_localCddbPath;
    
    public function __construct($path = null)
    {
        $this->setLocalCDDBDirPath($path);
    }
    
    public function setLocalCDDBDirPath($path)
    {
        $path = realpath($path);
        if ($path === false) {
            require_once 'Diggin/CDDB/Application/Exception.php';
            throw new Diggin_CDDB_Application_Exception('not valid path');
        }
        
        $this->_localCddbPath = $path;
    }
    
    /**
     * get LocalCDDBDirPath
     *
     * @return string $this->_localCddbPath
     * @throws Diggin_CDDB_Application_Exception
     */
    public function getLocalCDDBDirPath()
    {
        if (!isset($this->_localCddbPath)) {
            require_once 'Diggin/CDDB/Application/Exception.php';
            throw new Diggin_CDDB_Application_Exception('not set path');
        }
        
        return $this->_localCddbPath;
    }
    
    /**
     * get last CDDB local file from CDex
     *
     * @param string $path //  etc. c:\cdex_151\LocalCDDB
     * @return SPLFileInfo $fileInfo;
     * @throws Diggin_CDDB_Application_Exception
     */
    public function getLastFile()
    {       
        $rii = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($this->getLocalCDDBDirPath()));
        
        $ai = new ArrayIterator(array());
        do {
            if ($rii->getDepth() == 1 and (substr($rii->current()->getPath(), -6) != 'Status')) {
                $ai->offsetSet($rii->current()->getATime(), $rii->current());
            }
            $rii->next();
        } while ($rii->valid());
        
        $ai->ksort();
        $ai->rewind();
        $c = count($ai);
        for ($i = 1; $i < $c; $i++) {
            $ai->seek($c - $i);
            $fileInfo = $ai->current()->getFileInfo();
            if ($fileInfo->isFile() and 
                ( '#FILENAME=' === substr($fileInfo->openFile()->current(), 0, 10) )) {
                return $fileInfo;
            }
        }
        
        //if not found
        require_once 'Diggin/CDDB/Application/Exception.php';
        $errorMsg = sprintf('LocalCDDB Foramt text not found at %s',
                             $this->getLocalCDDBDirPath());
        throw new Diggin_CDDB_Application_Exception($errorMsg);
    }
    
    /**
     * get Seek point From => DISCID, TO => TTITLE{X}
     * 
     * @param SplFileObject $splFileObject
     * @return array $points 
     */
    public function getSeekPointsLatestOfFile(SplFileObject $splFileObject, $start = '#FILENAME', $end = 'PLAYORDER')
    {
        $line = count(file($splFileObject->getPathName()));
        for ($i = 1; $i <= $line; $i++) {
            $splFileObject->seek($line - $i);
            //cdex comment is /^#FILE/
            if (preg_match("/^$end.*/i", $splFileObject->current())) {
                $end = $splFileObject->key();
            }
            if (preg_match("/^$start.*/i", $splFileObject->current())) {
                return array('start' => ($line - $i), 'end' => $end);
            }
        }
        
        //if none
        
    } 
        
    /**
     * get - last modified Disc info .
     *
     * @param string $style
     * @return mixed
     */
    public function getLastDisc($decodeType = Diggin_CDDB_Disc_Decoder::TYPE_OBJECT)
    {                
        $lastFile = $this->getLastFile()->openFile();

        $points = $this->getSeekPointsLatestOfFile($lastFile, '#FILENAME', 'PLAYORDER');
        $discStr = implode('', array_slice(file($lastFile), $points['start'] +1, $points['end']));

        return Diggin_CDDB_Disc_Decoder::decode($discStr, $decodeType);
    }
    
    /**
     * Rewrite lastest Disc Info under LocalCDDB
     *
     * @param array 
     * sample:
     * $disc = array(
     *     'dtitle' => "Album Title",
     *     'dartist'=> "Artist Name",
     *     'dyear' => "2008",
     *     'dgenre' =>"Unknown", 
     *     'tracks' => array('title1','test2','test3','test4')
     * );
     * @return boolean
     * @throws Diggin_CDDB_Application_Exception
     */
    public function rewriteLastRecord($discArray)
    {
        $lastFile = $this->getLastFile();
        
        $points = $this->getSeekPointsLatestOfFile($lastFile->openFile(), '#FILENAME', 'PLAYORDER');

        if (!file_put_contents($lastFile, 
                               $this->getRewriteStr($lastFile, $points, $discArray))) {
            require_once 'Diggin/CDDB/Application/Exception.php';
            throw new Diggin_CDDB_Application_Exception('couldnt rewrite');
        }
        
        return true;
    }
    
	/**
     * 
     * @todo merge Net_CDDB_Disc object | array
     * 
     * @param string | SPLFileInfo
     * @param array 
     * @param array $disc
     * 
     * 
     * NOTES:
     * 0
     * 1
     * 2 <<if'start' ==3  'length' of array_slice is here
     * 3 DTITLE <=rewrite_points['start'])
     * 4 DYEAR
     * 5 DGENRE
     * 6 TTITLE0
     * 7 TTITLE1 <=$rewrite_points['end'] (last track )
     * 8 EXTD= 
     */
    public function getRewriteStr($file, $rewrite_points, $disc)
    {
        //extract rewritepart string
        $rewriteStr = implode('', array_slice(file($file), $rewrite_points['start'] +1 , $rewrite_points['end']));

        $decode = Diggin_CDDB_Disc_Decoder::decode($rewriteStr, Diggin_CDDB_Disc_Decoder::TYPE_ARRAY, 'SJIS');

        //merge
        foreach ($disc as $k => $v) {
            if ($k === 'tracks') {
                foreach ($v as $c => $track) {
                    $decode['tracks'][$c]['ttitle'] = $track;
                }
            } else {
                $decode[$k] = $v;
            }
        }
        
        //__toString
        $rewriteStr = Diggin_CDDB_Disc_Encoder::encode($decode);
        
        //#filename line add
        $file->openFile()->seek($rewrite_points['start'] );
        $rewriteStr = $file->openFile()->current().$rewriteStr.PHP_EOL;
        
        return $rewriteStr;
    }

}