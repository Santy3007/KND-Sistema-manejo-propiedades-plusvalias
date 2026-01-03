<?php
$data = json_decode(file_get_contents("php://input"), true);
$carpeta = "graficos/";

// Verificar si la carpeta existe, si no, crearla
if (!is_dir($carpeta)) {
    if (!mkdir($carpeta, 0777, true)) {
        die("❌ Error al crear la carpeta 'graficos/'.");
    }
}

// Verificar si se pueden escribir archivos en la carpeta
if (!is_writable($carpeta)) {
    die("❌ No se puede escribir en la carpeta 'graficos/'. Revisa los permisos.");
}

// Ruta del log
$logFile = $carpeta . "log.txt";

// Verificar si se recibieron datos
if (!$data) {
    file_put_contents($logFile, "❌ No se recibieron datos\n", FILE_APPEND);
    die("❌ No se recibieron datos.");
}

// Función para convertir PNG a JPG
function convertirPNG_a_JPG($imgBase64, $nombreArchivo) {
    global $carpeta, $logFile;

    $imgData = base64_decode(explode(",", $imgBase64)[1]);
    $img = imagecreatefromstring($imgData);
    if ($img !== false) {
        $ancho = imagesx($img);
        $alto = imagesy($img);
        $fondo = imagecreatetruecolor($ancho, $alto);
        $blanco = imagecolorallocate($fondo, 255, 255, 255);
        imagefilledrectangle($fondo, 0, 0, $ancho, $alto, $blanco);
        imagecopy($fondo, $img, 0, 0, 0, 0, $ancho, $alto);

        // Guardar la imagen en formato JPG
        $ruta = $carpeta . $nombreArchivo;
        if (imagejpeg($fondo, $ruta, 100)) {
            file_put_contents($logFile, "✅ Imagen guardada: $ruta\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "❌ Error al guardar: $ruta\n", FILE_APPEND);
        }

        imagedestroy($img);
        imagedestroy($fondo);
    } else {
        file_put_contents($logFile, "❌ Error al procesar imagen base64\n", FILE_APPEND);
    }
}

// Guardar imágenes
convertirPNG_a_JPG($data['img1'], "propiedades_tipo.jpg");
convertirPNG_a_JPG($data['img2'], "disponibilidad.jpg");
convertirPNG_a_JPG($data['img3'], "precio_provincia.jpg");

// Confirmar guardado
file_put_contents($logFile, "✅ Proceso completado correctamente\n", FILE_APPEND);
echo "✅ Imágenes guardadas correctamente.";
?>
