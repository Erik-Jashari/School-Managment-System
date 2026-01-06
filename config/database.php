<?php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'school_management_system');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    function getConnection(){
        try{
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $pdo;
        }catch(Exception $e){
            error_log("Database Connection Error: " . $e->getMessage());
            die('Database Connection Failed Try Again Later.');
        }
    }
?>