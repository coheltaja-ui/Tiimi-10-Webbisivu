<?php
session_start();
if (!isset($_SESSION['kayttaja'])) {
    header('Location: kirjauduajax.html');
    exit;
}

try{
    $initials=parse_ini_file("./.ht.asetukset.ini");
    $yhteys=mysqli_connect($initials["databaseserver"], $initials["username"], $initials["password"], $initials["database"]);
    mysqli_set_charset($yhteys, "utf8mb4");
}
catch(Exception $e){
    print "Yhteysvirhe";
    exit;
}

// POISTO
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $stmt = mysqli_prepare($yhteys, "DELETE FROM menu_items WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    header("Location: crudlomake.php");
    exit;
}

// MUOKKAUS
$edit = null;

if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($yhteys, "SELECT * FROM menu_items WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $tulos = mysqli_stmt_get_result($stmt);
    $edit = mysqli_fetch_assoc($tulos);
}

// TALLENNUS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['id'];
    $kategoria_id = $_POST['kategoria_id'];
    $annos        = $_POST['annos'];
    $aineet       = $_POST['aineet'];
    $kuvaus       = $_POST['kuvaus'];
    $aktiivinen   = isset($_POST['aktiivinen']) ? 1 : 0;

    if (!ctype_digit($id) || !ctype_digit($kategoria_id)) {
        header("Location: crudlomake.php");
        exit;
    }

    $id_i  = (int)$id;
    $kat_i = (int)$kategoria_id;

    $stmt = mysqli_prepare($yhteys, "SELECT 1 FROM menu_items WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id_i);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_fetch_row($res)) {
        $stmt = mysqli_prepare($yhteys,
            "UPDATE menu_items
             SET kategoria_id=?, annos=?, aineet=?, kuvaus=?, aktiivinen=?
             WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'isssii', $kat_i, $annos, $aineet, $kuvaus, $aktiivinen, $id_i);
        mysqli_stmt_execute($stmt);

    } else {
        $stmt = mysqli_prepare($yhteys,
            "INSERT INTO menu_items
             (id, kategoria_id, annos, aineet, kuvaus, aktiivinen)
             VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'iisssi', $id_i, $kat_i, $annos, $aineet, $kuvaus, $aktiivinen);
        mysqli_stmt_execute($stmt);
    }
    header("Location: crudlomake.php");
    exit;
}

$lista = mysqli_query($yhteys, "SELECT * FROM menu_items ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fi">
<head>
<meta charset="UTF-8">
<title>Menun hallinta</title>
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
<?php include 'header.php'; ?>
<h2>Menun hallinta</h2>

<form method="post" action="crudlomake.php" id="crudForm">
  ID (pakollinen): <input required type="number" name="id" min="1" value="<?= $edit ? $edit['id'] : '' ?>"><br><br>
  Ateria:<select name="kategoria_id">
    <option value="1" <?= ($edit && $edit['kategoria_id']==1)?'selected':'' ?>>Alkupala</option>
    <option value="2" <?= ($edit && $edit['kategoria_id']==2)?'selected':'' ?>>Pääruoka</option>
    <option value="3" <?= ($edit && $edit['kategoria_id']==3)?'selected':'' ?>>Jälkiruoka</option>
  </select><br><br>
  Annos:  <input type="text" name="annos"  value="<?= $edit['annos']  ?? '' ?>"><br><br>
  Aineet: <input type="text" name="aineet" value="<?= $edit['aineet'] ?? '' ?>"><br><br>
  Kuvaus: <input type="text" name="kuvaus" value="<?= $edit['kuvaus'] ?? '' ?>"><br><br>
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
    <th>Annos</th>
    <th>Aineet</th>
    <th>Kuvaus</th>
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
    <td><?= $r['annos'] ?></td>
    <td><?= $r['aineet'] ?></td>
    <td><?= $r['kuvaus'] ?></td>
    <td><?= ($r['aktiivinen']==1)?'aktiivinen':'ei aktiivinen' ?></td>
    <td>
      <span class="btn-group">
        <a href="crudlomake.php?edit=<?= $r['id'] ?>" class="btn-blue">Muokkaa</a>
        <a href="crudlomake.php?delete=<?= $r['id'] ?>" class="btn-blue" onclick="return confirm('Poistetaanko?');">Poista</a>
      </span>
    </td>
  </tr>
  <?php endwhile; ?>
</table>
</body>
</html>