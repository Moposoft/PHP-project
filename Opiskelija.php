<?php
session_start();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>TIKO</title>
        <link rel="stylesheet" href="tyyli.css" media="screen" /> 
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Brawler"/>
    </head>

    <body>

        <nav>
            <ul> 
                <li><a href="Opiskelija.php" class="linkki" id="valittu">Opiskelija</a></li> 
                <li><a href="OpettajaTuutori.php" class="linkki">Opettajatuutori</a></li> 
                <li><a href="Ylituutori.php" class="linkki" >Ylituutori</a></li> 
            </ul>
        </nav>

        <h2>Opiskelijan versio</h2>
        <?php
        $y_tiedot = "host=dbstud.sis.uta.fi port=5432 dbname=pk81541 user=pk81541 password=salasana";

        if (!$yhteys = pg_connect($y_tiedot)) {
            die("Tietokantayhteyden luominen epäonnistui.");
        }

        if (isset($_POST['Hae'])) {
            $testi = ($_POST['Opiskelijanumero']);
            $tulos = pg_query("SELECT * FROM opiskelijat WHERE opiskelijanro=$testi");

            if (pg_num_rows($tulos) > 0) {
                $_SESSION["Opiskelijanumero"] = ($_POST['Opiskelijanumero']);
                $Opiskelijanumero = $_SESSION["Opiskelijanumero"];
                $Osoite = pg_fetch_result(pg_query("SELECT osoite FROM htiedot natural join opiskelijat WHERE opiskelijanro=$Opiskelijanumero"), 0);
                $Puhelinnumero = pg_fetch_result(pg_query("SELECT puhelinnro FROM htiedot natural join opiskelijat WHERE opiskelijanro=$Opiskelijanumero"), 0);
                $Sähköposti = pg_fetch_result(pg_query("SELECT sähköposti FROM htiedot natural join opiskelijat WHERE opiskelijanro=$Opiskelijanumero"), 0);
            } else {
                $_SESSION["Opiskelijanumero"] = NULL;
                echo '<h1>Opiskelijanumeroa ei löytynyt</h1>';
            }
        }

        if (isset($_POST['Muuta']) && isset($_SESSION["Opiskelijanumero"])) {
            $Opiskelijanumero = $_SESSION["Opiskelijanumero"];

            $tulos = pg_query("SELECT * FROM opiskelijat WHERE opiskelijanro=$Opiskelijanumero");
            if (pg_num_rows($tulos) > 0) {
                $Osoite = ($_POST['Osoite']);
                $Puhelinnumero = ($_POST['Puhelinnumero']);
                $Sähköposti = ($_POST['Sähköposti']);
                $kysely = "UPDATE htiedot ht SET osoite='$Osoite' FROM opiskelijat o WHERE ht.htunnus=o.htunnus AND opiskelijanro=$Opiskelijanumero;";
                $kysely .="UPDATE htiedot ht SET puhelinnro='$Puhelinnumero' FROM opiskelijat o WHERE ht.htunnus=o.htunnus AND opiskelijanro=$Opiskelijanumero;";
                $kysely .="UPDATE htiedot ht SET sähköposti='$Sähköposti' FROM opiskelijat o WHERE ht.htunnus=o.htunnus AND opiskelijanro=$Opiskelijanumero;";
                $paivitys = pg_query($kysely);

                if ($paivitys && (pg_affected_rows($paivitys) > 0)) {
                    echo '<h1>Tietojen päivitys onnistui</h1>';
                } else {
                    echo '<h1>Tietojen päivitys epäonnistui</h1>' . pg_last_error($yhteys);
                }
            } else {
                echo '<h1>Opiskelijanumeroa ei löytynyt</h1>';
            }
        }

        if (isset($_POST['HaeHops']) && isset($_SESSION['Opiskelijanumero'])) {
            $Opiskelijanumero = $_SESSION['Opiskelijanumero'];
            $_SESSION['Lukuvuosi'] = intval($_POST['Lukuvuosi']);
            $Lukuvuosi = $_SESSION['Lukuvuosi'];

            $hae = pg_query("SELECT * FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero");
            if (pg_num_rows($hae) > 0) {
                $Töissä = pg_fetch_result(pg_query("SELECT töissä FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $TyönKuvaus = pg_fetch_result(pg_query("SELECT työn_kuvaus FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $TyönTuntimäärä = pg_fetch_result(pg_query("SELECT työn_tuntimäärä FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $Perustelut = pg_fetch_result(pg_query("SELECT perustelut FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $SuoritetutOp = pg_fetch_result(pg_query("SELECT suoritetut_op FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $PääaineOp = pg_fetch_result(pg_query("SELECT pääaine_op FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $MuutOp = pg_fetch_result(pg_query("SELECT muut_op FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $Katsaus1 = pg_fetch_result(pg_query("SELECT katsaus1 FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $Katsaus2 = pg_fetch_result(pg_query("SELECT katsaus2 FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $Katsaus3 = pg_fetch_result(pg_query("SELECT katsaus3 FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $Katsaus4 = pg_fetch_result(pg_query("SELECT katsaus4 FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
                $Tuutori = pg_fetch_result(pg_query("SELECT opettajatuutori FROM hops WHERE lukuvuosi=$Lukuvuosi AND opiskelijanro=$Opiskelijanumero"), 0);
            } else {
                echo "<h1>HOPS:ia ei ole vielä täytetty!</h1>";
            }
        }

        if (isset($_POST['Lähetä']) && isset($_SESSION["Opiskelijanumero"]) && isset($_SESSION["Lukuvuosi"])) {
            $Opiskelijanumero = $_SESSION["Opiskelijanumero"];
            $Lukuvuosi = $_SESSION["Lukuvuosi"];
            if (isset($_POST['nimi'])) {
                foreach (array_keys($_POST['nimi']) as $key) {
                    $OpNimi = $_POST['nimi'][$key];
                    $OpAine = $_POST['aine'][$key];
                    $OpPist = $_POST['pisteet'][$key];
                    $OpLuk = $_POST['lukukausi'][$key];

                    $tarkista = pg_query("SELECT * FROM hops_opintojaksot where opiskelijanro = '$Opiskelijanumero' AND lukuvuosi=$Lukuvuosi AND opintojakson_nimi = '$OpNimi';");
                    if (pg_num_rows($tarkista) > 0) {
                        echo "<h1>Kurssi on jo lisätty!</h1>";
                    } else {
                        pg_query("INSERT INTO hops_opintojaksot (opiskelijanro,lukuvuosi,opintojakson_nimi,opintopisteitä,oppiaine,lukukausi) VALUES($Opiskelijanumero,$Lukuvuosi,'$OpNimi',$OpPist,'$OpAine','$OpLuk')");
                    }
                }
            }
            if (isset($_POST['Töissä'])) {
                $Töissä = t;
            } else {
                $Töissä = f;
            }
            $TyönKuvaus = ($_POST['TyönKuva']);
            $TyönTuntimäärä = ($_POST['Tuntimäärä']);
            $Perustelut = ($_POST['Perustelu']);
            $SuoritetutOp = ($_POST['SuoritetutOp']);
            $PääaineOp = ($_POST['PääaineOp']);
            $MuutOp = ($_POST['MuutOp']);
            $Katsaus1 = ($_POST['Katsaus1']);
            $Katsaus2 = ($_POST['Katsaus2']);
            $Katsaus3 = ($_POST['Katsaus3']);
            $Katsaus4 = ($_POST['Katsaus4']);
            $Tuutori = ($_POST['Tuutori']);
            $kysely = "INSERT INTO hops values($Opiskelijanumero, $Lukuvuosi, '$Tuutori', '$Töissä', '$TyönKuvaus', $TyönTuntimäärä, '$Perustelut', $SuoritetutOp, $PääaineOp, $MuutOp, '$Katsaus1', '$Katsaus2', '$Katsaus3', '$Katsaus4')";
            $paivitys = pg_query($kysely);
            if ($paivitys && (pg_affected_rows($paivitys) > 0)) {
                echo '<h1>Tietojen päivitys onnistui</h1>';
            } else {
                echo '<h1>Tietojen päivitys epäonnistui</h1>' . pg_last_error($yhteys);
            }
        }
        ?>

        <form method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 
            Opiskelijanumero: <input type="text" name="Opiskelijanumero" required value="<?php echo $Opiskelijanumero; ?>">
            <input type="submit" name="Hae" value="Hae"> 
        </form>

        <div id="div1">
            <form id="palautelomake2" method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 

                <h2>YHTEYSTIEDOT</h2>
                <label>Osoite:
                    <input type="text" class="palaute" name="Osoite" value="<?php echo $Osoite; ?>"/>
                </label>
                <label>Puhelinnumero(t):
                    <input type="tel" class="palaute" name="Puhelinnumero" value="<?php echo $Puhelinnumero; ?>"/>
                </label>
                <label>Sähköpostiosoite:
                    <input type="text" class="palaute" name="Sähköposti" value="<?php echo $Sähköposti; ?>"/>
                </label>
                <input value="Muuta" type="submit" name="Muuta"/>

            </form>
        </div>

        <h2>Yhteenveto oppilaan saamista pisteistä</h2>

        <table id="optaulu" align="center">
            <tr>
                <td>
                    Kurssi:
                </td>
                <td>
                    Opintopisteitä
                </td>
                <td>
                    Lukukausi
                </td>
                <td>
                    Lukuvuosi
                </td>
            </tr>
            <?php
            if (isset($_POST['Hae']) && isset($_SESSION["Opiskelijanumero"])) {
                $Opiskelijanumero = $_SESSION["Opiskelijanumero"];
                $hae = pg_query("select kurssinimi, lukuvuosi, lukukausi, opintopisteitä from suoritukset where opiskelijanro=$Opiskelijanumero;");
                while ($rivi = pg_fetch_array($hae)) {
                    ?>
                    <tr>
                        <td><?php echo $rivi['kurssinimi'] ?></td>
                        <td><?php echo $rivi['opintopisteitä'] ?></td>
                        <td><?php echo $rivi['lukukausi'] ?></td>
                        <td><?php echo $rivi['lukuvuosi'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>

        <h2>HOPS-kyselyt annetulle lukuvuodelle. </h2>

        <form method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 
            Anna Lukuvuosi(1-3): 
            <input type="number" name="Lukuvuosi" min="1" max="3" value="<?php
            if (isset($Lukuvuosi)) {
                echo $Lukuvuosi;
            } else
                echo '1';
            ?>" required/>
            <input type="submit" name="HaeHops" value="Hae"> 
        </form>

        <form id="palautelomake" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">

            <h2>Liisää suoritteva kurssi suunnitelmiin</h2>

            Opintojakson nimi:
            <input type="text" class="palaute" name="OpNimi" id="s1" value="" />
            Oppiaine:
            <input type="text" class="palaute" name="Oppiaine" id="s2" value=""/>
            Opintopisteitä:
            <input type="number" class="palaute" name="Op" id="s3" min="0" value="0"/>
            Lukukausi:<br>
            <input type="radio" name="Lukukausi" id="r1" value="Syksy" checked="checked"> Syksy<br>
            <input type="radio" name="Lukukausi" id="r2" value="Kevät"> Kevät<br>

            <input type="button" value="Lisää" onclick="lisääRivi()" />

            <h2>Opintosuunnitelmani</h2>

            <table id="taulu">
                <tr>
                    <td>
                        Oppijakson Nimi:
                    </td>
                    <td>
                        Oppiaine
                    </td>
                    <td>
                        Opintopisteitä
                    </td>
                    <td>
                        Lukukausi
                    </td>
                </tr>
                <?php
                if (isset($_POST['HaeHops']) && isset($_SESSION["Opiskelijanumero"])) {
                    $Lukuvuosi = $_SESSION['Lukuvuosi'];
                    $Opiskelijanumero = $_SESSION["Opiskelijanumero"];
                    $hae = pg_query("select * from hops_opintojaksot where lukuvuosi=$Lukuvuosi and opiskelijanro=$Opiskelijanumero;");
                    while ($rivi = pg_fetch_array($hae)) {
                        ?>
                        <tr>
                            <td><?php echo $rivi['opintojakson_nimi'] ?></td>
                            <td><?php echo $rivi['oppiaine'] ?></td>
                            <td><?php echo $rivi['opintopisteitä'] ?></td>
                            <td><?php echo $rivi['lukukausi'] ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>

            <script>
                function lisääRivi() {
                    var taulu = document.getElementById('taulu');
                    var lukukausi;
                    if (document.getElementById('r1').checked) {
                        lukukausi = document.getElementById('r1').value;
                    }
                    if (document.getElementById('r2').checked) {
                        lukukausi = document.getElementById('r2').value;
                    }

                    var s1 = document.getElementById('s1').value;
                    var s2 = document.getElementById('s2').value;
                    var s3 = document.getElementById('s3').value;

                    var tr = document.createElement('tr');
                    var td1 = document.createElement('td');
                    var td2 = document.createElement('td');
                    var td3 = document.createElement('td');
                    var td4 = document.createElement('td');

                    var in1 = document.createElement('input');
                    in1.setAttribute('type', 'hidden');
                    in1.setAttribute('name', 'nimi[]');
                    in1.setAttribute('value', s1);
                    var in2 = document.createElement('input');
                    in2.setAttribute('type', 'hidden');
                    in2.setAttribute('name', 'aine[]');
                    in2.setAttribute('value', s2);
                    var in3 = document.createElement('input');
                    in3.setAttribute('type', 'hidden');
                    in3.setAttribute('name', 'pisteet[]');
                    in3.setAttribute('value', s3);
                    var in4 = document.createElement('input');
                    in4.setAttribute('type', 'hidden');
                    in4.setAttribute('name', 'lukukausi[]');
                    in4.setAttribute('value', lukukausi);

                    var a1 = document.createTextNode(s1);
                    var a2 = document.createTextNode(s2);
                    var a3 = document.createTextNode(s3);
                    var a4 = document.createTextNode(lukukausi);

                    td1.appendChild(in1);
                    td2.appendChild(in2);
                    td3.appendChild(in3);
                    td4.appendChild(in4);
                    td1.appendChild(a1);
                    td2.appendChild(a2);
                    td3.appendChild(a3);
                    td4.appendChild(a4);
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td3);
                    tr.appendChild(td4);
                    taulu.appendChild(tr);
                }
                function poistaRivi() {
                    var rivit = document.getElementById('taulu').rows.length;
                    if (rivit > 1) {
                        document.getElementById('taulu').deleteRow(-1);
                    }
                }
            </script>

            <input type="button" value="Poista viimeisin" onclick="poistaRivi()" />
            <br>

            <input type="checkbox" name="Töissä" value="Töissä" <?php echo ($Töissä == 't' ? 'checked' : '') ?> >Olen töissä lukukauden aikana.<br>
            <label>(1) Työn kuva on :
                <input type="text" class="palaute" name="TyönKuva" value="<?php echo $TyönKuvaus; ?>" />
            </label>
            <label>(2) Työn määrä (kokopäivätyö / osa-aikatyö tuntimäärä viikossa):
                <input type="number" class="palaute" min="0" name="Tuntimäärä" value="<?php echo $TyönTuntimäärä; ?>"/>
            </label>
            <label>Perusteluni sille, että olen / en ole töissä:
                <textarea name="Perustelu" rows="5" placeholder="<?php echo $Perustelu; ?>">></textarea>
            </label>

            <h2>Katsaus Viime Opiskeluvuoteen</h2>
            <label>Sain kerättyä opintopisteitä yhteensä:
                <input type="number" class="palaute"  min="0" name="SuoritetutOp" value="<?php echo $SuoritetutOp; ?>" />
            </label>
            <label>Tietojenkäsittelytieteitä:
                <input type="number" class="palaute"  min="0" name="PääaineOp" value="<?php echo $PääaineOp; ?>" />
            </label>
            <label>Muita:
                <input type="number" class="palaute"  min="0" name="MuutOp" value="<?php echo $MuutOp; ?>" />
            </label>

            <label>Viime vuoden hyviä asioita olivat:
                <textarea name="Katsaus1" rows="5" placeholder="<?php echo $Katsaus1; ?>">></textarea>
            </label>
            <label>Viime vuonna seuraavat asiat eivät sujuneet odotusteni mukaisesti:
                <textarea name="Katsaus2" rows="5" placeholder="<?php echo $Katsaus2; ?>">></textarea>
            </label>
            <label>Tietojenkäsittelytieteissä minua tällä hetkellä kiinnostavat erityisesti seuraavat aiheet ja/tai alueet:
                <textarea name="Katsaus3" rows="5" placeholder="<?php echo $Katsaus3; ?>">></textarea>
            </label>
            <label>Valinnaisina opintoina minua kiinnostavat erityisesti seuraavat aineet:
                <textarea name="Katsaus4" rows="5" placeholder="<?php echo $Katsaus4; ?>"></textarea>
            </label>
            <label>OPETTAJATUUTORISI viime lukuvuonna:
                <input type="text" class="palaute" name="Tuutori" required value="<?php echo $Tuutori; ?>"/>
            </label>
            <input value="Lähetä" type="submit" name="Lähetä" />

        </form>
    </body>
</html>