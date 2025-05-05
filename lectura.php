<?php
/* 
    Muestra todos los eventos almacenados en la base de datos BaseX.
    Variables clave:
    - $session: Sesión activa con el servidor BaseX
    - $query: Consulta XQuery para recuperar los eventos
    - $result: Resultado XML crudo devuelto por la consulta
    - $xml: Objeto SimpleXML usado para recorrer los eventos y mostrarlos en HTML
*/

require_once 'BaseXClient/Query.php';
require_once 'BaseXClient/Session.php';
require_once 'BaseXClient/BaseXException.php';

use BaseXClient\Session;
use BaseXClient\BaseXException;
use BaseXClient\Query;

try {
    $session = new Session("localhost", 1984, "admin", "admin");
    $session->execute("OPEN dojosearch");

    $query = $session->query('
        for $evento in /DojoSearch/Eventos/Evento
        return $evento
    ');

    $result = $query->execute();
    $query->close();

    $xml = simplexml_load_string("<Eventos>$result</Eventos>");

    echo "<h1>Eventos Registrados</h1>";

    if ($xml && $xml->count() > 0) {
        foreach ($xml->Evento as $evento) {
            echo "<div style='border:1px solid #ccc; padding:15px; margin:10px;'>";
            echo "<h2>{$evento->Nombre}</h2>";
            echo "<p><strong>ID:</strong> {$evento['id']}</p>";
            echo "<p><strong>Fecha:</strong> {$evento->Fecha}</p>";
            echo "<p><strong>Ubicación:</strong> {$evento->Ubicacion->Ciudad}, {$evento->Ubicacion->Pais}</p>";

            echo "<h3>Entradas</h3>";
            foreach ($evento->Entradas->Entrada as $entrada) {
                echo "<p>Tipo: {$entrada['id']} | 
                    Precio: {$entrada->Precio}€ | 
                    Disponibles: {$entrada->Disponibles}</p>";
            }

            echo "<h3>Promotores</h3>";
            foreach ($evento->Promotores->Promotor as $promotor) {
                echo "<div style='margin-left:20px;'>
                        <p>ID: {$promotor['id']}<br>
                        Nombre: {$promotor->Nombre}<br>
                        Contacto: {$promotor->Contacto}</p>
                    </div>";
            }

            echo "<p><strong>Patrocinador:</strong> {$evento->Patrocinador}</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No hay eventos registrados</p>";
    }
} catch (BaseXException $e) {
    echo "<h3>Error de BaseX:</h3> <p>" . htmlentities($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h3>Error general:</h3> <p>" . htmlentities($e->getMessage()) . "</p>";
} finally {
    if (isset($session)) $session->close();
}
