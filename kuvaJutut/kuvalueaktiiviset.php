<?php
//http:localhost:81/Lopullinen/jsonlue.php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
try{
    $yhteys=mysqli_connect("db", "root", "password", "plantti");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
}
$tulos=mysqli_query($yhteys, "select * from kuvat where aktiivinen=1");
$lista = [];
while ($rivi=mysqli_fetch_object($tulos)){
    $kategoriat=new class{};
    $kategoriat->id=$rivi->id;
    $kategoriat->kategoria_id=$rivi->kategoria_id;
    $kategoriat->kuvapolku=$rivi->kuvapolku;
    $kategoriat->altteksti=$rivi->altteksti;
    $lista[]=$kategoriat;
}
mysqli_close($yhteys);
print json_encode($lista);

?>