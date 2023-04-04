<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - HTML soubory</title>
    <link rel="stylesheet" href="skola.css">
</head>
<body>
    <header class="navbar">
        <nav>
            <ul class="ul-no-dots">
                <li>
                    <a class="nav-item" href="index.php">Home</a>
                    <a class="nav-item" href="skola.php">HTML files</a>
                    <a class="nav-item" href="formular.php">Formular</a>
                </li>
            </ul>
        </nav>
    </header>
    <main>
    <?php
        $dir = "dokumenty/";
        $files = glob($dir . "*.html");
        $links = array();
        foreach ($files as $file) {
            $name = basename($file, ".html");
            $links[] = "<li><a href='$file'>$name</a></li>";
        }
        foreach ($links as $link) {
            echo $link;
        }
    ?>
    </main>
    <footer>
        Copyright Matěj Kaška 1620
    </footer>
</body>
</html>
