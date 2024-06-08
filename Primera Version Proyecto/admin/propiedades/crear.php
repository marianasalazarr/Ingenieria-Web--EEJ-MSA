<?php 
// Base de datos
    require '../../includes/config/database.php';
       $db = conectarDB();

       //consultar para obtener los vendedores
       $consulta = "SELECT * FROM vendedores";
       $resultado = mysqli_query($db, $consulta);
       //arreglo con mensajes de errores
        $errores = [];

            $titulo = '';
            $precio = '';
            $descripcion = '';
            $habitaciones = '';
            $wc = '';
            $estacionamiento = '';
            $vendedorId = '';

        //ejecutar el codigo despues de que el usuario envia el codigo 
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
          //echo "<pre>";
           // var_dump($_POST);
         // echo "</pre>";

          //echo "<pre>";
          //var_dump($_FILES);
           // echo "</pre>";

            $titulo = mysqli_real_escape_string($db,  $_POST['titulo']);
            $precio = mysqli_real_escape_string( $db,$_POST['precio']);
            $descripcion = mysqli_real_escape_string( $db,$_POST['descripcion']);
            $habitaciones = mysqli_real_escape_string( $db,$_POST['habitaciones']);
            $wc = mysqli_real_escape_string( $db,$_POST['wc']);
            $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento']);
            $vendedorId = mysqli_real_escape_string($db, $_POST['vendedor']);
            $creado = date('Y/m/d');
            $imagen = $_FILES['imagen'];
           


            if(!$titulo){
                $errores[]="Debes añadir un titulo";
            }
            if(!$precio){
                $errores[]="El precio es obligatorio";
            }
            if(strlen($descripcion)<50){
                $errores[]="La descripcion es obligatoria y debe tener al menos 50 caracteres";
            }
            if(!$habitaciones){
                $errores[]="El numero de habitaciones es obligatorio";
            }
            if(!$wc){
                $errores[]="El numero de baños es obligatorio";
            }
            if(!$estacionamiento){
                $errores[]="El numero de lugares del estacionamiento es obligatorio";
            }
            if(!$vendedorId){
                $errores[]="Debes elegir un vendedor";
            }
            if(!$imagen['name']){
                $errores[]="La imagen es obligatoria";
            }

            $medida = 1000 * 1000;

            if($imagen['size'] > $medida ){
                $errores[] = "La imagen es muy pesada";
            }
            //echo "<pre>";
            //var_dump($errores);
            //echo "</pre>";

            if(empty($errores)){
                //subida de archivos

                //crear carpeta
                $carpetaImagenes = "../../imagenes/";

                if(!is_dir($carpetaImagenes)) {
                mkdir($carpetaImagenes);
                }

                $nombreImagen = md5( uniqid(rand(), true) ) . ".jpg";

                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );
                


                //insertar en la base de datos
                $query = " INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedorId) VALUES ( '$titulo',
                '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId' )";

                //echo $query;
                $resultado = mysqli_query($db, $query);

                if($resultado){
                header('Location: /admin?resultado=1');
                }
            }
        }
    
    require '../../includes/funciones.php';
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>


        <a href="/admin" class="boton boton-verde">Volver</a>

        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>

                <label form="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

                <label form="precio">Precio</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

                <label form="imagen">Imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

                <label form="description">Descripción</label>
                <textarea id="descripción" name="descripcion"><?php echo $descripcion; ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">
                
                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento; ?>">

            </fieldset>
    
            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">-- Seleccione --</option>
                    <?php while($vendedor = mysqli_fetch_assoc($resultado)): ?>
                    <option <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']; ?>">
                    <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?> </option>
                        <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>
<?php 
    incluirTemplate('footer');
?>