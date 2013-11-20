<?php

/**
 * Description of helper
 *
 * @author francesco
 */
class Helper {
    
    private static $db;
    
    public static $TASK_NOT_DEFINED = "NOT DEFINED";
    
    public static $LOGIN_ERROR = "[LOGIN=ERROR]";
    public static $LOGIN_OK = "[LOGIN=OK]"; 
    public static $LOGIN_EXPIRED = "[LOGIN=EXPIRED]"; 
    
    
    /**
     * 
     * @return Database Object
     */
    public static function getDb() {
        if(!isset(self::$db)) {
            self::$db =  new Database(Configuration::$db_host, 
                                        Configuration::$db_name, 
                                            Configuration::$db_user, 
                                                Configuration::$db_password);
            return self::$db;
        }
        else
            return self::$db;
    }
    
    /**
     * Ritorna l'oggetto JSON della richiesta.
     * @return null
     */
    public static function getData() {
        $data = $_REQUEST["data"];
        if(empty($data))
            return null;
        else
            return json_decode ($data);
    }
    
    
    /**
     * Restituisce vero se il cliente è loggato, falso altrimenti.
     * @return boolean
     */
    public static function isLogged() {
        if(isset($_SESSION["user_data"]))
            return true;
        else
            return false;
    }
    
    /**
     * Imposta i dati di sessione dell'utente
     * @param type $data 
     */
    public static function setUserData($data) {
        $_SESSION["user_data"] = $data;
    }
    
    
    public static function get($name, $default = null, $method = "REQUEST") {
        $value = $default;
        switch ($method) {
                            case "REQUEST":if(!empty($_REQUEST[$name]))
                                                $value = $_REQUEST[$name];
                            case "GET":if(!empty($_GET[$name]))
                                                $value = $_GET[$name];
                            case "POST":if(!empty($_POST[$name]))
                                                $value = $_POST[$name];
        }
        return $value;
    }
    
    /**
     * Ritorna i dati dell'utente.
     * @return Null se l'utente non è loggato.
     */
    public static function getUserData() {
        if(!self::isLogged())
            return null;
        return $_SESSION["user_data"];
    }
    
    public static function setUserField($field, $value) {
        if(!self::isLogged()) {
            $_SESSION["user_data"]->$field = $value;
        }
    }
    
    /**
     * Ritorna un campo utente.
     * @param type $field Campo da ritornare
     * @param type $default Valore da ritornare nel caso il valore non esista
     * @return type
     */
    public static function getUserField($field, $default = null) {
        $data = self::getUserData();
        if($data) 
            if(!empty($data))
                return $data->$field;
            else
                return $default;
        else
            return $default;
    }
}

?>
