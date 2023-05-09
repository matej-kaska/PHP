<!DOCTYPE html>
<html>
<head>
    <title>PHP - Fakultonahrávač</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="background-image"></div>
    <nav class="navbar">
        <ul class="ul-navbar">
            <li class="li-navbar">
                <a class="nav-item" onclick="window.scrollTo(0, document.getElementById('Fakultonahrávač').offsetTop - 84)">Home</a>
                <a class="nav-item" onclick="window.scrollTo(0, document.getElementById('Nahrávač').offsetTop - 84)">Nahrávač</a>
                <a class="nav-item" onclick="window.scrollTo(0, document.getElementById('Soubory').offsetTop - 84)">Soubory</a>
                <a class="nav-item" onclick="window.scrollTo(0, document.getElementById('Formulář').offsetTop - 84)">Formulář</a>
                <a class="nav-item" onclick="window.scrollTo(0, document.getElementById('Účet').offsetTop - 84)">Účet</a>
            </li>
        </ul>
        <ul class="ul-account">
            <li class="li-account">
                <a class="login-button">Přihlásit se</a>
            </li>
        </ul>
    </nav>
    <main>
        <section>
            <header id="Fakultonahrávač" class="header">
                <h1 class="title">Fakultonahrávač</h1>
                <h2 class="title-name">Matěj Kaška</h2>
            </header>
        </section>
        <section class="section">
            <header id="Nahrávač" class="header-separator">
                <h1 class="header-of-section">Nahrávač</h1>
            </header>
            ss
        </section>
        <section class="section">
            <header id="Soubory" class="header-separator">
                <h1 class="header-of-section">Soubory</h1>
            </header>
            ss
        </section>
        <section class="section">
            <header id="Formulář" class="header-separator">
                <h1 class="header-of-section">Formulář</h1>
            </header>
            ss
        </section>
        <section class="section">
            <header id="Účet" class="header-separator">
                <h1 class="header-of-section">Účet</h1>
            </header>
            ss
        </section>
    </main>
    <form enctype="multipart/form-data" method="POST">
        <label for="fakulta">Kliknutím nahrajte recept ve validním XML souboru.</label>
        <br>
        <input type="file" name="xmlfile" data-max-file-size="2M"/>
        <br>
        <button type="submit">Validovat XML</button>
    </form>
    
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
    <div class="left-footer">Programování pro internet</div>
    <div class="right-footer">Matěj Kaška 2023</div>
</footer>
</body>
</html>