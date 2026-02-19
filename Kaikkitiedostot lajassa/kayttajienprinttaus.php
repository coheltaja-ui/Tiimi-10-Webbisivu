<?php
//http:localhost:81/Lopullinen/kayttajienprinttaus.php
$initials = parse_ini_file("./.ht.asetukset.ini");

try{
      $initials=parse_ini_file("./.ht.asetukset.ini");
    $yhteys=mysqli_connect($initials["databaseserver"], $initials["username"], $initials["password"], $initials["database"]);
    mysqli_set_charset($yhteys, "utf8mb4");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
}

$sql = "SELECT id, tunnus, salasana FROM kayttaja";
$tulos = mysqli_query($yhteys, $sql);

if ($tulos) {
    print "<table border='1'>";
    print "<tr><th>ID</th><th>Tunnus</th><th>Salasana</th></tr>";

    while ($rivi = mysqli_fetch_assoc($tulos)) {
        print "<tr>";
        print "<td>" . htmlspecialchars($rivi["id"]) . "</td>";
        print "<td>" . htmlspecialchars($rivi["tunnus"]) . "</td>";
        print "<td>" . htmlspecialchars($rivi["salasana"]) . "</td>";
        print "</tr>";
    }

    print "</table>";
} else {
    print "Query failed.";
}

mysqli_close($yhteys);

?>