<?php

/**
 * Original code borrowed from HTMLScraping
 * 
 * @see http://www.rcdtokyo.com/etc/htmlscraping/
 *
 * ---------------------------------------------------------------------
 * HTMLScraping class
 * ---------------------------------------------------------------------
 * PHP versions 5 (5.1.3 and later)
 * ---------------------------------------------------------------------
 * LICENSE: This source file is subject to the GNU Lesser General Public
 * License as published by the Free Software Foundation;
 * either version 2.1 of the License, or any later version
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl.html
 * If you did not have a copy of the GNU Lesser General Public License
 * and are unable to obtain it through the web, please write to
 * the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 * ---------------------------------------------------------------------
 */

/**
 * Diggin - Simplicity PHP Library
 * 
 * @category   Diggin
 * @package    Diggin_Http
 * @subpackage Response
 */
class Diggin_Http_Response_CharactorEncoding
{
    const DETECT_ORDER = 'ASCII, JIS, UTF-8, EUC-JP, SJIS';

    /**
     * @var string $_detectOrder
     */
    private static $_detectOrder = 'ASCII, JIS, UTF-8, EUC-JP, SJIS';
    
    /**
     * Set detect-order (static)
     *
     * @param string $order
     */
    public static function setDetectOrder($order)
    {
        if ($order === false) {
            self::$_detectOrder = self::DETECT_ORDER;
        } else {
            self::$_detectOrder = $order;
        }
    }

    /**
     * Get detecting order
     *
     * @return string
     */
    public static function getDetectOrder()
    {
        return self::$_detectOrder;
    }

    /**
     * Convert character encoding - mbstring or iconv
     *
     * @param mixed $vars
     * @param string $encodingfrom
     * @param string $encodingto
     * @return mixed
     * @throws Diggin_Http_Response_CharactorEncoding_Exception
     */
    public static function mbconvert($vars, $encodingfrom, $encodingto = 'UTF-8')
    {
        if (extension_loaded('mbstring')) {
            @mb_convert_variables($encodingto, $encodingfrom, $vars);
        } else {
            if (is_string($vars)) {
                $vars = array($vars);
            }
            foreach ($vars as $key => $value) {
                if (false === $convertVars[$key] = @iconv($encodingfrom, $encodingto, $value)) {
                    require_once 'Diggin/Http/Response/CharactorEncoding/Exception.php';
                    throw new Diggin_Http_Response_CharactorEncoding_Exception('Failed converting character encoding.');
                }
            }
        }

        return $vars;
    }

    /**
     * Create Wrapper instance accoring param's Response Object
     *
     * @param Object $response
     * @param string $encodingto
     * @return mixed
     */
    public static function createWrapper($response, $encodingto = 'UTF-8')
    {
        if ($response instanceof Zend_Http_Response) {
            $detect = self::detect($response);
            require_once 'Diggin/Http/Response/CharactorEncoding/Wrapper/Zf.php';
            return Diggin_Http_Response_CharactorEncoding_Wrapper_Zf::createWrapper($response, $detect, $encodingto);
        } else {
            require_once 'Diggin/Http/Response/CharactorEncoding/Exception.php';
            throw new Diggin_Http_Response_CharactorEncoding_Exception('Unknown Object Type..');
        }
    }
    
    /**
     * Detect response's character code name
     *
     * @param string $responseBody
     * @param string $contentType
     * @return string $encoding
     */
    public static function detect($responseBody, $contentType = null)
    {
        $encoding = false;
        if (isset($contentType)) {
            $encoding = self::_getCharsetFromCType($contentType);
        }
        if (!$encoding and preg_match_all('/<meta\b[^>]*?>/si', $responseBody, $matches)) {
            foreach ($matches[0] as $value) {
                if (strtolower(self::_getAttribute('http-equiv', $value)) == 'content-type'
                    and false !== $encoding = self::_getAttribute('content', $value)) {
                    $encoding = self::_getCharsetFromCType($encoding);
                    break;
                }
            }
        }

        /*
         * Use mbstring to detect character encoding if available.
         */
        if (extension_loaded('mbstring') and !$encoding) {
            $detectOrder = mb_detect_order();
            mb_detect_order(self::getDetectOrder());
            if (false === $encoding = mb_preferred_mime_name(mb_detect_encoding($responseBody))) {
                mb_detect_order($detectOrder);//restore
                require_once 'Diggin/Http/Response/CharactorEncoding/Exception.php';
                throw new Diggin_Http_Response_CharactorEncoding_Exception('Failed detecting character encoding.');
            }
            mb_detect_order($detectOrder);//restore
        }
        
        return $encoding;
    }

    /**
     * Get Charset From Ctype
     * 
     * @param  string  $string
     * @return mixed
     */
    protected static function _getCharsetFromCType($string)
    {
        $array = explode(';', $string);
        /* array_walk($array, create_function('$item', 'return trim($item);')); */
        if (isset($array[1])) {
            $array = explode('=', $array[1]);
            if (isset($array[1])) {
                $charset = trim($array[1]);
                if (preg_match('/^UTF-?8$/i', $charset)) {
                    return 'UTF-8';
                } elseif (function_exists('mb_preferred_mime_name')) {
                    return @mb_preferred_mime_name($charset);
                } else {
                    return $charset;
                }
            }
        }
        return false;
    }

    /**
     * Get Attribute from meta-tags
     * 
     * @param string $name:
     * @param string $string:
     * @return mixed
     */
    protected static function _getAttribute($name, $string)
    {
        $search = "'[\s\'\"]\b".$name."\b\s*=\s*([^\s\'\">]+|\'[^\']+\'|\"[^\"]+\")'si";
        if (preg_match($search, $string, $matches)) {
            return preg_replace('/^\s*[\'\"](.+)[\'\"]\s*$/s', '$1', $matches[1]);
        } else {
            return false;
        }
    }
}
