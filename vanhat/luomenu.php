
<?php
$json=isset($_POST["menu"]) ? $_POST["menu"] : "";

if (!($menu=tarkistaJson($json))){
    print "Täytä kaikki kentät";
    exit;
}

mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try{
    $yhteys=mysqli_connect("db", "root", "password", "plantti");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
}

$sql="insert into menu_items (kategoria_id, annos, aineet, kuvaus, aktiivinen) values(?, ?, ?, ?, ?)";
$stmt=mysqli_prepare($yhteys, $sql);
mysqli_stmt_bind_param($stmt, 'isssi', $menu->kategoria_id, $menu->annos, $menu->aineet, $menu->kuvaus, $menu->aktiivinen);
mysqli_stmt_execute($stmt);
mysqli_close($yhteys);
print $json;
?>

<?php
function tarkistaJson($json){
    if (empty($json)){
        return false;
    }
    $menu=json_decode($json, false);
    return $menu;
}
?>