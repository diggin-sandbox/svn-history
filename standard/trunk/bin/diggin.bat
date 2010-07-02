@ECHO off
REM Diggin
REM
REM This code was mostly adapted from Zend Framework
REM Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
REM http://framework.zend.com/license/new-bsd     New BSD License


REM Test to see if this was installed via pear
SET ZTMPZTMPZTMPZ=@ph
SET TMPZTMPZTMP=%ZTMPZTMPZTMPZ%p_bin@
REM below @php_bin@
FOR %%x IN ("@php_bin@") DO (if %%x=="%TMPZTMPZTMP%" GOTO :NON_PEAR_INSTALLED)

GOTO PEAR_INSTALLED

:NON_PEAR_INSTALLED
REM Assume php.exe is executable, and that diggin.php will reside in the
REM same file as this one
SET PHP_BIN=php.exe
SET PHP_DIR=%~dp0
GOTO RUN

:PEAR_INSTALLED
REM Assume this was installed via PEAR and use replacements php_bin & php_dir
SET PHP_BIN=@php_bin@
SET PHP_DIR=@php_dir@
GOTO RUN

:RUN
SET DIGGIN_SCRIPT=%PHP_DIR%\diggin.php
"%PHP_BIN%" -d safe_mode=Off -f "%DIGGIN_SCRIPT%" -- %*
