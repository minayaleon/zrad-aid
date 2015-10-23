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
class ZradAid_Jstree_Tree extends ZradAid_Jstree_Struct
{

    function __construct($table = "tree", $fields = array(), $add_fields = array("title" => "title", "type" => "type"))
    {
        parent::__construct($table, $fields);
        $this->fields = array_merge($this->fields, $add_fields);
        $this->add_fields = $add_fields;
    }

    function create_node($data)
    {
        $id = parent::_create((int) $data[$this->fields["id"]], (int) $data[$this->fields["position"]]);
        if ($id) {
            $data["id"] = $id;
            $this->set_data($data);
            return "{ \"status\" : 1, \"id\" : " . (int) $id . " }";
        }
        return "{ \"status\" : 0 }";
    }

    function set_data($data)
    {
        if (count($this->add_fields) == 0) {
            return "{ \"status\" : 1 }";
        }
        $s = "UPDATE `" . $this->table . "` SET `" . $this->fields["id"] . "` = `" . $this->fields["id"] . "` ";
        foreach ($this->add_fields as $k => $v) {
            if (isset($data[$k]))
                $s .= ", `" . $this->fields[$v] . "` = \"" . $this->db->escape($data[$k]) . "\" ";
            else
                $s .= ", `" . $this->fields[$v] . "` = `" . $this->fields[$v] . "` ";
        }
        $s .= "WHERE `" . $this->fields["id"] . "` = " . (int) $data["id"];
        $this->db->query($s);
        return "{ \"status\" : 1 }";
    }

    function rename_node($data)
    {
        return $this->set_data($data);
    }

    function move_node($data)
    {
        $id = parent::_move((int) $data["id"], (int) $data["ref"], (int) $data["position"], (int) $data["copy"]);
        if (!$id)
            return "{ \"status\" : 0 }";
        if ((int) $data["copy"] && count($this->add_fields)) {
            $ids = array_keys($this->_get_children($id, true));
            $data = $this->_get_children((int) $data["id"], true);

            $i = 0;
            foreach ($data as $dk => $dv) {
                $s = "UPDATE `" . $this->table . "` SET `" . $this->fields["id"] . "` = `" . $this->fields["id"] . "` ";
                foreach ($this->add_fields as $k => $v) {
                    if (isset($dv[$k]))
                        $s .= ", `" . $this->fields[$v] . "` = \"" . $this->db->escape($dv[$k]) . "\" ";
                    else
                        $s .= ", `" . $this->fields[$v] . "` = `" . $this->fields[$v] . "` ";
                }
                $s .= "WHERE `" . $this->fields["id"] . "` = " . $ids[$i];
                $this->db->query($s);
                $i++;
            }
        }
        return "{ \"status\" : 1, \"id\" : " . $id . " }";
    }

    function remove_node($data)
    {
        $id = parent::_remove((int) $data["id"]);
        return "{ \"status\" : 1 }";
    }

    function get_children($data)
    {
        $tmp = $this->_get_children((int) $data["id"]);
        if ((int) $data["id"] === 1 && count($tmp) === 0) {
            //$this->_create_default();
            //$tmp = $this->_get_children((int) $data["id"]);
        }
        $result = array();
        if ((int) $data["id"] === 0)
            return json_encode($result);
        foreach ($tmp as $k => $v) {
            $result[] = array(
                "attr" => array("id" => "node_" . $k, "rel" => $v[$this->fields["type"]]),
                "data" => $v[$this->fields["title"]],
                "state" => ((int) $v[$this->fields["right"]] - (int) $v[$this->fields["left"]] > 1) ? "closed" : ""
            );
        }
        return json_encode($result);
    }

    function search($data)
    {
        $this->db->query("SELECT `" . $this->fields["left"] . "`, `" . $this->fields["right"] . "` FROM `" . $this->table . "` WHERE `" . $this->fields["title"] . "` LIKE '%" . $this->db->escape($data["search_str"]) . "%'");
        if ($this->db->nf() === 0)
            return "[]";
        $q = "SELECT DISTINCT `" . $this->fields["id"] . "` FROM `" . $this->table . "` WHERE 0 ";
        while ($this->db->nextr()) {
            $q .= " OR (`" . $this->fields["left"] . "` < " . (int) $this->db->f(0) . " AND `" . $this->fields["right"] . "` > " . (int) $this->db->f(1) . ") ";
        }
        $result = array();
        $this->db->query($q);
        while ($this->db->nextr()) {
            $result[] = "#node_" . $this->db->f(0);
        }
        return json_encode($result);
    }

    function _create_default()
    {
        $this->_drop();
        $this->create_node(array(
            "id" => 1,
            "position" => 0,
            "title" => "C:",
            "type" => "drive"
        ));
        $this->create_node(array(
            "id" => 1,
            "position" => 1,
            "title" => "D:",
            "type" => "drive"
        ));
        $this->create_node(array(
            "id" => 2,
            "position" => 0,
            "title" => "_demo",
            "type" => "folder"
        ));
        $this->create_node(array(
            "id" => 2,
            "position" => 1,
            "title" => "_docs",
            "type" => "folder"
        ));
        $this->create_node(array(
            "id" => 4,
            "position" => 0,
            "title" => "index.html",
            "type" => "default"
        ));
        $this->create_node(array(
            "id" => 5,
            "position" => 1,
            "title" => "doc.html",
            "type" => "default"
        ));
    }

}

