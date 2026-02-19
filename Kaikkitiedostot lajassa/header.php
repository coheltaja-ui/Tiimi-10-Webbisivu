<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>
<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <title>Ruokalista</title>
    <style>
        .header {
            background-color: #222629;
            color: white;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-left: -8px;
            margin-top: -8px;
        }
        .header a {
            color: white;
            text-decoration: none;
            background-color: #151e14;
            padding: 8px 12px;
            border-radius: 5px;
            padding-right: 50px;
            padding-left: 50px;
        }
        .header a:hover {
            background-color: #463e3e;
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <?php
         if (isset($_SESSION["kayttaja"])): ?>
         <span>
            Olet kirjautuneena: <b><?php print htmlspecialchars($_SESSION["kayttaja"]); ?></b>
             </span>
             <a href="kayttajienprinttaus.php">Näytä käyttäjät</a>
             <a href="crudlomake.php">Ruokalistan hallinta</a>
             <a href="kuvaCrud2.php">Kuvien hallinta</a>
        <?php 
        endif
         ?>
    </div>
    <div>
        <?php
if (isset($_SESSION["kayttaja"])) {
?>
    <a href="logulos.php">Kirjaudu ulos</a>
<?php
}
?>
    </div>

</div>