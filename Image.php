<?php

/**
 * Zend Rad Aid
 *
 * LICENCIA
 *
 * Este archivo está sujeta a la licencia CC(Creative Commons) que se incluye
 * en LICENCIA.txt.
 * Tambien esta disponible a traves de la Web en la siguiente direccion
 * http://www.zend-rad.com/licencia/
 * Si usted no recibio una copia de la licencia por favor envie un correo
 * electronico a <licencia@zend-rad.com> para que podamos enviarle una copia
 * inmediatamente.
 *
 * @author Juan Minaya Leon <info@juanminaya.com>
 * @copyright Copyright (c) 2011-2012 , Juan Minaya Leon
 * (http://www.zend-rad.com)
 * @licencia http://www.zend-rad.com/licencia/   CC licencia Creative Commons
 */
class ZradAid_Image
{

    /**
     * 0.5Mb por defecto
     * 
     * @param bigint
     */
    private $_maxWeight = 524288;

    /**
     * @var int
     */
    private $_minHeight = 50;

    /**
     * @var int
     */
    private $_minWidth = 50;

    /**
     * @var int
     */
    private $_width = 50;

    /**
     * @param int
     */
    private $_height = 50;

    /**
     * @var array
     */
    private $_extensions;
    
    /**
     * @var
     */
    private $_renameExtension = '';

    /**
     * @param string
     */
    private $_path = '';
    
    /**
     * @param string
     */
    private $_relativePath = '';

    /**
     * inicializamos
     * @param array $init
     */
    public function __construct($init)
    {
        if (Zend_Registry::isRegistered('configImages')) {
            // Get Entity
            $configImages = Zend_Registry::get('configImages');
            $configField = $configImages->thumb->$init['entity']->$init['field'];
            $this->_width = $configField->resize->width;
            $this->_height = $configField->resize->height;
            $this->_maxWeight = $configField->validator->maxWeight;
            $this->_minWidth = $configField->validator->minWidth;
            $this->_minHeight = $configField->validator->minHeight;            
            $this->_extensions = explode(',', $configField->validator->extension);
            $this->_renameExtension = $configField->rename->extension;
            $this->_path = UPLOAD_PATH . '/' . $init['entity'] . '/images';
            $this->_relativePath = 'uploads/' . $init['entity'] . '/images';
        }
    }
   
    public function getRelativePath()
    {
        return $this->_relativePath;
    }

    public function getRenameExtension()
    {
        return $this->_renameExtension;
    }

        
    /**
     * @return string
     */
    public function getStrDimensions()
    {
        return $this->_width . 'x' . $this->_height . 'px';
    }

    /**
     * @return string
     */
    public function getStrWeight()
    {
        // Description
        $result = $this->_maxWeight / 1048576;
        $result = $this->_round($result, 2);
        if ($result >= 1) {
            $size = $result . 'MB';
        } else {
            $result = $this->_maxWeight / 1024;
            $size = $result . 'KB';
        }
        
        return $size;
    }

    public function getExtensions()
    {
        return $this->_extensions;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getMaxWeight()
    {
        return $this->_maxWeight;
    }

    public function getMinHeight()
    {
        return $this->_minHeight;
    }

    public function getMinWidth()
    {
        return $this->_minWidth;
    }

    public function getWidth()
    {
        return $this->_width;
    }

    public function getHeight()
    {
        return $this->_height;
    }

}
