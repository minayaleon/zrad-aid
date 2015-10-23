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
require_once 'ZradAid/Image/PHPThumb/ThumbLib.inc.php';

class ZradAid_Form_Element_File extends Zend_Form_Element_File
{

    /**
     * nombre del archivo fisico
     *
     * @var string
     */
    private $_fileValue = null;

    /**
     * Tipo de archivo image,document
     *
     * @var string
     */
    private $_fileType;
    private $_entity;

    /**
     * The current dimensions of the image
     *
     * @var array
     */
    private $_currentDimensions;

    /**
     *
     */
    private $_isDelete;
    
    /**
     * @var addPath Subcarpeta adicional
     */
    private $_subPath = '';
    
    /**
     * @var boolean
     */
    private $_isResize = true;

    public function setIsDelete($flag = true)
    {
        $this->_isDelete = (bool) $flag;
    }

    public function getIsDelete()
    {
        return $this->_isDelete;
    }

    public function isDelete()
    {
        return $this->_isDelete;
    }

    public function getEntity()
    {
        return $this->_entity;
    }

    public function setEntity($_entity)
    {
        $this->_entity = $_entity;
    }
    
    public function setResize($resize)
    {
        $this->_isResize = $resize;
    }
    
    /**
     * @return string Ruta Adicional
     */
    public function getSubPath()                
    {
        return $this->_subPath;
    }

    /**
     * @param float|int $number Numero a redondear
     * @param int $decimal Numero de decimales 
     * @return float|int
     */
    private function _round($number, $decimal)
    {
        $factor = pow(10, $decimal);
        return (round($number * $factor) / $factor);
    }

    /**
     *
     * @var array $init
     */
    public function setInitialization(array $init)
    {
        // Vaciamos la sesion de formularios
        $formSession = new Zend_Session_Namespace('formSession');
        unset($formSession->elements);

        $this->_entity = $init['entity'];
        $this->_fileType = $init['type'];
        if ($this->_fileType == 'image') {
            if (Zend_Registry::isRegistered('configImages')) {
                // Get Entity
                $configImages = Zend_Registry::get('configImages');
                $configField = $configImages->thumb->$init['entity']->$init['field'];
                // Description
                $dimensiones = (string) $configField->resize->width . 'x' . $configField->resize->height;
                $result = $configField->validator->maxWeight / 1048576;
                $result = $this->_round($result, 2);
                if ($result >= 1) {
                    $size = $result . 'MB';
                } else {
                    $result = $configField->validator->maxWeight / 1024;
                    $size = $result . 'KB';
                }
                $this->setDescription('Peso m&aacute;ximo ' . $size . ', dimensiones ' . $dimensiones . 'px, formato ' . $configField->validator->extension);
                // Destination
                $this->_subPath = (isset($init['path']))  ? $init['path'] . '/' : '';
                $entityPath = $this->_getEntityPath($init['entity']);
                $this->setDestination(UPLOAD_PATH . '/' . $entityPath . '/images/' . $this->_subPath);
                // Validators
                $this->addValidator('Size', false, array(
                    'min' => $configField->validator->minWeight,
                    'max' => $configField->validator->maxWeight
                ));
                $this->addValidator('Extension', false, $configField->validator->extension);
                $this->addValidator('ImageSize', false, array(
                    'minwidth' => $configField->validator->minWidth,
                    'minheight' => $configField->validator->minHeight
                ));

                // Obtenemos la extension del archivo
                $fileInfo = array();
                $fileInfo['extension'] = $configField->rename->extension;
                if (empty($fileInfo['extension'])) {
                    $fileUploaded = $this->getFileName(null, false);
                    if (!empty($fileUploaded))
                        $fileInfo = pathinfo($fileUploaded);
                }
                $this->addFilter('Rename', ZradAid_String::generateName($fileInfo['extension']));
                if ($this->_isResize)
                    $this->addFilter(new ZradAid_Filter_File_Resize($init['entity'], $init['field']));
            }
        } else {
            if (Zend_Registry::isRegistered('configFiles')) {
                $configFiles = Zend_Registry::get('configFiles');
                $configField = $configFiles->file->$init['entity']->$init['field'];
                // Description
                $result = $configField->validator->maxWeight / 1048576;
                $result = $this->_round($result, 2);
                if ($result >= 1) {
                    $size = $result . 'MB';
                } else {
                    $result = $configField->validator->maxWeight / 1024;
                    $size = $result . 'KB';
                }
                $this->setDescription('Peso m&aacute;ximo ' . $size . ', formato ' . $configField->validator->extension);
                // Destination
                $entityPath = $this->_getEntityPath($init['entity']);                
                $this->setDestination(UPLOAD_PATH . '/' . $entityPath . '/files');
                // Validators
                $this->addValidator('Size', false, array(
                    'min' => $configField->validator->minWeight,
                    'max' => $configField->validator->maxWeight
                ));
                $this->addValidator('Extension', false, $configField->validator->extension);
                // Obtenemos la extension del archivo
                $fileUploaded = $this->getFileName(null, false);
                $fileInfo['extension'] = '';
                if (!empty($fileUploaded))
                    $fileInfo = pathinfo($fileUploaded);

                // Filters
                $this->addFilter('Rename', ZradAid_String::generateName($fileInfo['extension']));
                $this->addFilter(new ZradAid_Filter_File_DeleteEdit($init['field']));
            }
        }

        // Required Delete
        if (!$this->isRequired()) {
            $this->_isDelete = true;
        }
    }

