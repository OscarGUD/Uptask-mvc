<div class="contenedor reestablecer">
    
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'?>


    <div class="contenedor-sm">
        <p class="descripcion-pagina">Reestablece tu password de Uptask</p> 

        <?php include_once __DIR__ . '/../templates/alertas.php'?>
        <?php if($mostrar){?>

        <form method="POST" class="formulario">

            <div class="campo">
                <label for="password">Password:</label>
                <input 
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Tu Password"
                >
            </div>

            <input type="submit" class="boton" value="Reestablecer"> 
        </form>

        <?php }?>

        <div class="acciones">
            <a href="/crear">Crea tu Cuenta Aqui</a>
            <a href="/olvide">Recupera tu password Aqui</a>
        </div>
    </div>
</div>