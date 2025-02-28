<?php
header('Content-Type: text/html; charset=utf-8');
echo <<<HTML
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Backer Seite</title>
    </head>
    <body>
        <header>
            <h1>Backer</h1>
            <h2>Bestellungen</h2>
        </header>
        <section class="firstOrder">
            <h3>Order No. 1</h3>
            <form method="get" action="https://echo.fbi.h-da.de/">
                <input type="radio" id="bestellt1" name="status" value="bestellt">
                <label for="bestellt1">Bestellt</label><br>
                
                <input type="radio" id="imOfen1" name="status" value="im ofen">
                <label for="imOfen1">Im Ofen</label><br>
                
                <input type="radio" id="fertig1" name="status" value="fertig">
                <label for="fertig1">Fertig</label><br>

                <button type="submit">Speichern</button>
            </form>
        </section>

        <section class="secondOrder">
            <h3>Order No. 2</h3>
            <form method="get" action="https://echo.fbi.h-da.de/">
                <input type="radio" id="bestellt2" name="status" value="bestellt">
                <label for="bestellt2">Bestellt</label><br>
                
                <input type="radio" id="imOfen2" name="status" value="im ofen">
                <label for="imOfen2">Im Ofen</label><br>
                
                <input type="radio" id="fertig2" name="status" value="fertig">
                <label for="fertig2">Fertig</label><br>
                
                <button type="submit">Speichern</button>
            </form>
        </section>

        <section class="thirdOrder">
            <h3>Order No. 3</h3>
            <form method="get" action="https://echo.fbi.h-da.de/">
                <input type="radio" id="bestellt3" name="status" value="bestellt">
                <label for="bestellt3">Bestellt</label><br>
                
                <input type="radio" id="imOfen3" name="status" value="im ofen">
                <label for="imOfen3">Im Ofen</label><br>
                
                <input type="radio" id="fertig3" name="status" value="fertig">
                <label for="fertig3">Fertig</label><br>

                <button type="submit">Speichern</button>
            </form>
        </section>
    </body>
</html>
HTML;
?>
