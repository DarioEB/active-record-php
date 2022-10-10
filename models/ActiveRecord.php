<?php

namespace Model;

class ActiveRecord {
    //Base de datos en un atributo estatico
    protected static $db;
    protected static $columnaDB = [];
    protected static $tabla = '';

    // Errores
    protected static $errores = [];

    // Definir la conexion a la base de datos
    public static function setDB($database) {
        self::$db = $database; // con self hacemos referencia a metodos estaticos
    }

    public function guardar() {
         if(!is_null($this->id)) {
            // Actualizar
            $this->actualizar();
        } else {
            // Creando un nuevo registro
            $this->crear();
        }
    }
    public function actualizar() {
        // Sanitizar los datos
        // Sanitizamos los datos a traves del metodo sanitizarDatos
        $atributos = $this->sanitizarDatos();

        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key} = '{$value}'";
        }

        $query = " UPDATE " . static::$tabla ." SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";
        
        $resultado = self::$db->query($query);
        if ($resultado){
            // Redireccionar al usuario
            // echo "Inserrado correctamente";
            header('Location: /BIENES-RAICES/admin?resultado=2');
        }
    }

    public function crear() {
        // Sanitizar los datos
        // Sanitizamos los datos a traves del metodo sanitizarDatos
        $atributos = $this->sanitizarDatos();

        // $string = join(', ', array_keys($atributos));
        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";
        
        $resultado = self::$db->query($query);
        
        if ($resultado){
            // Redireccionar al usuario
            // echo "Inserrado correctamente";
            header('Location: /BIENES-RAICES/admin?resultado=1');
        }
    }

    // Eliminar un registro
    public function eliminar() {
        // Eliminar la propiedad
        $query = "DELETE FROM " . static::$tabla ." WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado) {
            $this->borrarImagen();
            header('Location: /admin?resultado=3');
        }

    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnaDB as $col) {
            if($col === 'id') continue;
            // Vamos mapeando los valores de los atributos dentro del arreglo columnas
            $atributos[$col] = $this->$col;
        }
        return $atributos;
    }

    public function sanitizarDatos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach($atributos as $key => $value){
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }  

    // Subida de archivos
    public function setImagen($imagen) {
        
        // Elimina la imagen previa
        if(!is_null( $this->id )) {
            // Comprobar si existe el archivo
            $this->borrarImagen();
        }
        // Asignar al atributo de imagen el nombre de la imagen
        if($imagen) {
            $this->imagen = $imagen;
        }
    }

    // Eliminar el archivo
    public function borrarImagen() {
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if($existeArchivo) {
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }

    // Validacion
    public static function getErrores() {
        return static::$errores;
    }

    public function validar() {
        static::$errores = [];
        return static::$errores;
    }

    // Listar todas las tablas
    public static function all() {
        // echo "Consultado todas las tablas";
        $query = " SELECT * FROM " . static::$tabla;

        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Obtiene determinado numero de registro
    public static function get($cant) {
        $query = " SELECT * FROM " . static::$tabla . " LIMIT " . $cant ;
        
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca una propiedad por su ID
    public static function find($id) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = ${id}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado );
    }

    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        // Liberar la memoria
        $resultado->free();
        // Retornar los resultados
        return $array();
    }   

    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value) {
            if( property_exists($objeto, $key) ) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    // Sincronizar el objeto el memoria con los cambios realizados por el usuario
    public function sincronizar($args = []) {
        foreach($args as $key => $value) {
            if(property_exists( $this, $key ) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}