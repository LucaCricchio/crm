<?php
/**
 * Questa classe si occupa dell'interazione col database.
 *
 * @author francesco
 */
class Database {
    
    private $server;
    private $database;
    private $username;
    private $password;
    private $resource;
    
    private $query;
    private $result;
    private $affected;
    
    private $errors = array();
    
    public function connect() {
        $this->resource = mysqli_connect($this->server, $this->username, $this->password);
        if($this->resource) {
            return mysqli_select_db($this->resource, $this->database);
        }
        else
            return false;
    }
    
    
    public function __construct($server, $database, $username, $password) {
        $this->server = $server;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function quote($string, $escape = true) {
        if($escape)
            $string =  mysqli_real_escape_string($this->resource, $string);
        return '"' . $string . '"';
    }
    
    public function setQuery($query) {
        $this->query = $query;
    }
    
    public function execute() {
        if(!$this->resource)
            die("NOT CONNECTED");
        $this->result = mysqli_query($this->resource, $this->query);
        if($this->result) {
            $this->affected = mysqli_affected_rows($this->resource);
            return true;
        }
        else
            $this->addError (mysqli_error ($this->resource));
    }
    
    private function addError($error) {
        $this->errors [] = $error;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function loadResult() {
        $this->execute();
        $row = mysqli_fetch_row($this->result);
        return $row[0];
    }
    
    public function loadObjectList() {
        $this->execute();
        if($this->affected == 0)
                return null;
        $objects = array();
        while($row = mysqli_fetch_object($this->result))
                $objects [] = $row;
        return $objects;
    }
    
    public function loadObject() {
        $this->execute();
        if($this->affected == 0)
                return null;
        return mysqli_fetch_object($this->result);
    }
    
    public function affectedRows() {
        return $this->affected;
    }
    
    
}

