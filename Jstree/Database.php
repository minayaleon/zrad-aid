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
class ZradAid_Jstree_Database
{

    private $link = false;
    private $result = false;
    private $row = false;
    public $settings = array(
        "servername" => "localhost",
        "serverport" => "3306",
        "username" => false,
        "password" => false,
        "database" => false,
        "persist" => false,
        "dieonerror" => false,
        "showerror" => false,
        "error_file" => false
    );

    function __construct()
    {
        $configs = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        
        $params = $configs->resources->db->params;
        $settings['database'] = $params->dbname;
        $settings['servername'] = $params->host;
        $settings['username'] = $params->username;
        $settings['password'] = $params->password;        
        $this->settings = array_merge($this->settings, $settings);
        if ($this->settings["error_file"] === true)
            $this->settings["error_file"] = dirname(__FILE__) . "/__mysql_errors.log";
    }

    function connect()
    {
        if (!$this->link) {
            $this->link = ($this->settings["persist"]) ?
                mysql_pconnect(
                    $this->settings["servername"] . ":" . $this->settings["serverport"], $this->settings["username"], $this->settings["password"]
                ) :
                mysql_connect(
                    $this->settings["servername"] . ":" . $this->settings["serverport"], $this->settings["username"], $this->settings["password"]
                ) or $this->error();
        }
        if (!mysql_select_db($this->settings["database"], $this->link))
            $this->error();
        if ($this->link)
            mysql_query("SET NAMES 'utf8'");
        return ($this->link) ? true : false;
    }

    function query($sql)
    {
        if (!$this->link && !$this->connect())
            $this->error();
        if (!($this->result = mysql_query($sql, $this->link)))
            $this->error($sql);
        return ($this->result) ? true : false;
    }

    function nextr()
    {
        if (!$this->result) {
            $this->error("No query pending");
            return false;
        }
        unset($this->row);
        $this->row = mysql_fetch_array($this->result, MYSQL_BOTH);
        return ($this->row) ? true : false;
    }

    function get_row($mode = "both")
    {
        if (!$this->row)
            return false;

        $return = array();
        switch ($mode) {
            case "assoc":
                foreach ($this->row as $k => $v) {
                    if (!is_int($k))
                        $return[$k] = $v;
                }
                break;
            case "num":
                foreach ($this->row as $k => $v) {
                    if (is_int($k))
                        $return[$k] = $v;
                }
                break;
            default:
                $return = $this->row;
                break;
        }
        return array_map("stripslashes", $return);
    }

    function get_all($mode = "both", $key = false)
    {
        if (!$this->result) {
            $this->error("No query pending");
            return false;
        }
        $return = array();
        while ($this->nextr()) {
            if ($key !== false)
                $return[$this->f($key)] = $this->get_row($mode);
            else
                $return[] = $this->get_row($mode);
        }
        return $return;
    }

    function f($index)
    {
        return stripslashes($this->row[$index]);
    }

    function go_to($row)
    {
        if (!$this->result) {
            $this->error("No query pending");
            return false;
        }
        if (!mysql_data_seek($this->result, $row))
            $this->error();
    }

    function nf()
    {
        $numb = mysql_num_rows($this->result);
        if ($numb === false)
            $this->error();
        return mysql_num_rows($this->result);
    }

    function af()
    {
        return mysql_affected_rows();
    }

    function error($string = "")
    {
        $error = mysql_error();
        if ($this->settings["show_error"])
            echo $error;
        if ($this->settings["error_file"] !== false) {
            $handle = @fopen($this->settings["error_file"], "a+");
            if ($handle) {
                @fwrite($handle, "[" . date("Y-m-d H:i:s") . "] " . $string . " <" . $error . ">\n");
                @fclose($handle);
            }
        }
        if ($this->settings["dieonerror"]) {
            if (isset($this->result))
                mysql_free_result($this->result);
            mysql_close($this->link);
            die();
        }
    }

    function insert_id()
    {
        if (!$this->link)
            return false;
        return mysql_insert_id();
    }

    function escape($string)
    {
        if (!$this->link)
            return addslashes($string);
        return mysql_real_escape_string($string);
    }

    function destroy()
    {
        if (isset($this->result))
            mysql_free_result($this->result);
        if (isset($this->link))
            mysql_close($this->link);
    }

}