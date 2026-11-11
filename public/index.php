
<?php

require_once __DIR__ . '/../app/controllers/FacturaController.php';

$controller = new FacturaController();

$action = $_GET['action'] ?? 'index';

switch ($action) {

    case 'crear':
        $controller->crear();
        break;

    case 'guardar':
        $controller->guardar();
        break;

    case 'show':
        $controller->mostrar();
        break;


    case 'pdf':
        $controller->generarPdf();
        break;


    default:
        $controller->index();
        break;
}
