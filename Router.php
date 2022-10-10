<?php

namespace MVC;

class Router {
    public $rutasGET = [];
    public $rutasPOST = [];


    public function get($url, $fn) {
        $this->rutasGET[$url] = $fn;
    }

    public function post($url, $fn) {
        $this->rutasPOST[$url] = $fn;
    }

    public function comprobarRutas() {
        session_start();
        $auth = $_SESSION['login'] ?? null;

        // Arreglo de rutas protegidas
        $rutasProtegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', 
        '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];


        $urlActual = $_SERVER['PATH_INFO'] ?? '/';
        $metodo = $_SERVER['REQUEST_METHOD'];

        if( $metodo === 'GET' ) {
            // echo "Si fue un GET";
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else if($metodo === 'POST') {
            $fn = $this->rutasPOST[$urlActual] ?? null;
        }

        // Proteger las rutas
        if(in_array($urlActual, $rutasProtegidas) && !$auth) {
            header('Location: /');
        }

        if($fn) {
            // La url existe y hay una función asociada
            call_user_func($fn, $this);
        } else {
            echo "Página no Encontrada";
        }
    }

    // Muestra una vista
    public function render($view, $datos = []) {
        foreach($datos as $key => $value) {
            $$key = $value; // Le decime que el datos seria la llave y tendrá ese valor            
        }

        ob_start(); // Funcion para iniciar un almacenamiento en memeoria
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpiamos la memoria
        include_once __DIR__ . "/views/layout.php";

    }
}