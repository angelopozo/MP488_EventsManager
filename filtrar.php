<?php
/* 
    Busca y muestra un evento por ID.
    Variables:
    - $_GET['id']: ID a buscar
    - $xml: Objeto SimpleXML con datos del evento
    - $result: Resultado de la consulta XQuery
*/

echo '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Eventos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">';

require_once 'BaseXClient/Query.php';
require_once 'BaseXClient/Session.php';
require_once 'BaseXClient/BaseXException.php';

use BaseXClient\Session;
use BaseXClient\BaseXException;
use BaseXClient\Query;

$id = $_GET['id'] ?? '';
if (empty($id)) die("ID requerido");

try {
    $session = new Session('localhost', 1984, 'admin', 'admin');
    $session->execute("OPEN dojosearch");

    $query = $session->query("
        declare variable \$id external;
        /DojoSearch/Eventos/Evento[@id = \$id]
    ");

    $query->bind('id', $id);
    $result = $query->execute();

    if (!empty($result)) {
        $xml = simplexml_load_string($result);
        echo "<h2>Evento $id</h2>";
        echo "<ul>
            <li>Nombre: {$xml->Nombre}</li>
            <li>Fecha: {$xml->Fecha}</li>
            <li>Ubicación: {$xml->Ubicacion->Ciudad}, {$xml->Ubicacion->Pais}</li>
            <li>Patrocinador: {$xml->Patrocinador}</li>
        </ul>";

        echo "<h3>Entradas</h3>";
        foreach ($xml->Entradas->Entrada as $entrada) {
            echo "<p>Tipo: {$entrada['id']} | 
                Precio: {$entrada->Precio} | 
                Disponibles: {$entrada->Disponibles}</p>";
        }

        echo "<h3>Promotores</h3>";
        foreach ($xml->Promotores->Promotor as $promotor) {
            echo "<p>ID: {$promotor['id']} | 
                Nombre: {$promotor->Nombre} | 
                Contacto: {$promotor->Contacto}</p>";
        }
    } else {
        echo "Evento no encontrado";
    }
} catch (BaseXException $e) {
    die("Error BaseX: " . $e->getMessage());
} finally {
    if (isset($session)) $session->close();
}
echo '</div></body></html>';
