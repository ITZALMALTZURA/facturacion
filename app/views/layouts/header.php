
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Sistema de Facturación';
$currentPage = $currentPage ?? '';

// Ruta base de los recursos públicos
$baseUrl = '/facturacion/public';

// Punto de entrada de la aplicación
$routerUrl = $baseUrl . '/index.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Recuperar el tema seleccionado -->
    <script>
        const temaGuardado = localStorage.getItem('tema') || 'light';
        document.documentElement.setAttribute('data-theme', temaGuardado);
    </script>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- Tema -->
    <script src="<?= $baseUrl ?>/js/tema.js" defer></script>

    <!-- Estilos propios -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/estilos.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Biblioteca i18next -->
    <script src="https://cdn.jsdelivr.net/npm/i18next@23.16.8/dist/umd/i18next.min.js"></script>

    <!-- Configuración de traducciones -->
    <script src="<?= $baseUrl ?>/js/i18n.js" defer></script>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">

            <a class="navbar-brand fw-bold" href="<?= $routerUrl ?>">
                <i class="bi bi-receipt-cutoff me-2"></i>
                <span data-i18n="app.title">Sistema de Facturación</span>
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal"
                aria-expanded="false"
                aria-label="Mostrar menú">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'listado' ? 'active' : '' ?>"
                            href="<?= $routerUrl ?>">

                            <i class="bi bi-list-ul me-1"></i>
                            <span data-i18n="navigation.invoices">Facturas</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'crear' ? 'active' : '' ?>"
                            href="<?= $routerUrl ?>?action=crear">

                            <i class="bi bi-plus-circle me-1"></i>
                            <span data-i18n="navigation.new_invoice">Nueva factura</span>
                        </a>
                    </li>

                </ul>

            </div>

            <button
                type="button"
                class="btn btn-outline-light btn-sm ms-lg-3"
                id="btnTema"
                aria-label="Cambiar tema"
                title="Cambiar tema">

                <i class="bi bi-moon-stars-fill" id="iconoTema"></i>
                <span id="textoTema">Modo oscuro</span>
            </button>

            <button
                type="button"
                class="btn btn-outline-light btn-sm ms-2"
                id="btnIdioma"
                aria-label="Cambiar idioma"
                title="Cambiar idioma">
                EN
            </button>

        </div>
    </nav>

    <main class="container py-4 py-md-5">
