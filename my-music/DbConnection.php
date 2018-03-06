<?php

Class DBConnection {

    protected static $connection;

    function getDBConnection() {
        return self::db_connect();
    }

    private function db_connect() {
        if (!isset(self::$connection)) {
            $ini_array = parse_ini_file("properties.ini");
            $host = $ini_array['host'];
            $username = $ini_array['username'];
            $password = $ini_array['password'];
            $db_name = $ini_array['db_name'];

            self::$connection = new PDO("mysql:host=$host;dbname=$db_name", "$username", "$password");

            if (self::$connection == false) {
                return mysqli_connect_error();
            }
        }

        return self::$connection;
    }

}
