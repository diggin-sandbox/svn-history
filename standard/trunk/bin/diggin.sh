#!/bin/sh

#############################################################################
# Diggin
#
# This code was mostly adapted from Zend Framework.
# Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
# http://framework.zend.com/license/new-bsd     New BSD License
#############################################################################


# find php: pear first, command -v second, straight up php lastly

if test "@php_bin@" != '@'php_bin'@'; then
    PHP_BIN="@php_bin@"
elif command -v php 1>/dev/null 2>/dev/null; then
    PHP_BIN=`command -v php`
else
    PHP_BIN=php
fi

# find diggin.php: pear first, same directory 2nd, 
if test "@php_dir@" != '@'php_dir'@'; then
    PHP_DIR="@php_dir@"
else
    SELF_LINK="$0"
    SELF_LINK_TMP="$(readlink "$SELF_LINK")"
    while test -n "$SELF_LINK_TMP"; do
        SELF_LINK="$SELF_LINK_TMP"
        SELF_LINK_TMP="$(readlink "$SELF_LINK")"
    done
    PHP_DIR="$(dirname "$SELF_LINK")"
fi

"$PHP_BIN" -d safe_mode=Off -f "$PHP_DIR/diggin.php" -- "$@"

