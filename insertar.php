<?php
/* 
    Crea nuevos eventos con validación de campos.
    Variables clave:
    - $_POST: Todos los campos del formulario
    - $promotoresXML: Cadena XML generada para promotores
    - $nuevoEvento: Estructura XML completa del evento
*/

require_once 'BaseXClient/Query.php';
require_once 'BaseXClient/Session.php';
require_once 'BaseXClient/BaseXException.php';

use BaseXClient\Session;
use BaseXClient\BaseXException;
use BaseXClient\Query;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = [
        'id',
        'nombre',
        'fecha',
        'ciudad',
        'pais',
        'patrocinador',
        'entrada_vip_precio',
        'entrada_vip_disponibles',
        'entrada_normal_precio',
        'entrada_normal_disponibles'
    ];

    foreach ($required as $field) {
        if (empty($_POST[$field])) die("Error: Campo $field es requerido");
    }

    // Construir sección de promotores
    $promotoresXML = '';
    if (isset($_POST['promotor_id'])) {
        foreach ($_POST['promotor_id'] as $index => $id) {
            $nombre = $_POST['promotor_nombre'][$index];
            $contacto = $_POST['promotor_contacto'][$index];
            $promotoresXML .= "
                <Promotor id=\"$id\">
                    <Nombre>$nombre</Nombre>
                    <Contacto>$contacto</Contacto>
                </Promotor>";
        }
    }

    $nuevoEvento = sprintf(
        '
    <Evento id="%s">
        <Nombre>%s</Nombre>
        <Fecha>%s</Fecha>
        <Ubicacion>
            <Ciudad>%s</Ciudad>
            <Pais>%s</Pais>
        </Ubicacion>
        <Entradas>
            <Entrada id="vip">
                <Precio>%s</Precio>
                <Disponibles>%s</Disponibles>
            </Entrada>
            <Entrada id="normales">
                <Precio>%s</Precio>
                <Disponibles>%s</Disponibles>
            </Entrada>
        </Entradas>
        <Promotores>%s</Promotores>
        <Patrocinador>%s</Patrocinador>
    </Evento>',
        $_POST['id'],
        htmlspecialchars($_POST['nombre']),
        $_POST['fecha'],
        htmlspecialchars($_POST['ciudad']),
        htmlspecialchars($_POST['pais']),
        $_POST['entrada_vip_precio'],
        $_POST['entrada_vip_disponibles'],
        $_POST['entrada_normal_precio'],
        $_POST['entrada_normal_disponibles'],
        $promotoresXML,
        htmlspecialchars($_POST['patrocinador'])
    );

    try {
        $session = new Session('localhost', 1984, 'admin', 'admin');
        $session->execute("OPEN dojosearch");

        $query = $session->query("
            declare variable \$evento external;
            insert node parse-xml(\$evento) into /DojoSearch/Eventos
        ");

        $query->bind('evento', $nuevoEvento);
        $query->execute();
        $query->close();

        echo "Evento insertado. <a href='formulario.html'>Volver</a>";
    } catch (BaseXException $e) {
        die("Error BaseX: " . $e->getMessage());
    } finally {
        if (isset($session)) $session->close();
    }
} else {
    die("Método no permitido");
}
