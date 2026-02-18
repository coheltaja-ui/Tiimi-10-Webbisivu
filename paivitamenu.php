<?php

$id = isset($_POST["id"]) ? ($_POST["id"]) : 0;
$kategoria_id = isset($_POST["kategoria_id"]) ? ($_POST["kategoria_id"]) : 0;
$annos = isset($_POST["annos"]) ? $_POST["annos"] : "";
$aineet = isset($_POST["aineet"]) ? $_POST["aineet"] : "";
$kuvaus = isset($_POST["kuvaus"]) ? $_POST["kuvaus"] : "";
$aktiivinen = isset($_POST["aktiivinen"]) ? 1 : 0;

if ($id <= 0 || $kategoria_id <= 0 || $annos === "" || $aineet === "" || $kuvaus === "") {
    header("Location: ./luomenu.html");
    exit;
}

mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);

try {
    $yhteys = mysqli_connect("db", "root", "password", "plantti");

    } catch(Exception $e){
    header("Location: ../html/yhteysvirhe.html");
    exit;
}

$sql = "UPDATE menu_items 
        SET kategoria_id = ?, annos = ?, aineet = ?, kuvaus = ?, aktiivinen = ? 
        WHERE id = ?";

$stmt = mysqli_prepare($yhteys, $sql);
mysqli_stmt_bind_param($stmt, 'sssii', $kategoria_id, $annos, $aineet, $kuvaus, $aktiivinen, $id);
mysqli_stmt_execute($stmt);
mysqli_close($yhteys);

header("Location: ./luemenu.html");
exit;

?>