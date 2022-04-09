<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController{
    public static function index(Router $router){

        session_start();
        isAuth();
        
        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index',[
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }
    public static function crear(Router $router){
        $alertas = [];
        session_start();
        isAuth();

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);
            
            // Validacion
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                // Generar una URL unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar el proyecto
                $proyecto->guardar();

                // Redireccionar
                header('location: /proyecto?url=' . $proyecto->url);
            }
        }


        $router->render('dashboard/crear-proyecto',[
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }
    public static function proyecto(Router $router){

        session_start();
        isAuth();

        $url = $_GET['url'];
        if(!$url) header('location: /dashboard');
        // Revisar que la persona que rivisa el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $url);

        if($proyecto->propietarioId !== $_SESSION['id']){
            header('location: /dashboard');
        } 

        $router->render('dashboard/proyecto',[
            'titulo' => $proyecto->proyecto
        ]);
    }
    public static function perfil(Router $router){

        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();

            if(empty($alertas)){
                // Verificar que el email no exita en la bd
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    Usuario::setAlerta('error', 'El email ya esta registrado');
                } else {
                    // Guardar el usuario;
                    $usuario->guardar();

                    // Avisar que se guardo correctamente por una alerta
                    Usuario::setAlerta('exito', 'Guardado correctamente');

                    // Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }

                
            }
        }
        
        $alertas = Usuario::getAlertas();
        $router->render('dashboard/perfil-dashboard',[
            'titulo' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }
    public static function cambiar(Router $router){
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            // Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevoPassword();

            if(empty($alertas)){
                $resultado = $usuario->comprobarPassword();

                if($resultado){
                    // Asiganar el nuevo password
                    $usuario->password = $usuario->password_nuevo;

                    
                    // Eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);
                    
                    // Hashear el nuevo password
                    $usuario->hashPassword();

                    // actualizar en la base de datos
                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', 'Password actualizado correctamente');
                    }
                } else {
                    Usuario::setAlerta('error', 'Password incorrecto');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('dashboard/cambiar-password',[
            'titulo' => 'Cambiar Password',
            'alertas' => $alertas,
        ]);
    }

}