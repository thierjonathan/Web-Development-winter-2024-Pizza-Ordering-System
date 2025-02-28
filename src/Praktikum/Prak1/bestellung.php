<?php
header('Content-Type: text/html; charset=utf-8');
echo <<< HTML
<!DOCTYPE html>
<html lang="de">  
<head>
    <meta charset="UTF-8" />
    <!-- für später: CSS include -->
    <!-- <link rel="stylesheet" href="XXX.css"/> -->
    <!-- für später: JavaScript include -->
    <!-- <script src="XXX.js"></script> -->
    <title>Bestellseite</title>
</head>
<body>
    <h1>Bestellung</h1>
    <h2>Speisekarte</h2>
    <p>Eigentlicher Inhalt</p>

    <section class="menu-item">
        <img src="img/margherita.png" height="100" width="100" alt="pizza margherita">
        <p>margherita</p>
        <p>4.00 euro</p>
    </section>

    
    <section class="menu-item">
        <img src="img/Salami.jpeg" height="100" width="100" alt="pizza salami">
        <p>Salami</p>
        <p>4.50 euro</p>
    </section>

    
    <section class="menu-item">
        <img src="img/hawaii.jpg" height="100" width="100" alt="pizza hawaii">
        <p>Hawaii</p>
        <p>5.50 euro</p>
    </section>

    <h2>Warenkorb</h2>
    <form method="get" action="https://echo.fbi.h-da.de/">
        <select name="myselect" size="3" tabindex="1">
            <option value="margherita" selected> margherita</option>
            <option value="salami"> salami</option>
            <option value="Hawaii"> hawaii</option>
        </select><br>
        <p>14.50 euro</p>
        <textarea tabindex="2" accesskey="a">ihre adresse</textarea><br>
        <input type="reset" value="alle löschen">
        <input type="button" value="auswahl löschen" onclick="discardSelection()">
        <input type="submit" value="Bestellen">
    </form>
</body>
</html>
HTML;