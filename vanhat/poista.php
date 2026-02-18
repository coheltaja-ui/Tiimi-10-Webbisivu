<?php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
try{
    $yhteys=mysqli_connect("db", "root", "password", "plantti");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM menu_items WHERE id = $id";
    mysqli_query($yhteys, $sql);
}

mysqli_close($yhteys);
?>
