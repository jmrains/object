<?php

/*
 * Service passes PDO (the connection) to Entity
 */

class Service {

    public $DBH;

    public function __construct($servername, $dbname, $username, $password) {
        try {
            $this->DBH = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $this->DBH->setAttribute(PDO::ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
    }

}

?>