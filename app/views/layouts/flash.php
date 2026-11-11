
<?php if (!empty($_SESSION['flash'])): ?>

    <?php
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    ?>

    <script>
        Swal.fire({
            icon: <?= json_encode($flash['icon']) ?>,
            title: <?= json_encode($flash['title']) ?>,
            text: <?= json_encode($flash['text']) ?>,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#0d6efd'
        });
    </script>

<?php endif; ?>
