<?php
echo realpath(__DIR__ . '/../exports/template/template.xlsx');
echo '<br>';
echo file_exists(__DIR__ . '/../exports/template/template.xlsx') ? 'File exists' : 'File not found';
?>