    public function getFileType()
    {
        return $this->_fileType;
    }

    public function setFileType($fileType)
    {
        $this->_fileType = $fileType;
    }

    public function getFileValue()
    {
        return $this->_fileValue;
    }

    public function setFileValue($_fileValue)
    {
        $this->_fileValue = $_fileValue;
    }

    /**
     * habilitando populate
     */
    public function setValue($value)
    {
        $this->_fileValue = $value;

        // Verificamos si no es vacio
        if (!empty($value)) {
            // Path
            $path = $this->getDestination() . '/' . $this->_fileValue;
            // Form session
            $formSession = new Zend_Session_Namespace('formSession');
            // Image element
            $formSession->elements[$this->getName()]['path'] = $path;
            // Is required
            if ($this->isRequired()) {
                $this->setRequired(false);
            }
            // Alone image type
            if ($this->_fileType == 'image') {
                // Obtenemos sus dimensiones
                if (file_exists($path)) {
                    $thumb = PhpThumbFactory::create($path);
                    $result = $thumb->getCurrentDimensions();
                    $this->_currentDimensions['width'] = $result['width'];
                    $this->_currentDimensions['height'] = $result['height'];
                }
            }
        }
        return $this;
    }

    public function getCurrentDimensions()
    {
        return $this->_currentDimensions;
    }

    /**
     * Processes the file, returns null or the filename only
     * For the complete path, use getFileName
     *
     * @return null|string
     */
    public function getValue()
    {
        if ($this->_value !== null) {
            return $this->_value;
        }

        $content = $this->getTransferAdapter()->getFileName($this->getName());
        if (empty($content)) {
            // Return current file
            if ($this->_fileValue !== null) {
                return $this->_fileValue;
            } else {
                return null;
            }
        }

        if (!$this->isValid(null)) {
            return null;
        }

        if (!$this->_valueDisabled && !$this->receive()) {
            return null;
        }

        // Return proccess file
        $newFile = $this->getFileName(null, false);
        if ($newFile !== null) {
            $this->_fileValue = $newFile;
            return $newFile;
        }
    }

    /**
     * @param string $entity
     * @return string
     */
    private function _getEntityPath($entity)
    {
        $output = array();
        preg_match_all("/([A-Z]|[a-z])[a-z]+/", $entity, $output);
        $output1 = $output[0];
        $path = '';
        foreach ($output1 as $result) {
            $path .= strtolower($result) . '-';
        }
        return  substr($path, 0, -1);     
    }

}