<!DOCTYPE html>
<html>
<head>
    <title>PHP - Fakultonahrávač</title>
    <link rel="stylesheet" href="index.css">
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
    <h1>Fakultonahrávač</h1>
    <form enctype="multipart/form-data" method="POST">
        <label for="fakulta">Kliknutím nahrajte recept ve validním XML souboru.</label>
        <br>
        <input type="file" name="xmlfile" data-max-file-size="2M"/>
        <br>
        <button type="submit">Validovat XML</button>
    </form>
    <div style="display: flex;">
        <img style="width: 35rem;" src = "svg/výkres.svg" alt="My Happy SVG"/>
    </div>
    
<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nahrany_xml = basename($_FILES['xmlfile']['name']);

        $xml = new DOMDocument;
        //$xml->load("XML.xml");
        $xml->load($nahrany_xml);
        if ($xml->schemaValidate('šablony/XSD.xsd')){

            echo '<p class="text-success">Nahraný soubor je validní a byl úspěšně nahrán do databáze.</p>';

            // XML
            $xml_dokument = new DOMDocument();
            $xml_dokument->load($nahrany_xml);

            // XSL
            $xsl_dokument = new DOMDocument();
            $xsl_dokument->load("styly/xsl.xsl");

            // XSLTtransformation
            $xsl_procesor = new XSLTProcessor();
            $xsl_procesor->importStylesheet($xsl_dokument);
            $transformovany_xml = $xsl_procesor->transformToDoc($xml_dokument);
            
            // ulozeni transformovaneho dokumentu
            $dir = 'dokumenty/';
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

            $transformovany_xml->save("dokumenty/" . $nazev_dokumentu );
        } else {
        echo '<p class="text-warning">Nahraný soubor není validní! Prosím zkontrolujte správnou strukturu.</p>';
        unlink($nahrany_recept);
        }
    }
?>
<footer>
Copyright Matěj Kaška 1620
</footer>
</body>
</html>