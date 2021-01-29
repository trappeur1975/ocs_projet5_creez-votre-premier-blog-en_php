<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title><?= $title ?></title>
        <!-- ISSUE PROBLEME DE CHEMIN -->
        <!--  LE CHEMIN EST GERER PAR RAPPORT AU FICHIER "index.php" par contre il faut indiquer le chemin absolu pour acceder au fichier "style.css" comme ci dessous en non en relatif comme  "<link href="css/style.css" rel="stylesheet" />" car sinon pour la route "http://localhost:8000/post/1" il va chercher le fichier "style.css" dans "GET /post/css/style.css" (info sortir de la console terminal de visual studio code) ce qui genere une erreur 4040-->
        <link href="/css/style.css" rel="stylesheet" />
    </head>
    
    <body>
        <h1>hello3</h1>
        <?= $content ?>
    </body>
</html>