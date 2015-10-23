<?php

/**
 * Zend Rad
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
class ZradAid_Image_QqFileUploader
{

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
    private $_newName = null;
    private $_minWidth = null;
    private $_minHeight = null;
    
    const NOT_EXTENSION = 'notExtension';
    
    protected $_messageTemplates = array(
        self::NOT_EXTENSION => 'Archivo incorrecto. Selecciona una imagen '
    );

    public function getNewName()
    {
        return $this->_newName;
    }

    public function setNewName($newName)
    {
        $this->_newName = $newName;
    }

    public function setMinWidth($minWidth)
    {
        $this->_minWidth = $minWidth;
    }

    public function setMinHeight($minHeight)
    {
        $this->_minHeight = $minHeight;
    }

    /**
     * @param array $allowed Extensions list of valid extensions, ex. array("jpeg", "xml", "bmp")
     * @param int $sizeLimit Limit in bytes, default 10485760
     */
    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new ZradAid_Image_QqFileUploader_UploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new ZradAid_Image_QqFileUploader_UploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings()
    {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
    {
        if (!is_writable($uploadDirectory)) {
            return array('success' => false, 'error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file) {
            return array('success' => false, 'error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('success' => false, 'error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            $sizeLimit = max(1, $this->sizeLimit / 1024 / 1024) . 'MB';            
            $cSize = (string)(round(max(1, $size / 1024 / 1024) * 100) / 100) . 'MB';
            return array('success' => false, 'error' => 'El peso máximo es ' . $sizeLimit . ' su imagen pesa ' . $cSize);
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];

        // Nuevo nombre
        if ($this->_newName !== null) {
            $filename = $this->_newName;
        }
        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);            
            return array('success' => false, 'error' => $this->_error(self::NOT_EXTENSION) . $these . '.');
        }

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
            return array('success' => true, 'ext' => $ext);
        } else {
            return array('success' => false, 'error' => 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }
    
    /**
     * @param  string $messageKey
     * @param  string $value OPTIONAL
     * @return void
     */
    protected function _error($messageKey)
    {
        return $this->_messageTemplates[$messageKey];
    }

}
