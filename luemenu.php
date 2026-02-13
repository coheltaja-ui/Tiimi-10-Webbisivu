<?php
//http:localhost:81/Lopullinen/luemenu.php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
try{
    $yhteys=mysqli_connect("db", "root", "password", "plantti");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
}
$tulos=mysqli_query($yhteys, "select * from menu_items");
$lista = [];
while ($rivi=mysqli_fetch_object($tulos)){
    $kategoriat=new class{};
    $kategoriat->id=$rivi->id;
    $kategoriat->annos=$rivi->annos;
    $kategoriat->aineet=$rivi->aineet;
    $kategoriat->kuvaus=$rivi->kuvaus;
    $kategoriat->aktiivinen=$rivi->aktiivinen;
    $lista[]=$kategoriat;
}
mysqli_close($yhteys);
print json_encode($lista);

?>
