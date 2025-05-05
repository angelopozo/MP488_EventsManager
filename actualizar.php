<?php
/* 
    Modifica eventos existentes.
    Variables destacadas:
    - $_POST['id']: ID del evento a modificar
    - $eventoActualizado: Nuevo XML con datos actualizados
    - replace node: Consulta XQuery para reemplazo
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

    $eventoActualizado = sprintf(
        '<Evento id="%s">
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
            declare variable \$id external;
            declare variable \$nuevoEvento external;
            replace node /DojoSearch/Eventos/Evento[@id = \$id] with parse-xml(\$nuevoEvento)
        ");

        $query->bind('id', $_POST['id']);
        $query->bind('nuevoEvento', $eventoActualizado);
        $query->execute();
        $query->close();

        echo "Evento actualizado. <a href='formulario.html'>Volver</a>";
    } catch (BaseXException $e) {
        die("Error BaseX: " . $e->getMessage());
    } finally {
        if (isset($session)) $session->close();
    }
} else {
    die("Método no permitido");
}
