<?php

class Diggin_Siteinfo extends ArrayIterator
{
    public function current()
    {
        $curerent = parent::current();
        if (is_array($curerent) && array_key_exists('data', $curerent)){
            return $curerent['data'];
        }

        return $curerent;
    }
}


