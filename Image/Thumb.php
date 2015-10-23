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

class ZradAid_Image_Thumb
{

    /**
     * @var string
     */
    private $_indentation = '    ';
    /**
     * @var string
     */
    private $_entity = null;
    /**
     * 
     */
    private $_name = '';
    /**
     * @var string
     */
    private $_baseUrl = null;
    /**
     * @var int
     */
    private $_currentImage = '';
    /**
     * @var int
     */
    private $_height;
    /**
     * @var int
     */
    private $_width;
    /**
     * @var int
     */
    private $_weight;
    /**
     * @var int
     */
    private $_currentWeight;
    /**
     * @var string
     */
    private $_message;
    /**
     * @var PhpThumbFactory
     */
    private $_thumb;

    /**
     * Use $_FILES['imagen']['tmp_name']
     *
     * @param Image $image
     */
    public function __construct($entity = null)
    {
        try {
            $configs = new Zend_Config_Ini(
                    APPLICATION_PATH . '/configs/application.ini',
                    APPLICATION_ENV);
            if($entity === null){
                $this->_width = $configs->thumb->width;
                $this->_height = $configs->thumb->height;
                $this->_weight = $configs->thumb->weight;
            }else{
                $this->_width = $configs->thumb->$entity->width;
                $this->_height = $configs->thumb->$entity->height;
                $this->_weight = $configs->thumb->$entity->weight;
                $this->setEntity($entity);
            }
        } catch (Exception $e) {
            throw new Jminaya_Image_Exception($e->getMessage());
        }
    }

    public function create($image)
    {
        try {
            $this->_thumb = PhpThumbFactory::create($image);
            $this->_currentWeight = filesize($image);
        } catch (Exception $e) {
            throw new Jminaya_Image_Exception($e->getMessage());
        }
    }

    /**
     * @return boolean
     */
    public function isValid($minSize = true)
    {
        $options = $this->_thumb->getCurrentDimensions();
        if ($options['width'] < $this->_width || $options['height'] < $this->_height && $minSize) {
            $this->_message = 'La dimensi&oacute;n m&iacute;nima es <strong>' . $this->_width . 'x' . $this->_height . 'px</strong>, su im&aacute;gen tiene <strong>' . $options['width'] . 'x' . $options['height'] . 'px</strong>';
            return false;
        }
        if ($this->_currentWeight > $this->_weight) {
            //current
            $currentNumber = ($this->_currentWeight >= 1048576) ? $this->_currentWeight / 1048576 : $this->_currentWeight / 1024;
            $currentUnidad = ($this->_currentWeight >= 1048576) ? 'MB' : 'KB';
            $currentWeight = number_format($currentNumber, 1, '.', ',') . ' ' . $currentUnidad;
            //sete
            $number = ($this->_weight >= 1048576) ? $this->_weight / 1048576 : $this->_weight / 1024;
            $unidad = ($this->_weight >= 1048576) ? 'MB' : 'KB';
            $weight = number_format($number, 1, '.', ',') . ' ' . $unidad;
            //message
            $this->_message = 'El peso m&aacute;ximo de la im&aacute;gen es <strong>' . $weight . '</strong>, su im&aacute;gen tiene <strong>' . $currentWeight . '</strong>';
            return false;
        }
        return true;
    }

    /**
     * @return $string 
     */
    private function _generateName()
    {
        $name = str_replace(" ", "", str_replace('.', "", microtime()));
        return $name;
    }

    /**
     * @param string $file
     */
    public function save($file = '')
    {
        if (!empty($file))
            $file = '/' . $file;

        $this->_thumb->adaptiveResize($this->_width, $this->_height);
        $imagen = $this->_generateName() . '.jpg';
        $this->_thumb->save(UPLOAD_PATH . $file . '/' . $imagen);
        return $imagen;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
    }
    
    /**
     * @param string $name
     */
    public function setName($name)     
    {
        $this->_name = $name;
    }

    
    /**
     * @param string $currentImage
     */
    public function setCurrentImage($currentImage)
    {
        $this->_currentImage = $currentImage;
    }

