<?php

try {
    header('Content-type: application/json');
    $con = new PDO('mysql:host=localhost;dbname=csi-v3', 'root', 'CSI_2019');

    $stmt = $con->query('SELECT updated_at FROM machines ORDER BY updated_at DESC LIMIT 1');
    $machine = $stmt->fetch();
    echo($machine['updated_at']);
} catch (PDOException $ex) {
    http_response_code(500);
    echo $ex->getMessage();
}
