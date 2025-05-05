<?php
/* 
    Gestiona la base de datos 'resultadoseconomicos' en BaseX a través de REST.
    Funciones principales:
    - Crear base de datos (método PUT)
    - Eliminar base de datos (método DELETE)
    - Usando conexión REST mediante file_get_contents y cabeceras HTTP
    Variables clave:
    - $restEndpoint: URL del endpoint REST de BaseX
    - $action: Acción POST (create o delete)
    - sendRestRequest(): Función personalizada para hacer peticiones HTTP con XML
*/

require_once 'BaseXClient/BaseXException.php';
require_once 'BaseXClient/Session.php';

use BaseXClient\Session;
use BaseXClient\BaseXException;

// Configuración de la nueva base de datos
$dbName = "resultadoseconomicos";
$restEndpoint = "http://localhost:8080/rest/$dbName";

// Función para enviar solicitudes REST
function sendRestRequest($method, $url, $data = null)
{
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => "Authorization: Basic " . base64_encode("admin:admin") . "\r\n" .
                "Content-Type: application/xml",
            'content' => $data
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    $status = isset($http_response_header[0]) ? $http_response_header[0] : 'Sin respuesta HTTP';
    return [$status, $response];

    if ($response === false) {
        return ['Error: No se pudo conectar al servidor REST de BaseX.', null];
    }
    
}

// Manejar acciones
$action = $_POST['action'] ?? '';
try {
    $session = new Session('localhost', 1984, 'admin', 'admin');

    switch ($action) {
        case 'create':
            // Crear base vía REST
            list($status, $result) = sendRestRequest("PUT", $restEndpoint);
            echo "Base creada: $status";
            break;

        case 'delete':
            // Eliminar base vía REST
            list($status, $result) = sendRestRequest("DELETE", $restEndpoint);
            echo "Base eliminada: $status";
            break;

        default:
            // Mostrar formulario
            echo '
            <h2>Gestión de Base de Datos Económicos</h2>
            <form method="post">
                <button type="submit" name="action" value="create">Crear Base</button>
                <button type="submit" name="action" value="delete">Eliminar Base</button>
            </form>';
            break;
    }
} catch (BaseXException $e) {
    die("Error: " . $e->getMessage());
} finally {
    if (isset($session)) $session->close();
}
