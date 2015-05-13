<?php

class PDOConnection {

    public function __construct($servername, $dbname, $username, $password) {
        try {
            $DBH = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
            return $DBH;
        } catch (Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
    }

}

?>