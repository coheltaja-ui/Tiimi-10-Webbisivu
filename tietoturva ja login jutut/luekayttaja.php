<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION["kayttaja"])) {
    echo json_encode(["kayttaja" => $_SESSION["kayttaja"]]);
} else {
    echo json_encode(["kayttaja" => null]);
}
exit;
?>