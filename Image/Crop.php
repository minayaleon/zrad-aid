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
class ZradAid_Image_Crop
{

    /**
     * Corta una imagen de acuerdo a las coordenadas descritas
     * 
     * @param string $thumbImageName Ruta en la que guardar el fichero. Si no se establece, o su valor es NULL, 
     * se mostrará directamente en la salida el flujo de imagen.
     * @param string Ruta de la imagen
     * @param int $width
     * @param int $height
     * @param int $startWidth
     * @param int $startHeight
     * @param int $scale
     */
    public function resizeThumbnailImage($thumbImageName, $image, $width, $height, $startWidth, $startHeight, $scale)
    {
        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);

        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType) {
            case "image/gif":
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, $startWidth, $startHeight, $newImageWidth, $newImageHeight, $width, $height);
        switch ($imageType) {
            case "image/gif":
                imagegif($newImage, $thumbImageName);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $thumbImageName, 90);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $thumbImageName);
                break;
        }
        chmod($thumbImageName, 0777);
        return $thumbImageName;
    }

}
