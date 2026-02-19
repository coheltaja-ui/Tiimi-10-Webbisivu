<?php
$initials = parse_ini_file("../.ht.asetukset.ini");
try{
      $initials=parse_ini_file("../.ht.asetukset.ini");
    $yhteys=mysqli_connect($initials["databaseserver"], $initials["username"], $initials["password"], $initials["database"]);
    mysqli_set_charset($yhteys, "utf8mb4");
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