<?php
class Diggin_RobotRules_Line
{
    private $_field;
    private $_value;
    private $_comment;

    /**
     *
     * @string $line
     * @return Diggin_RobotRules_Line
     */
    public static function parse($line)
    {
        //preg
        
        $robotsline = new self();
        $self->setField($mathes[1]);

        return $robotsline;
    }

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
        if (isset($this->_field)) {
            return $this->_field;
        } else {
            return '';
        }
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