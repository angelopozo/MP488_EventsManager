# Gestión de Eventos Deportivos

Sistema CRUD para gestión de eventos de artes marciales usando BaseX como backend XML.

## 🚀 Funcionalidades
- **CRUD Completo**: 
  - Crear eventos con múltiples promotores
  - Buscar por ID con visualización detallada
  - Edición completa de todos los campos
  - Eliminación segura de registros
- **Gestión de Bases de Datos**:
  - Crear/eliminar base de datos económica via REST
- **Características Técnicas**:
  - XQuery para operaciones XML
  - Validación de campos obligatorios
  - Diseño responsivo básico

## 📁 Estructura del Repositorio
| Archivo | Descripción |
|---------|-------------|
| `formulario.html` | Interfaz principal con todos los formularios |
| `borrar.php` | Lógica de eliminación de eventos |
| `filtrar.php` | Búsqueda y visualización de eventos |
| `insertar.php` | Creación de nuevos registros |
| `actualizar.php` | Modificación de eventos existentes |
| `lectura.php` | Listado completo de eventos |
| `resultadoseconomicos.php` | Gestión de base de datos económica |
| `users.xml` | Datos de ejemplo en formato XML |

## ⚙️ Requisitos
- Servidor BaseX 9.x+
- PHP 7.4+ con extensiones:
  - SimpleXML
  - HTTP wrappers (para REST)

## 🛠 Instalación
1. Clonar repositorio:
```bash
git clone https://github.com/tu-usuario/gestion-eventos.git
```
2. Importar `users.xml` a BaseX:
```bash
basex -c "CREATE DB dojosearch users.xml"
```
3. Configurar credenciales en archivos PHP si es necesario

## 📌 Notas
- Los precios se manejan en euros (€)
- Campos obligatorios marcados con * en formularios
- Puerto REST predeterminado: 8984
