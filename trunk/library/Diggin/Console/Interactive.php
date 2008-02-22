<?php

class Diggin_Console_Interactive
{
    /**
     * @param string $xmlStr
     * @param string $xpathValue
     * @param string $xpathQuery
     * @param string $showMessage
     * @return string
     */
    public function select($xmlStr, $xpathValue, $xpathQuery, $showMessage = "選択してください")
    {
        
        $iterator = new SimpleXMLIterator($xmlStr);
        $find = $iterator->xpath($xpathValue);
        $hit = count($find);
        
        foreach ($find as $key => $value){
            echo mb_convert_encoding($key, 'SJIS', 'utf8');
            echo ":";
            echo mb_convert_encoding($value, 'SJIS', 'utf8');
            echo "\n";
        }  

        require_once 'Zend/Validate/LessThan.php';
        $validator = new Zend_Validate_LessThan($hit);
        
        while (TRUE) {
            echo mb_convert_encoding($showMessage." ", 'SJIS', 'utf8');
          
            $input = trim(fgets(STDIN));
    
            if ($validator->isValid($input)) {
                $key = $iterator->xpath($xpathQuery);
                $return = (string) $key[$input];            
                break;
            } else {
                foreach ($validator->getMessages() as $message) {
                    echo "$message\n";
                }
            }
        }
        
        return $return;
    }
    
    /**
     * Y(yes) or N (no) 
     *
     * @param string
     * @return boolean
     */
    public function yesNo ($showMessage)
    {
        
        while (TRUE) {
            echo mb_convert_encoding($showMessage." ", 'SJIS', 'utf8');
          
            $input = strtolower(trim(fgets(STDIN)));
    
            if (strcmp($input, 'y') === 0){
                $boolean =TRUE;
                break;
            } elseif (strcmp($input, 'n') === 0) {
                $boolean =FALSE;
                break;
            }
            
        }
        
        return $boolean;
    }
}
