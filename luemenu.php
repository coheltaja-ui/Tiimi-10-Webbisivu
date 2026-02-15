<?php
//http:localhost:81/Lopullinen/luemenu.php
mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_INDEX);
try{
    $yhteys=mysqli_connect("db", "root", "password", "plantti");
    mysqli_set_charset($yhteys, "utf8mb4");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
    }
$tulos2=mysqli_query($yhteys, "select * from kategoriat");
$lista2 = [];
while ($rivi2=mysqli_fetch_object($tulos2)){
    $kategoriat=new class{};
    $kategoriat->id=$rivi2->id;
    $kategoriat->ateria=$rivi2->ateria;
    $lista2[]=$kategoriat;
}
$tulos=mysqli_query($yhteys, "select * from menu_items");
$lista = [];
while ($rivi=mysqli_fetch_object($tulos)){
    $menu_items=new class{};
    $menu_items->id=$rivi->id;
    $menu_items->annos=$rivi->annos;
    $menu_items->aineet=$rivi->aineet;
    $menu_items->kuvaus=$rivi->kuvaus;
    $menu_items->aktiivinen=$rivi->aktiivinen;
    $lista[]=$menu_items;
}
mysqli_close($yhteys);
print "<h1>Kategoriat</h1>";
foreach ($lista2 as $rivi2) {
    print "Kategoria id: " . $rivi2->id . "<br>";
    print "Ateria tyyppi: " . $rivi2->ateria . "<br>";

}

print "<h1>Ruokalista</h1>";
foreach ($lista as $rivi) {
    if ($rivi->aktiivinen == 1) {
        $aktiivisuus = "aktiivinen";
    } else {
        $aktiivisuus = "ei aktiivinen";
    }
    print "Annoksen id on: " . $rivi->id . "<br>";
    print "Annoksen nimi on: " . $rivi->annos . "<br>";
    print "Annoksen ainesosat ovat: " . $rivi->aineet . "<br>";
    print "Annoksen kuvaus: " . $rivi->kuvaus . "<br>";
    print "Tila: $aktiivisuus<br>";
    print "<button onclick='poistatuote($rivi->id)'>Poista</button><br><br>";
}

?>
<script>
function poistatuote(id) {
    if (!confirm("poistetaanko?")) return;

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };
    xmlhttp.open("GET", "poista.php?id=" + id, true);
    xmlhttp.send();
}
</script>   
<script>
function Aktiivisuusmuutos(id) {
    if (!confirm("Muutetaanko aktiivisuus?")) return;

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
    };
    xmlhttp.open("GET", "akti.php?id=" + id, true);
    xmlhttp.send();
}
</script>   