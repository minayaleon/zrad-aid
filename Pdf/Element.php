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
class ZradAid_Pdf_Element
{

    /**
     * @var ZradAid_Pdf_Page
     */
    private $_page;

    /**
     * @var float
     */
    private $_fontSize;

    /**
     * @var float
     */
    private $_margin = 0.00;

    /**
     * @var array
     */
    private $_pages = array();

    /**
     * @var bool
     */
    private $_drawHeader = true;

    /**
     * @param bool $drawHeader
     */
    public function setDrawHeader($drawHeader)
    {
        $this->_drawHeader = $drawHeader;
    }

    /**
     * @return array
     */
    public function getPages()
    {
        return $this->_pages;
    }

    /**
     * @return ZradAid_Pdf_Page
     */
    public function getCurrentPage()
    {
        return $this->_page;
    }

    /**
     * @return ZradAid_Pdf_Page
     */
    public function createPage()
    {
        $class = get_class($this->_page);
        return new $class(Zend_Pdf_Page::SIZE_A4);
    }

    /**
     * @param float $margin
     */
    public function setMargin($margin)
    {
        $this->_margin = $margin;
    }

    /**
     * @return float
     */
    public function getMargin()
    {
        return $this->_margin;
    }

    /**
     * @param ZradAid_Pdf_Page $page
     * @param float $fontSize
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function __construct($page)
    {
        $this->_page = $page;
        $this->_margin = $this->_page->getMargin();

        // Agregamos la pagina creada
        array_push($this->_pages, $this->_page);
    }

    /**
     * @param String $text
     * @param int $x
     * @param int $y
     * @return array $nextPosition posiciones siguientes en x,y
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function drawZLineText($text, $x, $y)
    {
        $color = '';
        if (is_array($text)) {
            $color = $text['color'];
            $text = $text['text'];
        }
        
        $letterSpacing = 0;
        $yi = $y;
        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;
        $this->_fontSize = $this->_page->getFontSize();

        // Validate overflow page 
        $i = 0;
        $yv = $this->_page->getHeight() - ($y + ($this->_fontSize * 1 * ($i + 1)) + $this->_margin + ($letterSpacing * $i));
        if ($yv < 0) {
            $header = $this->_page->getHeader();
            $margin = $this->_page->getMargin();
            $font = $this->_page->getFont();
            $fontSize = $this->_page->getFontSize();
            // Creamos una nueva pagina
            $this->_page = $this->createPage();
            // Agregamos la pagina creada
            array_push($this->_pages, $this->_page);
            // Inicializamos
            if (!empty($color)) {
                $this->_page->setFillColor($color);
            }
            $this->_page->setMargin($margin);
            $this->_page->setFont($font, $fontSize);
            if ($this->_drawHeader) {
                $this->_page->setHeader($header);
                $position = $this->_page->drawHeader();
                $y = $position['y2'];
            } else {
                $y = $this->_margin;
                $yi = $y;
            }
        }

        // Draw
        $x = $marginLeft + $x;
        $y = $this->_page->getHeight() - ($y + $marginTop + ($this->_fontSize * 1.1));
        $this->_page->drawText($text, $x, $y, 'UTF-8');
        $widthText = $this->calculateTextWidth($text);
        $position = array('x1' => $x, 'y1' => $yi, 'x2' => $widthText + $x, 'y2' => $this->_page->getHeight() - $y);
        return $position;
    }

    /**
     * @param array $lines
     * @param int $x
     * @param int $y
     * @param float $letterSpacing
     * @return array $nextPosition posiciones siguientes en x,y
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function drawZMultilineText($lines, $x, $y, $attributes = array())
    {
        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;
        $this->_fontSize = $this->_page->getFontSize();

        $letterSpacing = 0;
        if (isset($attributes['letterSpacing'])) {
            $letterSpacing = $attributes['letterSpacing'];
        }

        if (isset($attributes['color'])) {
            $this->_page->setFillColor($attributes['color']);
        }

        // Draw        
        $x = $marginLeft + $x;
        foreach ($lines as $i => $line) {
            // Validate overflow page            
            $yv = $this->_page->getHeight() - ($y + ($this->_fontSize * 1 * ($i + 1)) + $this->_margin + ($letterSpacing * $i));
            if ($yv < 0) {
                $header = $this->_page->getHeader();
                $margin = $this->_page->getMargin();
                $font = $this->_page->getFont();
                $fontSize = $this->_page->getFontSize();
                // Creamos una nueva pagina
                $this->_page = $this->createPage();
                // Agregamos la pagina creada
                array_push($this->_pages, $this->_page);
                // Inicializamos
                if (isset($attributes['color'])) {
                    $this->_page->setFillColor($attributes['color']);
                }
                $this->_page->setMargin($margin);
                $this->_page->setFont($font, $fontSize);
                if ($this->_drawHeader) {
                    $this->_page->setHeader($header);
                    $position = $this->_page->drawHeader();
                    $y = $position['y2'];
                } else {
                    $y = 0;
                }
            }

            // Set Margin
            $marginLeft = ($x == 0) ? $this->_margin : 0;
            $marginTop = ($y == 0) ? $this->_margin : 0;

            $yi = $this->_page->getHeight() - ($y + $marginTop + ($this->_fontSize * 1 * ($i + 1)) + ($letterSpacing * $i));
            $this->_page->drawText($line, $x, $yi, 'UTF-8');
            if ($i == 0)
                $widthText = $this->calculateTextWidth($line);
        }
        $position = array('x2' => $widthText + $x, 'y2' => $this->_page->getHeight() - $yi);
        return $position;
    }

    /**
     * @param array $items
     * @param int $x
     * @param int $y
     * @param float $letterSpacing
     * @return array $nextPosition posiciones siguientes en x,y
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function drawZHtmlMultilineText($items, $x, $y, $attributes = array())
    {
        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;
        $this->_fontSize = $this->_page->getFontSize();

        $letterSpacing = 0;
        if (isset($attributes['letterSpacing'])) {
            $letterSpacing = $attributes['letterSpacing'];
        }

        if (isset($attributes['color'])) {
            $this->_page->setFillColor($attributes['color']);
        }

        // Draw        
        $xVineta = $marginLeft + $x;
        $x = $marginLeft + $x + $items['posx'];

        $list = $items['result'];
        foreach ($list as $j => $item) {
            $lines = $item['lines'];
            foreach ($lines as $i => $line) {
                // Validate overflow page            
                $yv = $this->_page->getHeight() - ($y + ($this->_fontSize * 1 * ($i + 1)) + $this->_margin + ($letterSpacing * $i));
                if ($yv < 0) {
                    $header = $this->_page->getHeader();
                    $margin = $this->_page->getMargin();
                    $font = $this->_page->getFont();
                    $fontSize = $this->_page->getFontSize();
                    // Creamos una nueva pagina
                    $this->_page = $this->createPage();
                    // Agregamos la pagina creada
                    array_push($this->_pages, $this->_page);
                    // Inicializamos
                    if (isset($attributes['color'])) {
                        $this->_page->setFillColor($color);
                    }
                    $this->_page->setMargin($margin);
                    $this->_page->setFont($font, $fontSize);
                    if ($this->_drawHeader) {
                        $this->_page->setHeader($header);
                        $position = $this->_page->drawHeader();
                        $y = $position['y2'];
                    } else {
                        $y = 0;
                    }
                }

                // Set Margin
                $marginLeft = ($x == 0) ? $this->_margin : 0;
                $marginTop = ($y == 0) ? $this->_margin : 0;

                $yi = $this->_page->getHeight() - ($y + $marginTop + ($this->_fontSize * 1 * ($i + 1)) + ($letterSpacing * $i));
                if ($i == 0)
                    $yvineta = $yi;
                $this->_page->drawText($line, $x, $yi, 'UTF-8');
                if ($i == 0)
                    $widthText = $this->calculateTextWidth($line);
            }
            //Dibujando la vineta            
            $this->_page->drawText('-', $xVineta, $yvineta, 'UTF-8');
            $y = $this->_page->getHeight() - $yi + $letterSpacing;
        }


        $position = array('x2' => $widthText + $x, 'y2' => $this->_page->getHeight() - $yi);
        return $position;
    }

    /**
     * @param string $image baseUrl de la imagen a mostrar
     * @param int $x
     * @param int $y
     * @return array $nextPosition posiciones siguientes en x,y
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function drawZImage($image, $x, $y)
    {

        // Draw        
        $info = GetImageSize($image);
        $with = (int) $info[0] / 1.5;
        $height = (int) $info[1] / 1.5;

        // Validate overflow page
        $yv = $this->_page->getHeight() - $y - $height - $this->_margin;
        if ($yv < 0) {
            $header = $this->_page->getHeader();
            $margin = $this->_page->getMargin();
            $font = $this->_page->getFont();
            $fontSize = $this->_page->getFontSize();
            // Creamos una nueva pagina
            $this->_page = $this->createPage();
            // Agregamos la pagina creada
            array_push($this->_pages, $this->_page);
            // Inicializamos
            $this->_page->setMargin($margin);
            if ($this->_drawHeader) {
                $this->_page->setHeader($header);
                $position = $this->_page->drawHeader();
                $y = $position['y2'];
            } else {
                $y = $this->_margin;
            }
        }

        $imagePath = Zend_Pdf_Image::imageWithPath($image);

        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;
        // Bug image
        //$y += 1;
        // Positions
        $y1 = $this->_page->getHeight() - ($y + $height + $marginTop);
        $y2 = $y1 + $height;
        $x1 = $x + $marginLeft;
        $x2 = $x1 + $with;
        $this->_page->drawImage($imagePath, $x1, $y1, $x2, $y2);
        $position = array(
            'x1' => $x,
            'y1' => $y,
            'x2' => $x2,
            'y2' => $this->_page->getHeight() - $y1,
            'with' => $with,
            'height' => $height);
        return $position;
    }

    /**
     * Draw rectangle.
     *
     * radius is an integer representing radius of the four corners, or an array
     * of four integers representing the radius starting at top left, going
     * clockwise
     * 
     * Structure attributes
     * array(
     *      'padding' => 0,
     *      'radius' => 0,
     *      'text' => 'Lorem'    
     * )     
     *
     * @param float $x
     * @param float $y
     * @param float $with
     * @param float|string $height
     * @param array $attributes
     * @return array $position posiciones siguientes en x,y
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function drawZRectangle($x, $y, $with, $height = null, $attributes = array())
    {
        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;

        // Attributes
        $radius = 0;
        $padding = 0;
        $text = '';
        $letterSpacing = 0;

        // Radius
        if (isset($attributes['radius']))
            $radius = $attributes['radius'];

        // Padding
        if (isset($attributes['padding']))
            $padding = $attributes['padding'];

        if (!is_array($padding)) {
            $padding = array($padding, $padding, $padding, $padding);
        } else {
            for ($i = 0; $i < 4; $i++) {
                if (!isset($padding[$i])) {
                    $padding[$i] = 0;
                }
            }
        }

        // Text
        if (isset($attributes['text']['letterSpacing'])) {
            $letterSpacing = $attributes['text']['letterSpacing'];
        }

        if (isset($attributes['text']['content']) && is_string($attributes['text']['content']) && !empty($attributes['text']['content'])) {
            $text = $attributes['text']['content'];
            $withCalc = $with - ($padding[1] + $padding[3]);
            if (isset($attributes['text']['font']) && isset($attributes['text']['fontSize'])) {
                $this->_page->setFont($attributes['text']['font'], $attributes['text']['fontSize']);
            }
            if (isset($attributes['text']['html']) && $attributes['text']['html']) {
                $pdfHtml = new ZradAid_Pdf_Html();
                $elements = $pdfHtml->getElements($text);
                $result = $this->calculateHtmlTexHeight($elements, $withCalc, $letterSpacing);
            } else {
                $result = $this->calculateTexHeight($text, $withCalc, $letterSpacing);
            }
        }

        if ($height === null && isset($result['height'])) {
            $height = $result['height'];
        } else if (!is_numeric($height)) {
            $height = 10;
        }

        if (isset($attributes['minHeight']) && $attributes['minHeight'] > $height) {
            $height = $attributes['minHeight'];
        }

        // Draw  Rectangle
        $height = $height + ($padding[0] + $padding[2]);

        // Validate overflow page
        $yv = $this->_page->getHeight() - ($y + $height + $this->_margin);
        if ($yv < 0) {
            $header = $this->_page->getHeader();
            $margin = $this->_page->getMargin();
            $font = $this->_page->getFont();
            $fontSize = $this->_page->getFontSize();
            // Creamos una nueva pagina
            $this->_page = $this->createPage();
            // Agregamos la pagina creada
            array_push($this->_pages, $this->_page);
            // Inicializamos
            $this->_page->setMargin($margin);
            $this->_page->setFont($font, $fontSize);
            if ($this->_drawHeader) {
                $this->_page->setHeader($header);
                $position = $this->_page->drawHeader();
                $y = $position['y2'];
            } else {
                $y = 0;
            }
        }

        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;

        $x1 = $x + $marginLeft;
        $y1 = $this->_page->getHeight() - ($y + $height + $marginTop);
        $x2 = $x1 + $with;
        $y2 = $y1 + $height;

        if (isset($attributes['fillColor'])) {
            $this->_page->setFillColor($attributes['fillColor']);
        }
        if (isset($attributes['lineWidth'])) {
            $this->_page->setLineWidth($attributes['lineWidth']);
        }
        if (isset($attributes['lineColor'])) {
            $this->_page->setLineColor($attributes['lineColor']);
        }


        if ($radius != 0 || is_array($radius)) {
            $this->_page->drawRoundedRectangle($x1, $y1, $x2, $y2, $radius);
        } else {
            $this->_page->drawRectangle($x1, $y1, $x2, $y2);
        }

        // Draw Text
        if (isset($attributes['text']['content']) && is_string($attributes['text']['content']) && !empty($attributes['text']['content'])) {
            $bug = 2;
            if (isset($result['lines']) && is_array($result['lines'])) {
                $lines = $result['lines'];
                $textDraw = $this->drawZMultilineText($lines, $x + $padding[3], $y + $padding[0] + $marginTop - $bug, $attributes['text']);
            } else if (isset($attributes['text']['html']) && $attributes['text']['html']) {
                $this->drawZHtmlMultilineText($result, $x + $padding[3], $y + $padding[0] + $marginTop - $bug, $attributes['text']);
            }
        }
        $position = array('x1' => $x, 'y1' => $y + $marginTop, 'x2' => $x2, 'y2' => $this->_page->getHeight() - $y1, 'y3' => $textDraw['y2'], 'height' => $height);
        return $position;
    }

    /**
     * @param string $text palabra
     * @return float $stringWidth ancho de la palabra
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function calculateTextWidth($text)
    {
        $font = $this->_page->getFont();
        $fontSize = $this->_page->getFontSize();
        $drawingString = iconv('UTF-8', 'UTF-16BE//IGNORE', $text);
        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;
    }

    /**
     * @param string $text
     * @param float $maxWidth
     * @param float $letterSpacing
     * @return array
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function calculateTexHeight($text, $maxWidth, $letterSpacing = 0)
    {
        $fontSize = $this->_page->getFontSize();
        $lines = array();
        // Break lines        
        $nl2 = explode('<br />', nl2br($text));
        // Recorremos las lineas
        foreach ($nl2 as $index => $text) {
            // Limpiamos
            $text = trim($text);
            if (!empty($text)) {
                // Calculamos el ancho de cada palabra
                $words = explode(' ', $text);
                // Array de valores de ancho de c/palabra
                $wordsLens = array();
                // Calculamos el ancho del espacio
                $spaceSize = $this->calculateTextWidth(' ');
                foreach ($words as $word) {
                    $wordsLens[] = $this->calculateTextWidth($word);
                }
                $i = 0;
                $x = 0;
                $line = array();
                $nWords = count($words);
                while ($i < $nWords) {
                    if (($x + $wordsLens[$i]) <= $maxWidth) {
                        $x += $wordsLens[$i] + $spaceSize;
                        $line[] = $words[$i];
                        $i++;
                    } else {
                        // Opcional: $lines[] = $line;
                        $lines[] = implode(' ', $line);
                        $line = array();
                        $x = 0;
                    }
                }
                // Guardamos la ultima línea
                // Opcional: $lines[] = $line;
                $lines[] = implode(' ', $line);
            } else {
                $lines[] = '';
            }
        }
        return array('width' => $maxWidth, 'height' => ($fontSize * count($lines) * 1) + ((count($lines)) * $letterSpacing), 'lines' => $lines);
    }

    /**
     * @param array $items
     * @param float $maxWidth
     * @param float $letterSpacing
     * @return array
     * @author Juan Minaya Leon <minayaleon@gmail.com>
     */
    public function calculateHtmlTexHeight($items, $maxWidth, $letterSpacing = 0)
    {
        $result = array();
        // Tag <li>        
        $marginLi = 10;
        $maxWidthLi = $maxWidth - $marginLi;
        // Recorremos                
        $height = 0;
        foreach ($items['li'] as $text) {
            $item['posy'] = $subHeight;
            $subResult = $this->calculateTexHeight($text, $maxWidthLi, $letterSpacing);
            $item['lines'] = $subResult['lines'];
            $height += $subResult['height'];
            array_push($result, $item);
        }
        return array('posx' => $marginLi, 'width' => $maxWidth, 'height' => $height, 'result' => $result);
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $with
     * @param float $height
     * @param Zend_Pdf_Action_URI $target     
     */
    public function drawZLinkImage($x, $y, $with, $height, $target)
    {
        // Set Margin
        $marginLeft = ($x == 0) ? $this->_margin : 0;
        $marginTop = ($y == 0) ? $this->_margin : 0;

        $x1 = $x + $marginLeft;
        $y1 = $this->_page->getHeight() - ($y + $height + $marginTop);
        $x2 = $x1 + $with;
        $y2 = $y1 + $height;

        $annotation = Zend_Pdf_Annotation_Link::create($x1, $y1, $x2, $y2, $target);
        $this->_page->attachAnnotation($annotation);
    }

}
