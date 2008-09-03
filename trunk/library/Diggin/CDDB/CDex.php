<?php
//とちゅう
class Diggin_CDDB_CDex
{

    /**
     * @var lastDiscSeek point
     */
    protected $_lastDiscSeek;
    
    /*
     * @var Disc (Net_CDDB_Disc)
     */
    public $disc;
    
    public $_localCddbPath;
    
    public function __construct($path = null)
    {
        $this->_localCddbPath = $path;
    }
    
    /**
     * get last CDDB local file from CDex
     *
     * @param string $path //  etc. c:\cdex_151\LocalCDDB
     * @return SPLFileInfo $fileInfo;
     * @throws Diggin_CDDB_Exception
     */
    public static function getLastFile($path)
    {
        $path = realpath($path);
        if ($path === false) {
            require_once 'Diggin/CDDB/Exception.php';
            throw new Diggin_CDDB_Exception('not valid path');
        }
        
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath($path)));
        
        $ai = new ArrayIterator(array());
        do {
            if ($rii->getDepth() == 1 and (substr($rii->current()->getPath(), -6) != 'Status')) {
                $ai->offsetSet($rii->current()->getATime(), $rii->current());
            }
            $rii->next();
        } while ($rii->valid());
        
        $ai->ksort(); //oops! ArrayIterator::krsort is not defined!
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
        throw new Diggin_CDDB_Exception('not valid LocalCDDB path, etc. c:\cdex_151\LocalCDDB');
    }
    
    /**
     * get - last modified Disc info .
     *
     * @param string $path "LocalCDDB"path -like 'c:\cdex_151\LocalCDDB'
     * @return Net_CDDB_Disc
     */
    public function getLastDiscInfo($path)
    {
        $fileInfo = self::getLastFile($path);
                
        $splFileObject = $fileInfo->openFile();
        
        $splFileObject->seek($this->_getSeekStartLatestOfFile($splFileObject));
        
        $disc = array();
        $disc['discid'] = rtrim(ltrim($splFileObject->getCurrentLine(), 'DISCID='));
        list($disc['dartist'], $disc['dtitle']) = explode(' / ', $splFileObject->getCurrentLine(), 2);
        $disc['dtitle'] = trim($disc['dtitle']);
        $disc['dartist'] = ltrim($disc['dartist'], 'DTITLE=');
        $disc['dyear'] = rtrim(ltrim($splFileObject->getCurrentLine(), 'DYEAR='));
        $disc['dgenre'] = rtrim(ltrim($splFileObject->getCurrentLine(), 'DGENRE='));
        
        $tracks = array('0');
        //
        do {
            $title = trim(preg_replace('/^TTITLE(\d*)=/s', '${2}', $splFileObject->getCurrentLine()));
            if (preg_match('/^EXTD=/s', $title)) break;
            $tracks[] = $title;
        } while ($splFileObject->valid());
        
        require_once 'Net/CDDB/Disc.php';
        $ncd = new Net_CDDB_Disc();
        $ncd->Net_CDDB_Disc($disc);
        $ncd->_tracks = $tracks;
        return $ncd;
    }
    
    private function _getSeekStartLatestOfFile(SplFileObject $splFileObject)
    {
        $line = count(file($splFileObject->getPathName()));
        for ($i = 2; $i < $line; $i++) {
            $splFileObject->seek($line-$i);
            //cdex comment is /^#FILE/
            if (preg_match('/^DISCID.*$/s', $splFileObject->getCurrentLine())) {
                return $line-$i;
            }
        }
    }
    
    
    public function rewriteLastDiscInfo(Net_CDDB_Disc $ncd)
    {
        if (!$this->_localCddbPath) {
            require_once 'Diggin/CDDB/Exception.php';
            throw new Diggin_CDDB_Exception('must set Path, before calling this method');
        }
        
        $fileInfo = self::getLastFile($this->_localCddbPath);
        
        $splFileObject = $fileInfo->openFile();
        
        $splFileObject->seek($this->_getSeekStartLatestOfFile($splFileObject));
        
//        return $splFileObject;
    }
}

$path = 'D:\zip\cdex_151\LocalCDDB';
$cddb = new Diggin_CDDB_CDex();
var_dump($cddb->rewriteLastDiscInfo($path));
