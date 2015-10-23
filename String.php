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
define("UTF_8", 1);
define("ASCII", 2);
define("ISO_8859_1", 3);

class ZradAid_String
{
    protected $_string = '';

    public function __construct($string = '')
    {
        $this->_string = $string;
    }

    /**
     *
     * @return ZradAid_String
     */
    public static function parse($string)
    {
        return new ZradAid_String($string);
    }

    public function strCmp($string)
    {
        if ($this->_string == $string) {
            return true;
        }
        return false;
    }

    public function equals($string)
    {
        if ($this->_string == $string) {
            return true;
        }
        return false;
    }

    public function isVacio()
    {
        if ($this->_string == '') {
            return true;
        }
        return false;
    }

    public function encode()
    {
        $c = 0;
        $ascii = true;

        $i = 0;
        $numberCharacters = strlen($this->_string);
        if ($numberCharacters > 0) {
            do {
                $byte = ord($this->_string[$i]);
                if ($c > 0) {
                    if (($byte >> 6) != 0x2) {
                        return ISO_8859_1;
                    } else {
                        $c--;
                    }
                } elseif ($byte & 0x80) {
                    $ascii = false;
                    if (($byte >> 5) == 0x6) {
                        $c = 1;
                    } elseif (($byte >> 4) == 0xE) {
                        $c = 2;
                    } elseif (($byte >> 3) == 0x14) {
                        $c = 3;
                    } else {
                        return ISO_8859_1;
                    }
                }
                ++$i;
            } while ($i < $numberCharacters);
        }
        return ($ascii) ? ASCII : UTF_8;
    }

    /**
     *
     * @return ZradAid_String
     */
    public function toISO()
    {
        $string = ($this->encode() == ISO_8859_1) ? $this->_string : iconv("UTF-8", "ISO-8859-1", $this->_string);
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function toUTF8()
    {
        $string = ($this->encode() == ISO_8859_1) ? iconv("ISO-8859-1", "UTF-8", $this->_string) : $this->_string;
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function toLower()
    {
        $string = utf8_encode(strtolower(utf8_decode($this->_string)));
        return ZradAid_String::parse($string)->replace("Ñ", "ñ");
    }

    /**
     * Convierte a nombre propio cualquier palabra p.e ICA a Ica
     * Tambien se usa para nombres compuestos p.e MADRE DE DIOS a Madre de Dios
     */
    public function toLowerPr()
    {
        $newString = '';
        $parts = explode(' ', $this->_string);
        foreach ($parts as $part) {
            $newString .= ucfirst(mb_strtolower($part, 'UTF-8')) . ' ';
        }
        return trim($newString);
    }

    /**
     * @return ZradAid_String
     */
    public function toUpper()
    {
        $string = utf8_encode(strtoupper(utf8_decode($this->_string)));
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function toUcWords()
    {
        $string = utf8_encode(ucwords(utf8_decode($this->_string)));
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function toUcFirst()
    {
        $string = utf8_encode(ucfirst(utf8_decode($this->_string)));
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function replace($strBusqueda, $strReplace)
    {
        $string = str_replace($strBusqueda, $strReplace, $this->_string);
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function trim()
    {
        $string = trim($this->_string);
        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function subStr($inicio, $tamanio = null)
    {
        if ($tamanio === null) {
            $string = substr($this->_string, $inicio);
        } else {
            $string = substr($this->_string, $inicio, $tamanio);
        }

        return new ZradAid_String($string);
    }

    /**
     * @return ZradAid_String
     */
    public function forDB()
    {
        $strBusqueda = array("\'", '\"', '"');
        $strReplace = array("''", "''", "''");

        return $this->replace($strBusqueda, $strReplace)->toUTF8();
    }

    /**
     * @return ZradAid_String
     */
    public function toStringSearch()
    {
        $dirty = array("á", "é", "í", "ó", "ú", "'", '"', 'ü');
        $clean = array("a", "e", "i", "o", "u", "", "", 'u');

        $string = ZradAid_String::parse($this->_string)
            ->toLower()
            ->replace($dirty, $clean)
            ->forDB();

        return $string;
    }

    /**
     * @return ZradAid_String
     */
    public function toLimpiar()
    {
        $dirty = array("Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú", "'", '"', 'ü',"Ñ","ñ");
        $clean = array("A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "", "", 'u',"N",'n');

        return $this->trim()->replace($dirty, $clean);
    }

    public function toAmigable()
    {

        $dirty = array("Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú", "ñ", "'", '"', 'ü', ' ', '.', '?', '¿', ',', '(', ')');
        $clean = array("A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "n", "", "", 'u', '-', '', '', '', '', '', '');

        return $this->trim()->replace($dirty, $clean)->toLower();
    }

    /**
     * Procesa una cadena y lo convierte en URL 
     * Vestidos de Moda 2013 -> vestidos-de-moda-2013
     * 
     * @return string URL Amigable     
     */
    public function toUrlFriendly()
    {
        //Buscamos caracteres no deseados (/,-) y los borramos
        $partes = explode(' ', $this->_string);
        $r = array();
        foreach ($partes as $parte) {
            $palabra = trim($parte);
            if (!empty($palabra) && !in_array($palabra, array('-', '/', '+', '(', ')'))) {
                array_push($r, $palabra);
            }
        }

        $this->_string = implode(' ', $r);
        $dirty = array("Á", "É", "Í", "Ó", "Ú", "Ñ","á", "é", "í", "ó", "ú", "ñ", "'", '"', 'ü', ' ', '.', '?', '¿', ',', '(', ')', '/');
        $clean = array("A", "E", "I", "O", "U", "N", "a", "e", "i", "o", "u", "n", "", "", 'u', '-', '', '', '', '', '', '', '-');

        return $this->trim()->replace($dirty, $clean)->toLower();
    }

    /**
     * @return ZradAid_String
     */
    public function toPlural()
    {
        $stringClean = $this->toStringSearch();
        $indexLastLetter = $stringClean->len() - 1;

        $lastLetter = $stringClean->subStr($indexLastLetter, 1);

        if (!$lastLetter->searchOut("a e i o u")->isVacio()) {
            return new ZradAid_String($this->_string . 's');
        } else {
            return new ZradAid_String($this->_string . 'es');
        }
    }

    /**
     * @param string $extension extension a generar
     * @return $string
     */
    public static function generateName($extension = '')
    {
        return str_replace(' ', '', str_replace('.', '', microtime())) . '.' . $extension;
    }

    public function __toString()
    {
        return $this->_string;
    }

    public function len()
    {
        return strlen($this->_string);
    }

    /**
     * @return ZradAid_String
     */
    public function searchOut($string)
    {
        return new ZradAid_String(strstr($string, $this->_string));
    }

    /**
     * @return ZradAid_String
     */
    public function searchIn($string)
    {
        return new ZradAid_String(strstr($this->_string, $string));
    }

    /**
     * @return ZradAid_String
     */
    public function set($string)
    {
        $this->_string = $string;
        return $this;
    }
}