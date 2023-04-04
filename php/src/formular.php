<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Formulář</title>
    <link rel="stylesheet" href="formular.css">
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
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve the form data
            $name = $_POST["name"];
            $email = $_POST["email"];
            $message = $_POST["message"];

            // Get the highest number of existing XML files in the "zpravy" directory
            $highest_number = 0;
            $files = scandir("zpravy");
            foreach ($files as $file) {
                if (strpos($file, "zprava") === 0) {
                    $number = intval(substr($file, 6));
                    if ($number > $highest_number) {
                        $highest_number = $number;
                    }
                }
            }

            // Create a new XML file with the next incrementing number
            $filename = "zpravy/zprava" . ($highest_number + 1) . ".xml";
            $xml = new SimpleXMLElement("<zprava></zprava>");
            $xml->addChild("jmeno", $name);
            $xml->addChild("email", $email);
            $xml->addChild("zprava", $message);

            // XSL
            $xsl_dokument = new DOMDocument();
            $xsl_dokument->load("styly/formular.xsl");

            // XSLTtransformation
            $xsl_procesor = new XSLTProcessor();
            $xsl_procesor->importStylesheet($xsl_dokument);
            $transformovany_xml = $xsl_procesor->transformToDoc($xml);

            // ulozeni transformovaneho dokumentu
            $dir = 'zpravy/';
            $filename_prefix = 'html';
            $filename_suffix = 1;
            
            if ($handle = opendir($dir)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && strpos($entry, $filename_prefix) === 0) {
                        $current_suffix = intval(substr($entry, strlen($filename_prefix)));
                        if ($current_suffix >= $filename_suffix) {
                            $filename_suffix = $current_suffix + 1;
                        }
                    }
                }
                closedir($handle);
            }
            
            $nazev_dokumentu = $filename_prefix . $filename_suffix . '.html';

            $transformovany_xml->save("zpravy/" . $nazev_dokumentu );
        }
    ?>
        <form method="post">
            <h2>Napište nám zprávu</h1>
            <label for="name">Jméno:</label>
            <input type="text" id="name" name="name" placeholder="Vaše jméno..">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Váš email..">

            <label for="message">Zpráva:</label>
            <textarea id="message" name="message" placeholder="Napište nám zprávu.."></textarea>

            <button type="submit">Odeslat</button>
        </form>
        <ul class="zpravy-list">
        <?php
            $dir = "zpravy/";
            $files = glob($dir . "*.html");
            $links = array();
            foreach ($files as $file) {
                $name = basename($file, ".html");
                $links[] = "<li><a class='zprava' href='$file'>$name</a></li>";
            }
            foreach ($links as $link) {
                echo $link;
            }
        ?>
        </ul>
    </main>
    <footer>
        Copyright Matěj Kaška 1620
    </footer>
    </body>
</html>
