<?php include_once __DIR__ .'/header-dashboard.php';?>

<div class="contenedor-sm">
    <?php include_once __DIR__ . '/../templates/alertas.php';?>

    <a href="/perfil" class="enlace">Vover a Perfil</a>


    <form action="/cambiar-password" method="POST" class="formulario">
        <div class="campo">
            <label for="password-actual">Password Actual:</label>
            <input 
                type="password"
                id="password-actual"
                name="password_actual"
                placeholder="Tu password Actual"
            >
        </div>
        <div class="campo">
            <label for="password-nuevo">Nuevo Password:</label>
            <input 
                type="password"
                name="password_nuevo"
                id="password-nuevo"
                placeholder="Tu Nuevo Password"
            >
        </div>
        <input type="submit" class="boton" value="Guardar Cambios"> 
 
    </form>
</div>

<?php include_once __DIR__ . '/footer-dashboard.php';?>
