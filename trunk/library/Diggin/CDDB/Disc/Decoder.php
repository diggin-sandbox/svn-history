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
 * @subpackage Disc
 * @copyright  2006-2008 sasezaki (http://diggin.musicrider.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Diggin_CDDB_Disc_Decoder implements Diggin_CDDB_Disc_Interface
{    
    /**
     * decoding CDDB format
     *
     * @param string $encodedValue
     * @param int $objectDecodeType Optional; flag indicating how to decode
     * objects.
     * @return mixed
     */
    public static function decode($encodedValue, $objectDecodeType = Diggin_CDDB_Disc_Decoder::TYPE_OBJECT, $encoding = 'SJIS')
    {
        if ($encoding !== 'utf8') {
           $encodedValue = mb_convert_encoding($encodedValue, 'utf8', $encoding);
        }
                
        require_once 'Net/CDDB/Utilities.php';
        $ncu = new Net_CDDB_Utilities();
        
        $record = $ncu->parseRecord($encodedValue);
        
        if ($objectDecodeType === Diggin_CDDB_Disc_Decoder::TYPE_OBJECT)
        {
            require_once 'Net/CDDB/Disc.php';
            $record = new Net_CDDB_Disc($record);
        }
        
        return $record;
    }
}