<?php

require_once 'ZFDebug/Controller/Plugin/Debug/Plugin/File.php';

class DigginX_Controller_Plugin_ZFDebug_Plugin_Ref
        extends ZFDebug_Controller_Plugin_Debug_Plugin_File
{
    protected $_sourcePath = array();

    protected $_manualPath = array();

    public function __construct(array $options = array())
    {
        parent::__construct($options);
        
        //default source path
        $this->setSourcePath('Zend', 'http://framework.zend.com/code/browse/Standard_Library/standard/trunk/library/', '?r=trunk');
        $this->setSourcePath('ZendX', 'http://framework.zend.com/code/browse/Extras_Library/trunk/library/', '?r=trunk');
        $this->setSourcePath('ZFDebug', 'http://code.google.com/p/zfdebug/source/browse/trunk/library/');
        

        $this->setManualPath('Zend', 'http://framework.zend.com/manual/ja/');

        //$this->_sourcePath = array_merge($sourcePath, $options['source_path']);
    }

    /**
     * set reference path and suffix
     *
     * there is a lot of path....
     * http://framework.zend.com/svn/framework/standard/branches/release-1.8/library/
     * http://framework.zend.com/svn/framework/standard/tags/release-1.8.1/library/
     * http://framework.zend.com/code/browse/Standard_Library/standard/trunk/library/Zend/Filter/Input.php?r=trunk
     * 
     * //
     * require_once 'Zend/Version.php';
     * $version = Zend_Version::VERSION; 
     * @todo not-stable (dev version 1.8.0dev-etc)'s repo not exists;
     * $this->setSourcePath('Zend', "http://framework.zend.com/svn/framework/standard/tags/release-$version/library/");
     *
     * $this->setSourcePath('Zend', 'http://framework.zend.com/code/browse/Standard_Library/standard/trunk/library/', '?r=trunk');
     * $this->setSourcePath('ZendX', 'http://framework.zend.com/code/browse/Extras_Library/trunk/library/', '?r=trunk');
     * //
     *
     * @param string $libraryName
     * @param string $sourcePath
     * @param string $suffix
     */
    public function setSourcePath($libraryName, $sourcePath, $suffix = null)
    {
        $this->_sourcePath = array_merge($this->_sourcePath, 
                                   array($libraryName => array('src' => $sourcePath, 'suffix' => $suffix)));
    }

    /**
     *
     */
    public function setManualPath($libraryName, $manualPath)
    {
        $this->_manualPath = array_merge($this->_manualPath, array($libraryName => $manualPath));
    }

    protected function getManualPath($file)
    {
        //lazy return - each component's top page.
        return strtolower(preg_replace('#(^/)(([A-Za-z]+)/([A-Za-z]+).*)#', '\3.\4', $file)).'.html';
    }

    /**
     * [ original code borrowed from ZFDebug]
     * 
     * License available at:
     * http://zfdebug.googlecode.com/svn/trunk/license.txt
     */
    public function getPanel()
    {

        $included = $this->_getIncludedFiles();
        $html = '<h4>File Information</h4>';
        $html .= count($included).' Files Included<br />';
        $size = 0;
        foreach ($included as $file) {
            $size += filesize($file);
        }
        $html .= 'Total Size: '. round($size/1024, 1).'K<br />';
        
        $html .= 'Basepath: ' . $this->_basePath . '<br />';

        $libraryFiles = array();
        foreach ($this->_library as $key => $value) {
            if ('' != $value) {
                $libraryFiles[$key] = '<h4>' . $value . ' Library Files</h4>';
            }
        }

        $html .= '<h4>Application Files</h4>';
        foreach ($included as $file) {
            $file = str_replace($this->_basePath, '', $file);
            $inUserLib = false;
                foreach ($this->_library as $key => $library)
                {
                        if('' != $library && false !== strstr($file, $library)) {

                                
                                if (isset($this->_sourcePath[$library])) {
                                    
                                    // clear include_path 
                                    $path = str_replace('.', '', explode(PATH_SEPARATOR, get_include_path()));
                                    $relativefile = substr(str_replace($path, '' , $file), 1);
                                    $souceBase = $this->_sourcePath[$library]['src'];
                                    $suffix = $this->_sourcePath[$library]['suffix'];

                                    $file .= ' <a href="'.$souceBase.$relativefile.$suffix.'">src</a>';
                                }

                                if (isset($this->_manualPath[$library])) {
                                    
                                    $path = str_replace('.', '', explode(PATH_SEPARATOR, get_include_path()));
                                    $manpath = $this->getManualPath(str_replace($path, '', $file));

                                    $file .= ' <a href="'.$this->_manualPath[$library].$manpath.'">man</a>';
                                }

                                $libraryFiles[$key] .= $file.'<br>';
                                $inUserLib = TRUE;
                        }
                }
                if (!$inUserLib) {
                        $html .= $file . '<br />';
                }
        }

        $html .= implode('', $libraryFiles);



        return $html;
    }


}
