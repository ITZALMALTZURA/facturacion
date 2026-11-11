# Sistema de Facturación

Aplicación web para la gestión y generación de facturas, desarrollada con PHP nativo bajo una arquitectura MVC, utilizando MySQL como sistema gestor de bases de datos.

## Características

- Registro y consulta de facturas.
- Administración de clientes, monedas y series.
- Captura de conceptos o partidas de facturación.
- Cálculo automático de subtotal, IVA (16 %) y total.
- Validación de información en frontend y backend.
- Consultas a la base de datos mediante PDO y consultas preparadas.
- Generación de documentos PDF mediante Dompdf.
- Filtrado de facturas por cliente, moneda y rango de fechas.
- Interfaz adaptable con Bootstrap 5.3.
- Soporte para español e inglés mediante i18next.
- Tema claro y oscuro.

## Tecnologías utilizadas

| Tecnología | Uso |
|---|---|
| PHP | Lógica del servidor |
| MySQL | Gestión de la base de datos |
| PDO | Conexión y consultas a la base de datos |
| HTML5 | Estructura de las vistas |
| CSS3 | Estilos personalizados |
| JavaScript | Interactividad y validaciones |
| Bootstrap 5.3 | Diseño de interfaz |
| SweetAlert2 | Notificaciones |
| i18next | Internacionalización |
| Dompdf | Generación de documentos PDF |
| Composer | Administración de dependencias |
| Apache | Servidor web |

## Estructura del proyecto


facturacion/
│
├── app/
│   ├── config/
│   │   └── Database.php
│   │
│   ├── controllers/
│   │   └── FacturaController.php
│   │
│   ├── models/
│   │   ├── Cliente.php
│   │   ├── Factura.php
│   │   ├── Moneda.php
│   │   └── Serie.php
│   │
│   └── views/
│       ├── facturas/
│       │   ├── crear.php
│       │   ├── detalle.php
│       │   ├── listado.php
│       │   └── pdf.php
│       │
│       └── layouts/
│           ├── header.php
│           ├── footer.php
│           └── flash.php
│
├── public/
│   ├── css/
│   ├── js/
│   ├── locales/
│   │   ├── es/
│   │   └── en/
│   └── index.php
│
├── vendor/
├── .htaccess
├── composer.json
├── composer.lock
└── README.md
```

## Requisitos previos

Para ejecutar el proyecto localmente se necesita:

- PHP 8 o superior.
- Apache.
- MySQL o MariaDB.
- Composer.
- Extensión PDO para MySQL (`pdo_mysql`).

Se recomienda utilizar XAMPP para disponer de Apache, PHP y MySQL en un entorno local.

## Instalación y configuración

### 1. Clonar o descargar el proyecto

Colocar el proyecto dentro del directorio de Apache. En XAMPP para Windows:


C:\xampp\htdocs\facturacion
```

### 2. Instalar dependencias

Abrir una terminal en la raíz del proyecto y ejecutar:

```bash
composer install
```

Este comando instalará las dependencias definidas en `composer.lock`, incluyendo Dompdf.

### 3. Configurar la base de datos

Crear la base de datos en MySQL y configurar los parámetros de conexión en:


app/config/Database.php
```

Establecer los datos correspondientes al entorno local:

- Servidor de base de datos.
- Nombre de la base de datos.
- Usuario.
- Contraseña.

**Nota:** La estructura de las tablas y sus relaciones deben estar creadas previamente para que la aplicación funcione correctamente.

### 4. Configuración de Apache mediante .htaccess

El proyecto utiliza un archivo `.htaccess` ubicado en la raíz del directorio:


facturacion/.htaccess
```

Su contenido es:

```apache
DirectoryIndex public/index.php
Options -Indexes
```

**¿Qué hace esta configuración?**

**DirectoryIndex public/index.php**

Indica a Apache que, cuando se acceda al directorio principal del proyecto, utilice `public/index.php` como archivo de entrada predeterminado.

Esto permite acceder a la aplicación mediante:


http://localhost/facturacion
```

Sin necesidad de escribir directamente:


http://localhost/facturacion/public/index.php
```

**Options -Indexes**

Deshabilita la visualización automática del contenido de los directorios cuando no existe un archivo de índice.

Esto evita que Apache muestre listados de archivos y carpetas al acceder a determinados directorios.

#### Configuración de Apache en XAMPP

Para que Apache permita aplicar las instrucciones del archivo `.htaccess`, es necesario verificar la configuración de `httpd.conf`.

Abrir:


C:\xampp\apache\conf\httpd.conf
```

Localizar la configuración del directorio `htdocs`:

```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
    Require all granted
</Directory>
```

La directiva `AllowOverride All` permite que Apache procese las configuraciones definidas en los archivos `.htaccess`.

Si se encuentra configurada como `AllowOverride None`, será necesario modificarla.

También se debe comprobar que la directiva `mod_rewrite` esté disponible si posteriormente se incorporan reglas de reescritura de URL. **La configuración actual no utiliza `mod_rewrite`**, ya que el enrutamiento se realiza mediante el controlador frontal y el parámetro `action`.

Después de modificar la configuración de Apache, guardar los cambios y reiniciar el servicio desde el panel de control de XAMPP.

#### ¿Por qué se utiliza esta configuración?

Se utiliza un único punto de entrada (`public/index.php`) para centralizar el acceso a las funcionalidades del sistema.

El archivo `index.php` recibe el parámetro `action` y determina qué método del controlador debe ejecutarse.

Por ejemplo:


http://localhost/facturacion/public/index.php?action=crear
http://localhost/facturacion/public/index.php?action=show&serid=1&facid=1
http://localhost/facturacion/public/index.php?action=pdf&serid=1&facid=1
```

Las rutas internas se construyen utilizando una URL base para mantener el funcionamiento de los enlaces y recursos del proyecto.

### 5. Ejecutar la aplicación

Iniciar Apache y MySQL desde XAMPP y acceder desde el navegador:


http://localhost/facturacion
```

## Generación de PDF

La aplicación utiliza Dompdf para generar documentos PDF a partir de vistas HTML.

La dependencia se administra mediante Composer:

```bash
composer require dompdf/dompdf
```

El archivo de carga automática de Composer se utiliza para acceder a las clases de la biblioteca:

```php
require_once __DIR__ . '/../../vendor/autoload.php';
```

## Arquitectura

El proyecto utiliza el patrón Modelo-Vista-Controlador (MVC):

- **Modelo:** administra las consultas y operaciones relacionadas con la base de datos.
- **Vista:** presenta la información y los formularios al usuario.
- **Controlador:** recibe las solicitudes, coordina los modelos y selecciona las vistas correspondientes.

El archivo `public/index.php` funciona como punto de entrada y distribuye las solicitudes hacia los métodos correspondientes de `FacturaController`.

## Autor

Desarrollado como proyecto de aplicación web utilizando PHP, MySQL y tecnologías de desarrollo frontend.