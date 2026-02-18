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


$virhe = '';
$info  = '';

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($del_id > 0) {
        mysqli_query($yhteys, "DELETE FROM kuvat WHERE id = $del_id");
    }
    header('Location: kuvaCrud2.php'); exit;
}

// MUOKKAA
$edit = null;
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $eid = (int)($_POST['id'] ?? 0);
    if ($eid > 0) {
        $res = mysqli_query($yhteys, "SELECT * FROM kuvat WHERE id=$eid");
        $edit = mysqli_fetch_assoc($res);
        if (!$edit) { $virhe = 'Annettua ID:tä ei löytynyt.'; }
    } else {
        $virhe = 'Anna muokattava ID.';
    }
}
// Muokkaus esitäyttö myös GET:llä listasta
if (!$edit && isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid>0) {
        $res = mysqli_query($yhteys, "SELECT * FROM kuvat WHERE id=$eid");
        $edit = mysqli_fetch_assoc($res);
        if (!$edit) { $virhe = 'Annettua ID:tä ei löytynyt.'; }
    }
}

// TALLENNA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ( !isset($_POST['action']) || $_POST['action'] === 'save')) {
    $id     = (int)($_POST['id'] ?? 0);
    $kat    = (int)($_POST['kategoria_id'] ?? 0);
    $kuvapolku  = $_POST['kuvapolku']  ?? '';
    $altteksti = $_POST['altteksti'] ?? '';
    $akt    = isset($_POST['aktiivinen']) ? 1 : 0;

    if ($id <= 0) {
        $virhe = 'ID on pakollinen.';
    } elseif ($kat <= 0 || $kuvapolku === '' || $altteksti === '') {
        $virhe = 'Täytä kaikki kentät.';
    } else {
        // Päättele: päivitys jos rivi löytyy annetulla ID:llä, muuten lisäys
        $exists = mysqli_query($yhteys, "SELECT 1 FROM kuvat WHERE id=$id");
        if ($exists && mysqli_fetch_row($exists)) {
            mysqli_query($yhteys,
                "UPDATE kuvat SET 
                    kategoria_id=$kat, kuvapolku='$kuvapolku', altteksti='$altteksti', aktiivinen=$akt
                 WHERE id=$id");
            $info = 'Päivitetty.';
        } else {
            mysqli_query($yhteys,
                "INSERT INTO kuvat (id, kategoria_id, kuvapolku, altteksti, aktiivinen)
                 VALUES ($id, $kat, '$kuvapolku', '$altteksti', $akt)");
            $info = 'Lisätty.';
        }
        header('Location: kuvaCrud2.php'); exit;
    }
}

$lista = mysqli_query($yhteys, 'SELECT * FROM kuvat ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="fi">
<head>
<meta charset="UTF-8">
<title>Kuvien hallinta</title>
<style>
  .btn-blue,
  .btn-blue:link,
  .btn-blue:visited,
  .btn-blue:hover,
  .btn-blue:active,
  .btn-blue:focus {
    background: #1976d2;
    color: #fff;
    border: 0;
    padding: 6px 10px;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    outline: none;
  }
  .btn-group { display: inline-flex; gap: 6px; }
</style>

<script>
function tyhjennaLomake(){
  const f = document.getElementById('crudForm');
  if (!f) return false;
  f.reset();
  f.querySelector('input[name="id"]').value = '';
  return false;
}
</script>

</head>
<body>
<h2>Kuvien hallinta</h2>

<?php 
if ($virhe !== ''): 
?>
  <p style="color:red;"><?= $virhe ?></p>
<?php 
elseif ($info !== ''): 
?>
  <p style="color:green;"><?= $info ?></p>
<?php 
endif; 
?>

<form method="post" action="kuvaCrud2.php" id="crudForm">
  ID (pakollinen): <input required type="number" name="id" min="1" value="<?= $edit ? $edit['id'] : '' ?>"><br><br>
  Ateria:<select name="kategoria_id">
    <option value="1" <?= ($edit && $edit['kategoria_id']==1)?'selected':'' ?>>Alkupala</option>
    <option value="2" <?= ($edit && $edit['kategoria_id']==2)?'selected':'' ?>>Pääruoka</option>
    <option value="3" <?= ($edit && $edit['kategoria_id']==3)?'selected':'' ?>>Jälkiruoka</option>
  </select><br><br>
  Kuvapolku:  <input type="text" name="kuvapolku"  value="<?= $edit['kuvapolku']  ?? '' ?>"><br><br>
  Aineet: <input type="text" name="altteksti" value="<?= $edit['altteksti'] ?? '' ?>"><br><br>
  Aktiivinen: <input type="checkbox" name="aktiivinen" <?= ($edit && $edit['aktiivinen']==1)?'checked':'' ?>><br><br>

  <button type="submit" name="action" value="save">Tallenna</button>
  <button type="button" onclick="return tyhjennaLomake();">Tyhjennä lomake</button>
</form>

<hr>

<h3>Lista</h3>
<table border="1" cellpadding="6">
  <tr>
    <th>#</th>
    <th>id</th>
    <th>Ateria</th>
    <th>Kuvapolku</th>
    <th>Altteksti</th>
    <th>Tila</th>
    <th>Toiminnot</th>
  </tr>
  <?php $i=1; while($r = mysqli_fetch_assoc($lista)): ?>
  <tr>
    <td><?= $i++ ?></td>
    <td><?= $r['id'] ?></td>
    <td><?php
        if ($r['kategoria_id']==1) echo 'Alkupala';
        elseif ($r['kategoria_id']==2) echo 'Pääruoka';
        else echo 'Jälkiruoka';
    ?></td>
    <td><?= $r['kuvapolku'] ?></td>
    <td><?= $r['altteksti'] ?></td>
    <td><?= ($r['aktiivinen']==1)?'aktiivinen':'ei aktiivinen' ?></td>
    <td>
      <span class="btn-group">
        <a href="kuvaCrud2.php?edit=<?= $r['id'] ?>" class="btn-blue">Muokkaa</a>
        <a href="kuvaCrud2.php?delete=<?= $r['id'] ?>" class="btn-blue" onclick="return confirm('Poistetaanko?');">Poista</a>
      </span>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

</body>
</html>