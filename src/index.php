<?php
header("Content-type: text/html");

$Prak="";
$PrakInfo="";
if (is_dir("Praktikum")) {
    $PrakInfo="<li>Work on your lab exercises in the folder <b>Praktikum</b>.</li>";
    $Prak = "<a class=\"flex-item flex-item--warning\" href=\"./Praktikum\">Praktikum</a>";
}

$Exam="";
$ExamInfo="";
if (is_dir("Exam")) {
    $ExamInfo="<li><strong>Use the folder Exam (located in src/Exam) for your solution of the examination. Do not use any other folder!</strong></li>";
    $Exam = "<a class=\"flex-item flex-item--warning\" href=\"./Exam\">Klausur</a>";
}

echo <<<HTML
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>EWA Repository</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <main class="main-container">
            <div class="main-wrapper">
                <header>
                    <h1 class="h1">EWA Repository</h1>
                </header>
                <hr>
                <ul>
                    <li>This is the content of the file 'index.php' in the folder <b>'src'</b>!</li>
                    <li>For older examinations you may have a look at the folder <b>Klausuren</b></li>
                    <li>Many good examples are located in <b>Demos</b></li>
                    <li>Play around in the <b>Playground</b> folder</li>
                    $PrakInfo
                    <li>Use the folder <b>Exam_Probe</b> for our exam test run</li>
                    $ExamInfo
                </ul>
                <h2>Navigation</h2>
                <div class="flex-container">
                    $Prak
                    <a href="./Playground" class="flex-item">Spielwiese</a>
                </div>
                <div class="flex-container">
                    <a href="http://localhost/phpmyadmin" class="flex-item" target="_blank">Datenbanken</a>
                    <a href="https://lernen.h-da.de/course/view.php?id=18875" class="flex-item" target="_blank">Moodlekurs</a>
                </div>
                <div class="flex-container">
                    <a href="./Demos" class="flex-item">Demos</a>
                    <a href="./Klausuren" class="flex-item">Altklausuren</a>
                </div>
                <div class="flex-container">
                    <a href="./Exam_Probe" class="flex-item">Probeklausur</a>
                    $Exam
                </div>
            </div>
        </main>
    </body>
</html>
HTML;