    public function renderView($method = 'new')
    {
        if(empty($this->_currentImage)){
            $this->_currentImage = $this->_baseUrl . '/images/admin/imagen.gif';
        }

        $phtml = '<div id="imageDlg' . $this->_name . '" style="display:none">' . "\n";
        $phtml .= $this->_indentation . '<div style="position: relative; width: ' . $this->_width . 'px; height: ' . $this->_height . 'px; overflow: hidden;">' . "\n";
        $phtml .= $this->_indentation . $this->_indentation . '<img src="' . $this->_currentImage . '" alt="imagen" />' . "\n";

        if($method == 'new'){
            $phtml .= $this->_indentation . $this->_indentation . '<div id="ui-overlay-state">' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . '<div class="ui-overlay">' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '<div class="ui-widget-overlay"></div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '<div class="ui-widget-shadow ui-corner-all" style="width: 122px;height: 72px;margin-top:-43px;margin-left:-68px; position: absolute; left: 50%; top: 50%;"></div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . '</div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . '<div style="position: absolute; width: 100px; height: 50px;margin-top:-37px;margin-left:-61px; left: 50%; top: 50%; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all">' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '<div class="ui-dialog-content ui-widget-content" style="background: none; border: 0; padding-bottom: 8px;">Loading</div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '<div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '<div class="ui-progressbar-value ui-widget-header ui-corner-left ui-corner-right" style="width: 100%; height: 2em;"></div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '</div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . $this->_indentation . '</div>' . "\n";
            $phtml .= $this->_indentation . $this->_indentation . '</div>' . "\n";
        }

        $phtml .= $this->_indentation . '</div>' . "\n";
        $phtml .= '</div>' . "\n";
        echo $phtml;
    }

    public function renderJscript($method = 'new')
    {
        $width = $this->_width + 20;
        $jscript = "\n";
        $jscript .= $this->_indentation . 'function open' . $this->_name . '(){' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . '$(\'#imageDlg' . $this->_name . '\').dialog({' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'title: \'IMAGEN\',' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'modal: true,' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'autoOpen: false,' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'resizable:false,' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'width:' . $width . ',' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'minHeight:' . $this->_height . ',' . "\n";

        if ($method == 'new') {
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'open: function(){' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$(\'#ui-overlay-state\').show();' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$(\'img#imagen\').hide();' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$.post(baseUrl + \'/admin/' . $this->_entity . '/imagen\', {id: id},function(data){' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$(\'img#imagen\').attr(\'src\', data).load(function(){' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$(\'img#imagen\').show();' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$(\'#ui-overlay-state\').hide();' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '});' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '});' . "\n";
            $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . '},' . "\n";
        }

        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . 'buttons: {' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . 'Cerrar: function() {' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '$(this).dialog(\'close\');' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . $this->_indentation . '}' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . $this->_indentation . '}' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . '});' . "\n";
        $jscript .= $this->_indentation . $this->_indentation . '$(\'#imageDlg' . $this->_name . '\').dialog(\'open\');' . "\n";
        $jscript .= $this->_indentation . '}' . "\n" . "\n";

        $jscript2 = $this->_indentation . '$(\'#image_btn\').click(function(){' . "\n";

        if ($method == 'new') {
            $jscript2 .= $this->_indentation . $this->_indentation . 'id = $(\'#list\').jqGrid(\'getGridParam\',\'selrow\');' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . 'if(id == null){' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . $this->_indentation . 'var title = \'VER IMAGEN\';' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . $this->_indentation . 'var message = \'Seleccione un registro de la lista\';' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . $this->_indentation . 'dialog(title,message);' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . '}else{' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . $this->_indentation . 'openImage();' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . '}' . "\n";
        }else{
            $jscript2 .= $this->_indentation . $this->_indentation . 'dialogImage();' . "\n";
            $jscript2 .= $this->_indentation . $this->_indentation . 'return;' . "\n";
        }

        $jscript2 .= $this->_indentation . '});' . "\n" . "\n";

        echo $jscript;
    }

}
