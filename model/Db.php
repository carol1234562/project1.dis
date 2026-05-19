<?php

class Db {
    
    // Guardamos la conexión
    private static $conexion = null;

    // Datos de conexión a la base de datos
    private function __construct() {
        $host     = 'localhost';
        $baseDatos = 'nightfest';
        $usuario  = 'root';
        $password = '';

        // Creamos la conexión PDO
        self::$conexion = new PDO(
            "mysql:host=$host;dbname=$baseDatos;charset=utf8mb4",
            $usuario,
            $password
        );

        // Si hay un error lo mostraria
        self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    //devuelve siempre la misma conexion
    public static function getConexion() {
        if (self::$conexion === null) {
            new Db();
        }
        return self::$conexion;
    }
}