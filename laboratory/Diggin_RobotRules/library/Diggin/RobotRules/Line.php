<?php

class Diggin_RobotRules_Line
{
    private $_field;
    private $_value;
    private $_comment;

    public function __toString()
    {
        return $this->getField().':'.$this->getValue().
            (isset($this->_comment) ? ' # '.$this->getComment(): '')."\n";
    }
    public function setField($field)
    {
        $this->_field = $field;
    }
    public function getField()
    {
        return $this->_field;
    }
    public function setValue($value)
    {
        $this->_value = $value;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function setComment($comment)
    {
        $this->_comment = $comment;
    }
    public function getComment()
    {
        return $this->_comment;
    }
}

