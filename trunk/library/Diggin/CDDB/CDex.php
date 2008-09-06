<?php
//とちゅう
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
 * @subpackage CDex
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Diggin_CDDB_CDex
{

    /**
     * @var lastDiscSeek point
     */
    protected $_lastDiscSeek;
    
    public $_localCddbPath;
    
    public function __construct($path = null)
    {
        $this->setLocalCDDBDirPath($path);
    }
    
    public function setLocalCDDBDirPath($path)
    {
        $path = realpath($path);
        if ($path === false) {
            require_once 'Diggin/CDDB/Exception.php';
            throw new Diggin_CDDB_Exception('not valid path');
        }
        
        $this->_localCddbPath = $path;
    }
    
    /**
     * get LocalCDDBDirPath
     *
     * @return string $this->_localCddbPath
     * @throws Diggin_CDDB_Exception
     */
    public function getLocalCDDBDirPath()
    {
        if (!isset($this->_localCddbPath)) {
            require_once 'Diggin/CDDB/Exception.php';
            throw new Diggin_CDDB_Exception('not set path');
        }
        
        return $this->_localCddbPath;
    }
    
    /**
     * get last CDDB local file from CDex
     *
     * @param string $path //  etc. c:\cdex_151\LocalCDDB
     * @return SPLFileInfo $fileInfo;
     * @throws Diggin_CDDB_Exception
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
        require_once 'Diggin/CDDB/Exception.php';
        $errorMsg = sprintf('LocalCDDB Foramt text not found at %s',
                             $this->getLocalCDDBDirPath());
        throw new Diggin_CDDB_Exception($errorMsg);
    }
    
    /**
     * get - last modified Disc info .
     *
     * @param string $path "LocalCDDB"path -like 'c:\cdex_151\LocalCDDB'
     * @return array
     */
    public function getLastDiscInfo()
    {                
        $lastFile = $this->getLastFile()->openFile();
        
        $points = $this->getSeekPointsLatestOfFile($lastFile);
        $lastFile->seek($points['start']);
        
        $disc = array();
        $disc['discid'] = trim(preg_replace('/^DISCID=(.*)(\s)$/i', '$1', $lastFile->current()));
        list($disc['dartist'], $disc['dtitle']) = explode(' / ', $lastFile->fgets(), 2);
        $disc['dtitle'] = trim($disc['dtitle']);
        $disc['dartist'] = ltrim($disc['dartist'], 'DTITLE=');
        $disc['dyear'] = rtrim(ltrim($lastFile->fgets(), 'DYEAR='));
        $disc['dgenre'] = rtrim(ltrim($lastFile->fgets(), 'DGENRE='));
        
        do {
            $title = preg_replace('/^TTITLE(\d*)=(.*)(\s)$/', '$2', $lastFile->fgets());

            //if (preg_match('/^EXTD=/s', $title)) break;
            if ($lastFile->key() > $points['end']) break;
            $disc['tracks'][] = trim($title);
        } while ($lastFile->valid());
        
        return $disc;
    }
    
    /**
     * get Seek point From => DISCID, TO => TTITLE{X}
     * 
     * @param SplFileObject $splFileObject
     * @return array $points 
     */
    public function getSeekPointsLatestOfFile(SplFileObject $splFileObject)
    {
        $line = count(file($splFileObject->getPathName()));
        for ($i = 1; $i < $line; $i++) {
            $splFileObject->seek($line - $i);
            //cdex comment is /^#FILE/
            if (preg_match('/^EXTD.*/i', $splFileObject->current())) {
                $end = $splFileObject->key() -1;
            }
            
            if (preg_match('/^DTITLE.*$/s', $splFileObject->current())) {
                return array('start' => ($line - $i), 'end' => $end);
            }
        }
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
     * @throws Diggin_CDDB_Exception
     */
    public function rewriteLastDiscInfo(array $discArray)
    {
        $lastFile = $this->getLastFile();
        
        $points = $this->getSeekPointsLatestOfFile($lastFile->openFile());
        
        if (!file_put_contents($lastFile, 
                               $this->getRewriteStr($lastFile, $points, $discArray))) {
            require_once 'Diggin/CDDB/Exception.php';
            throw new Diggin_CDDB_Exception('couldnt rewrite');
        }
        
        return true;
    }
    
	/**
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
        $fileArray = file($file);

        $rewriteStr = implode('', array_slice($fileArray, 0, $rewrite_points['start']));
        $rewriteStr .=  'DTITLE='.$disc['dartist'].' / '.$disc['dtitle'].PHP_EOL.
                        'DYEAR='.$disc['dyear'].PHP_EOL.
                        'DGENRE='.$disc['dgenre'].PHP_EOL;
        $trackStr = '';
        $trackCount = $rewrite_points['end'] - $rewrite_points['start'] - 3;
        for ($i = 0; $i <= $trackCount; $i++) {
            $track = isset($disc['tracks'][$i]) ? $disc['tracks'][$i] : false;
            if (!$track) {
                //@todo various fomart by sprintf
                $trackStr .= "TTITLE$i=".$disc['dtitle'].'_'.($i+1).PHP_EOL;
            } else {
                $trackStr .= "TTITLE$i=".$track.PHP_EOL;
            }
        }
        $rewriteStr .= $trackStr;
        $rewriteStr .= implode('', array_slice($fileArray, $rewrite_points['end'] +1));

        //@todo
        //mb_convert_encoding($rewriteStr, '?', 'utf8');
        //
        return mb_convert_encoding($rewriteStr, 'SJIS', 'utf8');  
    }
}
