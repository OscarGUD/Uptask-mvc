<?php

namespace Model;

class Usuario extends ActiveRecord{

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre' , 'email', 'password', 'token', 'confirmado'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password-actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // Validar Login
    public function validarLogin() : array{
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El email no es valido';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }

    // Validacion para cuentas nuevas
    public function validarCuentaNueva() : array{
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener minimo 6 caracteres';
        }
        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Los password no son iguales';
        }

        return self::$alertas;
        
    }

    // Valida un email
    public function validarEmail() : array{
        if(!$this->email){
            self::$alertas['error'][] = 'El email el obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El email no es valido';
        }

        return self::$alertas;
    }

    // Validar Password
    public function validarPassword() : array{
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe contener minimo 6 caracteres';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'El email no es valido';
        }
        return self::$alertas;
    }

    // Validar el perfil
    public function validar_perfil() : array{
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        return self::$alertas;
    }

    public function nuevoPassword() : array{
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El password actual es obligatorio';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El password_nuevo es obligatorio';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El password debe contener minimo 6 caracteres';
        }

        return self::$alertas;
    }

    // Comprobar el password
    public function comprobarPassword() : bool{
         return password_verify($this->password_actual, $this->password);
    }

    // Hashea el password
    public function hashPassword() : void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar un token
    public function crearToken() : void{
        $this->token = md5(uniqid());
    }

}