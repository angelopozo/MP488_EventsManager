<?php
/* 
    Elimina un evento por ID.
    Variables:
    - $_POST['id']: ID del evento a borrar
    - $session: Conexión a BaseX
    - $query: Consulta XQuery de eliminación
*/

require_once 'BaseXClient/Query.php';
require_once 'BaseXClient/Session.php';
require_once 'BaseXClient/BaseXException.php';

use BaseXClient\Session;
use BaseXClient\BaseXException;
use BaseXClient\Query;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = htmlspecialchars($_POST['id']);

    try {
        $session = new Session('localhost', 1984, 'admin', 'admin');
        $session->execute("OPEN dojosearch");

        $query = $session->query("
            delete node /DojoSearch/Eventos/Evento[@id='$id']
        ");

        $query->execute();
        echo "Evento $id eliminado. <a href='formulario.html'>Volver</a>";
    } catch (BaseXException $e) {
        die("Error BaseX: " . $e->getMessage());
    } finally {
        if (isset($session)) $session->close();
    }
} else {
    die("ID no proporcionado");
}
