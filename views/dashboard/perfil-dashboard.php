<?php include_once __DIR__ .'/header-dashboard.php';?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php';?>

    <a href="/cambiar-password" class="enlace">Cambiar password</a>

    <form action="/perfil" method="POST" class="formulario">
        <div class="campo">
            <label for="nombre">Nombre:</label>
            <input 
                type="text"
                id="nombre"
                name="nombre"
                placeholder="Tu nuevo nombre"
                value="<?php echo $usuario->nombre?>"
            >
        </div>
        <div class="campo">
            <label for="email">Email:</label>
            <input 
                type="email"
                name="email"
                id="nombre"
                placeholder="Tu nuevo email"
                value="<?php echo $usuario->email?>"
            >
        </div>
        <input type="submit" class="boton" value="Guardar Cambios"> 
 
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php';?>
