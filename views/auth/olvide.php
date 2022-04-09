<div class="contenedor olvide">

    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Olvidaste tu password de Uptask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'?>

        <form action="/olvide" method="POST" class="formulario">

            <div class="campo">
                <label for="email">Email:</label>
                <input 
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Tu Email"
                >
            </div>


            <input type="submit" class="boton" value="Olvide"> 
        </form>

        <div class="acciones">
            <a href="/">Inicia Sesion Aqui</a>
            <a href="/crear">Crea tu cuenta Aqui</a>
        </div>
    </div>
</div>