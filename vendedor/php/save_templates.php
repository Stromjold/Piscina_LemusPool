<?php
// Guarda/actualiza una plantilla. Recibe multipart/form-data:
// - id (string)
// - name, description
// - demoLogin, demoMain, demoAdmin (opcional)
// - image (opcional file)
header('Content-Type: application/json');
$uploadsDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$dataFile = $dataDir . DIRECTORY_SEPARATOR . 'templates.json';
if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
$resp = ['success' => false, 'message' => '', 'template' => null];
try {
    $id = isset($_POST['id']) ? trim($_POST['id']) : null;
    if (!$id) throw new Exception('Falta id');
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $demos = [
        'login' => isset($_POST['demoLogin']) ? trim($_POST['demoLogin']) : '',
        'main' => isset($_POST['demoMain']) ? trim($_POST['demoMain']) : '',
        'admin' => isset($_POST['demoAdmin']) ? trim($_POST['demoAdmin']) : '',
    ];

    // Cargar existente
    $templates = [];
    if (file_exists($dataFile)) {
        $raw = file_get_contents($dataFile);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            // soportar tanto array indexado o asociativo
            foreach ($decoded as $tpl) {
                if (isset($tpl['id'])) $templates[$tpl['id']] = $tpl;
            }
        }
    }

    // Procesar imagen si se subió
    $imagePath = isset($templates[$id]['image']) ? $templates[$id]['image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $f = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if (!in_array($f['type'], $allowed)) throw new Exception('Tipo de imagen no permitido');
        // nombre único
        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
        $filename = $id . '_' . time() . '.' . $ext;
        $dst = $uploadsDir . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($f['tmp_name'], $dst)) throw new Exception('No se pudo mover la imagen');
        $imagePath = $dst;
    }

    $templates[$id] = [
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'image' => $imagePath,
        'demos' => $demos
    ];

    // Guardar como lista asociativa
    $out = array_values($templates);
    file_put_contents($dataFile, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    // Preparar respuesta (ajustar ruta de imagen para público)
    $tplResp = $templates[$id];
    if (!empty($tplResp['image']) && !preg_match('#^https?://#', $tplResp['image'])) {
        $tplResp['image'] = '/vendedor/php/uploads/' . basename($tplResp['image']);
    }

    $resp['success'] = true;
    $resp['message'] = 'Plantilla guardada';
    $resp['template'] = $tplResp;
    echo json_encode($resp);
    exit;
} catch (Exception $e) {
    $resp['message'] = $e->getMessage();
    echo json_encode($resp);
    exit;
}
