<?php
header('Content-Type: text/html; charset=utf-8');
echo <<< HTML
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8" />
        <title>Fahrer</title>
    </head>

    <body>
        <h1>Fahrer</h1>
        <section>
            <!--Bestellung Infos-->
            <section>
                <h2>Bestellung #10</h2>
                <h3>Haardtring 15 64295, Darmstadt</h3>
                <ol>
                    <li>Salami Pizza</li>
                    <li>Margherita Pizza</li> 
                </ol>
                <p>14.50 Euro</p>
            <br>
            <!--Status-->
                <p>Status:</p>
                <form method="get" action="https://echo.fbi.h-da.de/">
                    <select name="status_update" size="1" tabindex="1">
                        <option value="gebacken" selected>Gebacken</option>
                        <option value="in_lieferung">In Lieferung</option>
                        <option value="geliefert">Geliefert</option>
                    </select><br>
                    <input type="submit" value="Speichern">
                </form>
            </section>
        </section>
    </body>
</html>
HTML;