<?php

class Diggin_RobotRules_Protocol_Txt_Record
{
    private $_fields = array();

    public function __get($field)
    {
        if (array_key_exists($this->_fields)) {
            return $this->_fields[$field];
        } else {
            return null;
        }
    }

    /** Diggin_RobotRules_Protocol_Txt_line  **/
    public function append(Diggin_RobotRules_Protocol_Txt_line $line)
    {
        if (array_key_exists($field = $line->getField(), $this->_fields)) {
            $this->_fields[$field][count($field)] = $line;
        } else {
            $this->_fields[$field][0] = $line;
        }
    }

    protected function toString()
    {
        $fieldstring = "";
        uksort($this->_fields, array($this, '_fieldsort'));
        foreach ($this->_fields as $field) {
            $fieldstring .= implode("\n", $field);
        }
        return $fieldstring;
    }

    /**
     * note: User-Agent must first. 
     * but Allow & Disallow which not define sort
     * http://www.robotstxt.org/norobots-rfc.txt 3.2
     */
    private function _fieldsort($x, $y)
    {
        //if ($x ==  $y);
        //var_dump($x, $y);

        if (strtolower($x) === 'user-agent') return -10;
        if (strtolower($y) === 'user-agent') return 10;

        if (strtolower($x) === 'disallow' && strtolower($y) == 'allow') {
            return -1;
        } else if (strtolower($x) === 'allow' && strtolower($y) == 'disallow') {
            return 1;
        }
        return 0;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
