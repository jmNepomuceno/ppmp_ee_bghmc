<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$templatePath = realpath(__DIR__ . '/../exports/templates/template.xlsx');

if (!$templatePath || !file_exists($templatePath)) {
    http_response_code(500);
    echo 'Template file not found at: ' . $templatePath;
    exit;
}

try {
    // Just load the template, do not access any sheet or modify
    $spreadsheet = IOFactory::load($templatePath);

    // Clean output buffer in case of any echoes
    if (ob_get_length()) {
        ob_clean();
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="template.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error loading template: ' . $e->getMessage();
    exit;
}
