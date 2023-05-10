<?php
    //libxml_use_internal_errors(true);
    session_start();

    if(@$_SESSION["email"]) {
        $loggedin = true;
    }

    function dtdvalidation($pass) {
        if ($pass) {
            echo '<img src="static/pass.svg" class="validation question-mark">';
        } else {
            echo '<img src="static/fail.svg" class="validation question-mark">';
        }
    }

    function xsdvalidation($pass) {
        if ($pass) {
            echo '<img src="static/pass.svg" class="validation question-mark">';
        } else {
            echo '<img src="static/fail.svg" class="validation question-mark">';
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (@$_POST['formName'] === 'file') {
            $nahrany_xml = $_FILES['xmlfile']['name'];
            $puvodni_xml = new DOMDocument;
            $puvodni_xml->load($nahrany_xml);
            $koren = 'school';
            $generator_dokumentu = new DOMImplementation;
            $doctype = $generator_dokumentu->createDocumentType($koren, "", 'sablony/DTD.dtd');
            $novy_xml = $generator_dokumentu->createDocument(null, "", $doctype);
            $novy_xml->encoding = "utf-8";
            $puvodni_uzel = $puvodni_xml->getElementsByTagName($koren)->item(0);
            $novy_uzel = $novy_xml->importNode($puvodni_uzel, true);
            $novy_xml->appendChild($novy_uzel);
            file_put_contents('xml-dtd.xml', $novy_xml->saveXML());

            if (@$novy_xml->validate()) {
                $dtd_validation_result = true;
            } else {
                $dtd_validation_result = false;
            }

            if (@$puvodni_xml->schemaValidate('sablony/XSD.xsd')){
                $xsd_validation_result = true;

                // XSLT
                $xml_dokument = new DOMDocument();
                $xml_dokument->load($nahrany_xml);
                $xsl_dokument = new DOMDocument();
                $xsl_dokument->load("styly/xsl.xsl");
                $xsl_procesor = new XSLTProcessor();
                $xsl_procesor->importStylesheet($xsl_dokument);
                $transformovany_xml = $xsl_procesor->transformToDoc($xml_dokument);
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
                $xsd_validation_result = false;
            }
        }
        else if (@$_POST['formName'] === 'xml') {
            $xml = simplexml_load_file('XML.xml');

            foreach ($xml->faculties->faculty->katedry->katedra as $katedra) {
                if ($katedra->k_name == $_POST['xml-katedry']) {
                    if ($_POST['xml-pozice'] === 'Student') {
                        $students = $katedra->students;
                        break;
                    } else {
                        $teachers = $katedra->teachers;
                        break;
                    }
                }
            }

            if ($_POST['xml-pozice'] === 'Student') {
                $new_student = $students->addChild('student');
                $new_student->s_name = $_POST['xml-name'];
                $new_student->st = $_POST['xml-st'];
                $new_student->s_email = $_POST['xml-email'];
            } else {
                $new_teacher = $teachers->addChild('teacher');
                $new_teacher->t_name = $_POST['xml-name'];
                $new_teacher->t_telefon = $_POST['xml-st'];
                $new_teacher->pozice = $_POST['xml-email'];
            }

            $xml->asXML('XML.xml');
        } else if (@$_POST['formName'] === 'login') {
            $con = mysqli_connect("db:3306","administrator","heslo","db");

            if (mysqli_connect_errno()) {
                echo '<script type="text/javascript">',"window.addEventListener('load', function() {window.alert('Failed to connect to MySQL: '" . mysqli_connect_error() . " !');});",'</script>';
                exit();
            }

            $email = $_POST['email'];
            $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result !== false) {
                $row = mysqli_fetch_assoc($result);
                $password = hash('sha256', $_POST['password']);
                if ($password === $row['password']) {
                    $loggedin = true;
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["name"] = $row['name'];
                } else {
                    echo '<script type="text/javascript">',"window.addEventListener('load', function() {window.alert('Špatné heslo!');});",'</script>';
                }
                mysqli_free_result($result);
            } else {
                echo '<script type="text/javascript">',"window.addEventListener('load', function() {window.alert('Tento uživatel neexistuje!');});",'</script>';
            }

            
            mysqli_close($con);
        } else if (@$_POST['formName'] === 'register') {
            $email = $_POST['email'];
            $name = $_POST['name'];
            $password = $_POST['password'];

            if ($password !== "" && $name !== "" && $email !== "") {
                $con = mysqli_connect("db:3306","administrator","heslo","db");

                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                    exit();
                }

                $stmt = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) === 0) {
                    $password = hash('sha256', $password);
                    $stmt = mysqli_prepare($con, "INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "sss", $email, $name, $password);
    
                    if (mysqli_stmt_execute($stmt)) {
                        // SUCCESS
                    } else {
                        echo '<script type="text/javascript">',"window.addEventListener('load', function() {window.alert('Error creating new user: '" . mysqli_error($con) . " !');});",'</script>';
                    }

                } else {
                    echo '<script type="text/javascript">',"window.addEventListener('load', function() {window.alert('Tento e-mail je již používán!');});",'</script>';
                }

                mysqli_stmt_close($stmt);
                mysqli_close($con);
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>PHP - Fakultonahrávač</title>
        <link rel="stylesheet" href="index.css">
        <script src="static/script.js"></script>
    </head>
    <body>
        <div class="background-image"></div>
        <nav class="navbar">
            <ul class="ul-navbar">
                <li class="li-navbar">
                    <a class="nav-item text" onclick="window.scrollTo(0, document.getElementById('Fakultonahrávač').offsetTop - 84)">Home</a>
                    <a class="nav-item text" onclick="window.scrollTo(0, document.getElementById('Nahrávač').offsetTop - 84)">Nahrávač</a>
                    <a class="nav-item text" onclick="window.scrollTo(0, document.getElementById('Soubory').offsetTop - 84)">Soubory</a>
                    <a class="nav-item text" onclick="window.scrollTo(0, document.getElementById('Formulář').offsetTop - 84)">Formulář</a>
                    <a class="nav-item text" onclick="window.scrollTo(0, document.getElementById('Účet').offsetTop - 84)">Účet</a>
                </li>
            </ul>
            <?php
                if (isset($loggedin)) {
                    echo '<ul class="ul-account logout" onclick="window.location.href=\'logout.php\'">
                            <li class="li-account">
                                <a class="login-button text">Odhlásit se</a>
                            </li>
                        </ul>';
                } else {
                    echo '<ul class="ul-account" onclick="modalLogin(true)">
                            <li class="li-account">
                                <a class="login-button text">Přihlásit se</a>
                            </li>
                        </ul>';
                }
            ?>
        </nav>
        <main>
            <section>
                <header id="Fakultonahrávač" class="header">
                    <h1 class="title text">Fakultonahrávač</h1>
                    <h2 class="title-name text">Matěj Kaška</h2>
                </header>
            </section>
            <section class="section">
                <header id="Nahrávač" class="header-separator">
                    <h1 class="header-of-section text">Nahrávač</h1>
                </header>
                <div class="blobs-container">
                    <div class="blob">
                        <h2 class="text blob-title">Nahrajte XML Soubor</h2>
                        <form enctype="multipart/form-data" method="POST" id="file">
                            <label class="upload text">
                                <input type="hidden" name="formName" value="file" />
                                <input type="file" name="xmlfile" data-max-file-size="2M" onchange="document.getElementById('file').submit();"/>
                                Vyberte soubor
                            </label>
                        </form>
                    </div>
                    <div class="blob">
                        <h2 class="text blob-title">Validace XML</h2>
                        <div class="row">
                            <?php
                                if (isset($dtd_validation_result)) {
                                    dtdvalidation($dtd_validation_result);
                                } else {
                                    echo '<img src="static/question-mark.svg" class="validation question-mark">';
                                }
                            ?>
                            <a class="text pointer" onclick="window.location.href='sablony/DTD.html'">DTD Validace</a>
                        </div>
                        <div class="row">
                            <?php
                                if (isset($xsd_validation_result)) {
                                    xsdvalidation($xsd_validation_result);
                                } else {
                                    echo '<img src="static/question-mark.svg" class="validation question-mark">';
                                }
                            ?>
                            <a class="text pointer" onclick="window.location.href='sablony/XSD.html'">XSD Validace</a>
                        </div>
                    </div>
                </div>
            </section>
            <section class="section">
                <header id="Soubory" class="header-separator">
                    <h1 class="header-of-section text">Soubory</h1>
                </header>
                <div class="blobs-container">
                    <div class="blob">
                        <h2 class="text blob-title">Vyberte stránku</h2>
                        <select onDblClick="window.location.href=this.value" class="html-select text" name="htmls" id="htmls" size="10">
                            <?php
                                $dir = "dokumenty/";
                                $files = glob($dir . "*.html");
                                $links = array();
                                foreach ($files as $file) {
                                    $name = basename($file, ".html");
                                    $links[] = "<option value='$file'>$name</option>";
                                }
                                foreach ($links as $link) {
                                    echo $link;
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </section>
            <section class="section">
                <header id="Formulář" class="header-separator">
                    <h1 class="header-of-section text">Formulář</h1>
                </header>
                <div class="blobs-container">
                    <form enctype="multipart/form-data" method="POST" id="xml">
                        <div class="blob">
                            <h2 class="text blob-title">Přidejte studenta do XML</h2>
                            <div class="row form-container">
                                <div class="column">
                                    <label class="form-label text">Pozice</label>
                                    <label class="form-label text">Jméno a příjmení</label>
                                    <label class="form-label text" id="st">ST kód</label>
                                    <label class="form-label text" id="email">Email</label>
                                    <label class="form-label text">Katedra</label>
                                </div>
                                <div class="column">
                                    <select class="form-input text select-form" id="xml-pozice" name="xml-pozice" onchange="formXML(this.value)">
                                        <option value="Student">Student</option>
                                        <option value="Vyučující">Vyučující</option>
                                    </select>
                                    <input class="form-input text" name="xml-name" spellcheck="false" type="text">
                                    <input class="form-input text" name="xml-st" spellcheck="false" type="text">
                                    <input class="form-input text" name="xml-email" spellcheck="false" type="text">
                                    <select class="form-input text select-form" name="xml-katedry">
                                        <option value="Katedra biologie">Katedra biologie</option>
                                        <option value="Katedra fyziky">Katedra fyziky</option>
                                        <option value="Katedra geografie">Katedra geografie</option>
                                        <option value="Katedra chemie">Katedra chemie</option>
                                        <option value="Katedra chemie">Katedra matematiky</option>
                                        <option value="Katedra chemie">Katedra informatiky</option>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="formName" value="xml" />
                            <button type="submit" class="form-button text">Přidat</button>
                        </div>
                    </form>
                </div>
            </section>
            <section class="section">
                <header id="Účet" class="header-separator">
                    <h1 class="header-of-section text">Účet</h1>
                </header>
                <div class="blobs-container">
                    <div class="blob">
                        <h2 class="text blob-title">Přihlášený uživatel</h2>
                        <div class="row form-container">
                            <div class="column account-column">
                                <label class="text">Jméno:</label>
                                <label class="text">Email:</label>
                            </div>
                            <div class="column account-column">
                                <label class="text">
                                    <?php
                                    if (@$loggedin === true) {
                                        echo $_SESSION["name"];
                                    } else {
                                        echo 'Uživatel není přihlášen!';
                                    }
                                    ?>
                                </label>
                                <label class="text">
                                <?php
                                    if (@$loggedin === true) {
                                        echo $_SESSION["email"];
                                    } else {
                                        echo 'Uživatel není přihlášen!';
                                    }
                                    ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <aside id="modalLogin" class="modal">
                <section class="modal-container">
                    <form enctype="multipart/form-data" method="POST" id="login">
                        <header class="login-header">
                            <h1 class="text login-h1">Přihlásit se</h1>
                            <img src="static/close.svg" onclick="modalLogin(false)" class="close validation question-mark">
                        </header>
                        <div class="row form-container">
                            <div class="column account-column">
                                <label class="text modal-label">Email:</label>
                                <label class="text modal-label">Heslo:</label>
                            </div>
                            <div class="column account-column">
                                <input class="form-input text" required name="email" spellcheck="false" type="email">
                                <input class="form-input text" required name="password" spellcheck="false" type="password">
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" name="formName" value="login" />
                            <button type="submit" class="modal-buttons form-button text secondary" onclick="modalRegister(true)">Zaregistrovat se</button>
                            <button type="submit" class="modal-buttons form-button text">Přihlásit se</button>
                        </div>
                    </form>
                </section>
            </aside>
            <aside id="modalRegister" class="modal">
                <section class="modal-container">
                    <form enctype="multipart/form-data" method="POST" id="register">
                        <header class="login-header">
                            <h1 class="text login-h1">Zaregistruj se</h1>
                            <img src="static/close.svg" onclick="modalRegister(false)" class="close validation question-mark">
                        </header>
                        <div class="row form-container">
                            <div class="column account-column">
                                <label class="text modal-label">Email:</label>
                                <label class="text modal-label">Jméno:</label>
                                <label class="text modal-label">Heslo:</label>
                            </div>
                            <div class="column account-column">
                                <input class="form-input text" required name="email" spellcheck="false" type="email">
                                <input class="form-input text" required name="name" spellcheck="false" type="text">
                                <input class="form-input text" required name="password" spellcheck="false" type="password">
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" name="formName" value="register" />
                            <button type="submit" class="modal-buttons form-button text secondary" onclick="modalLogin(true)">Přihlásit se</button>
                            <button type="submit" class="modal-buttons form-button text">Zaregistrovat se</button>
                        </div>
                    </form>
                </section>
            </aside>
        </main>    
        <footer>
            <div class="left-footer">Programování pro internet</div>
            <div class="right-footer">Matěj Kaška 2023</div>
        </footer>
    </body>
</html>