<?php


namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){

        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();
            if(empty($alertas)){
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);
                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
                } else {
                    // El usuario existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        // Iniciar la sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar al usuario
                        header('location: /dashboard');
                    } else {
                        Usuario::setAlerta('error', 'El password es incorrecto');
                    }

                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        if(empty($_SESSION)){
            header('location: /');
        }
    }

    public static function crear(Router $router){
        $usuario = new Usuario();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarCuentaNueva();
            
            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);
                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar un token
                    $usuario->crearToken();

                    // Crear un nuevo ususario
                    $resultado = $usuario->guardar();

                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    if($resultado){
                        header('location: /mensaje');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/crear',[
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();
            
            if(empty($alertas)){
                // Buscar el usuario
                $usuario= Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $mail = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $mail->enviarInstrucciones();

                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }

            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/olvide',[
            'titulo' => 'Olvidaste tu password',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){
        $alertas = [];
        $mostrar = true;
        $token = s($_GET['token']);
        if(!$token) header('location: /');

        // Identificar el usuario con el token
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)){
            Usuario::setAlerta('error', 'El token no es valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Añadir el nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();
            if(empty($alertas)){
                // Hashear el nuevo password
                $usuario->hashPassword();
                // Eliminar el token
                $usuario->token = '';
                // Guardar el usuario en la base de datos
                $resultado = $usuario->guardar();
                // Redireccionar
                if($resultado){
                    header('location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/reestablecer',[
            'titulo' => 'Reestablecer password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);

        
    }

    public static function mensaje(Router $router){

        // Render a la vista
        $router->render('auth/mensaje',[
            'titulo' => 'Cuenta creada'
        ]);

    }

    public static function confirmar(Router $router){

        $alertas = [];

        $token = s($_GET['token']);
        if(!$token) header('location: /');

        // Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);
        if(empty($usuario)){
            // No se encontro el usuario con el token
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2);
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');

        }

        $alertas = Usuario::getAlertas();
        // Render a la vista
        $router->render('auth/confirmar',[
            'titulo' => 'Confirmar Cuenta',
            'alertas' => $alertas
        ]);
        
    }
}