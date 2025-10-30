<?php
$plantillas = [
    [
        'slug' => 'plantilla01',
        'title' => 'TIENDA ONLINE',
        'img' => 'vistas/img/plantilla01/plantilla01.png',
        'desc' => 'Tienda online completa con carrito y pasarelas de pago.'
    ],
    [
        'slug' => 'plantilla02',
        'title' => 'BLOG',
        'img' => 'vistas/img/plantilla02/plantilla02.png',
        'desc' => 'Plantilla ideal para blogs y contenido editorial.'
    ],
    [
        'slug' => 'plantilla03',
        'title' => 'RESTAURANTE',
        'img' => 'vistas/img/plantilla03/plantilla03.png',
        'desc' => 'Menús, reservas y presentación de platos.'
    ],
];
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12 text-center">
            <h1>Bienvenido a Lemus Pool</h1>
            <p>Elige una plantilla para ver las opciones de demostración.</p>
        </div>
    </div>

    <div class="row mt-4">
        <?php foreach ($plantillas as $index => $p): ?>
            <?php
                $slug = $p['slug'];
                $title = $p['title'];
                $img = $p['img'];
                $desc = $p['desc'];

                // Rutas web para los enlaces
                $webIndex = "plantillas/{$slug}/index.php";
                $webAdmin = "plantillas/{$slug}/admin/index.php";

                // Rutas de archivo para comprobación (relativas al directorio actual)
                $fileIndex = __DIR__ . '/../../' . $webIndex;
                $fileAdmin = __DIR__ . '/../../' . $webAdmin;

                $indexExists = file_exists($fileIndex);
                $adminExists = file_exists($fileAdmin);
            ?>
            <div class="col-sm-4 mb-4">
                <div class="card">
                    <img class="card-img-top" src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($title) ?>">
                    <div class="card-body">
                        <h5 class="card-title text-center"><?= htmlspecialchars($title) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($desc) ?></p>
                        <div class="d-flex justify-content-center">
                            <div class="btn-group" role="group">
                                <?php if ($indexExists): ?>
                                    <a class="btn btn-primary" href="<?= htmlspecialchars($webIndex) ?>" target="_blank">DEMO</a>
                                <?php else: ?>
                                    <a class="btn btn-secondary disabled" href="#" aria-disabled="true" tabindex="-1">DEMO</a>
                                <?php endif; ?>

                                <button id="btnGroupDrop<?= $index ?>" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop<?= $index ?>">
                                    <?php if ($adminExists): ?>
                                        <a class="dropdown-item" href="<?= htmlspecialchars($webAdmin) ?>" target="_blank">Página Administrativa</a>
                                    <?php else: ?>
                                        <a class="dropdown-item disabled" href="#" aria-disabled="true" tabindex="-1">Página Administrativa (no disponible)</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>