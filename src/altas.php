<?php 
include ('menu.php');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 1 Jul 2000 05:00:00 GMT"); // Fecha en el pasado
?>
<html lang="es">
	<head>
	    <meta charset="UTF-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	    <title>Altas</title>
        <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">
	</head>
    <body>
<?php 
include ('auth.php');

include_once ('drive/vendor/autoload.php');

function subirDocumentoDrive($documento){
    // Variables de credenciales.
    $claveJSON = '1diD_RyBCOw-p8na4eQm96u3fXC7r4CRO';
    $pathJSON = '../phpdriive-392834bd507f.json';

    //configurar variable de entorno
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.$pathJSON);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(["https://www.googleapis.com/auth/drive.file"]);
    try{		
        //instanciamos el servicio
        $service = new Google_Service_Drive($client);

        //instacia de archivo
        $file = new Google_Service_Drive_DriveFile();
        $file->setName($documento);

        //obtenemos el mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE); 
        $mime_type=finfo_file($finfo, $documento);

        //id de la carpeta donde hemos dado el permiso a la cuenta de servicio 
        $file->setParents(array($claveJSON));
        $file->setDescription("Imagen de usario");
        $file->setMimeType($mime_type);
        $result = $service->files->create(
          $file,
          array(
            'data' => file_get_contents($documento),
            'mimeType' => $mime_type,
            'uploadType' => 'media',
          )
        );
        $foto = $result->id;
        echo "2.- Fichero subido a Google Drive. ";
        return $foto;
    }catch(Google_Service_Exception $gs){
        $m=json_decode($gs->getMessage());
        echo $m->error->message;
    }catch(Exception $e){
        echo $e->getMessage();  
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $documento =$_FILES['documento']['name'];
    $ape = $_POST['apellido'];
	$nom = $_POST['nombre'];
	$ed = $_POST['edad'];
    $mail = $_POST['mail'];
   
    
    // Subimos el documento a nuestro servidor.
    if(move_uploaded_file($_FILES['documento']['tmp_name'], $documento)){
        echo "1.- Fichero subido al servidor. ";
        $foto = subirDocumentoDrive($documento);
        
        if (unlink($documento)){
            echo "3.- Fichero eliminado del servidor";
        }else{
            echo 'Error: No se ha podido eliminar el documento "'.$documento.'" en el servidor.';
        }		
    }else{
        echo "Error: Se ha producido un error, intentelo de nuevo.";
    }
    
}

// salida de informacion

$base = "gestion";
$Conexion =  mysqli_connect("localhost","root","",$base);

$cadena= "INSERT INTO persona(apellido, nombre, edad, mail,foto) VALUES ('$ape','$nom','$ed','$mail','$foto')";

$resultado = mysqli_query($Conexion,$cadena);

if($resultado){
	echo "<div style='display:flex;width: 70%;margin: 10%;'>
	<h3 style='margin-left:20px;'>".$ape."</h3>
	<h3 style='margin-left:20px;'>".$nom."</h3>
	<h3 style='margin-left:20px;'>".$ed." </h3>
    <h3 style='margin-left:20px;'>".$mail." </h3>
    <img src='https://drive.google.com/uc?export=download&id=".$foto."' style='width:30px;height:20px'>
	<h3 style='margin-left:20px;'>Se ha insertado un registro</h3></div>"."<br>";

}else{
	echo "<h3>NO se ha generado un registro</h3></div>"."<br>";
}
 ?>

    </body>
</html>
