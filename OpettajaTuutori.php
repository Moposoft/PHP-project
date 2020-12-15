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
                <li><a href="Opiskelija.php" class="linkki" >Opiskelija</a></li> 
                <li><a href="OpettajaTuutori.php" class="linkki" id="valittu">Opettajatuutori</a></li> 
                <li><a href="Ylituutori.php" class="linkki" >Ylituutori</a></li> 
            </ul>
        </nav>

        <h2>OpettajaTuutorin versio</h2>
        <?php
        $y_tiedot = "host=dbstud.sis.uta.fi port=5432 dbname=pk81541 user=pk81541 password=salasana";
        $Tuutoritunnus = '';

        if (!$yhteys = pg_connect($y_tiedot)) {
            die("Tietokantayhteyden luominen epäonnistui.");
        }

        if (isset($_POST['Hae'])) {
            $testi = ($_POST['Tuutoritunnus']);
            $tulos = pg_query("SELECT * FROM opiskelijat WHERE opiskelijanro=$testi");

            if (pg_num_rows($tulos) > 0) {
                $_SESSION["Tuutoritunnus"] = ($_POST['Tuutoritunnus']);
                $Tuutoritunnus = $_SESSION["Tuutoritunnus"];
            } else {
                $_SESSION["Tuutoritunnus"] = NULL;
                echo '<h1>Tuutoritunnusta ei löytynyt</h1>';
            }
        }

        if (isset($_POST['Lähetä'])) {
            $Opiskelijanumero = $_POST['Opiskelijanumero'];
            $Lukuvuosi = $_POST['Lukuvuosi'];
            $Syksy = $_POST['Syksy'];
            $Kevät = $_POST['Kevät'];
            $Ryhmä1 = $_POST['Ryhmä1'];
            $Ryhmä2 = $_POST['Ryhmä2'];
            $Poisjäänti = $_POST['Poisjäänti'];

            if (isset($_POST['Lisää1'])) {

                $sql = "update opiskelijat set ryhmäpalaveri=$Ryhmä1, ryhmäpalaveri2=$Ryhmä2 where opiskelijanro=$Opiskelijanumero";
                $tulos = pg_query($sql);
                if (pg_affected_rows($tulos) > 0) {
                    echo '<h1>Tiedot päivitetty onnistuneesti!</h1>';
                } else {
                    echo '<h1>Tarkista tiedot!</h1>' . pg_last_error($yhteys);
                    return;
                }
            }

            if (isset($_POST['Lisää2'])) {

                $hae = pg_query("select * from osallistuminen where opiskelijanro=$Opiskelijanumero and lukuvuosi=$Lukuvuosi");
                if (pg_num_rows($hae) > 0) {
                    $sql = "update osallistuminen set poisjäänti=$Poisjäänti, syksy=$Syksy, kevät=$Kevät where opiskelijanro=$Opiskelijanumero and lukuvuosi=$Lukuvuosi";
                    $tulos = pg_query($sql);
                    if (pg_affected_rows($tulos) > 0) {
                        echo '<h1>Tiedot päivitetty onnistuneesti!</h1>';
                    } else {
                        echo '<h1>Tarkista tiedot!</h1>' . pg_last_error($yhteys);
                    }
                } else {
                    $sql = "Insert into osallistuminen values($Opiskelijanumero, $Lukuvuosi, $Syksy, $Kevät, $Poisjäänti);";
                    $tulos = pg_query($sql);
                    if (pg_affected_rows($tulos) > 0) {
                        echo '<h1>Tiedot päivitetty onnistuneesti!</h1>';
                    } else {
                        echo '<h1>Tarkista tiedot!</h1>' . pg_last_error($yhteys);
                    }
                }
            }
        }
        ?>

        <form method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 
            Tuutoritunnus: <input type="text" name="Tuutoritunnus" required value="<?php echo $Tuutoritunnus; ?>">
            <input type="submit" name="Hae" value="Hae"> 
        </form>

        <h2>Kolmannella vuodella olevien tuutoroitavien sähköpostiosoitteet.</h2>
        <table align = "center">
            <tr>
                <td>
                    Nimi
                </td>
                <td>
                    Sähköposti
                </td>
            </tr >

            <?php
            if (isset($_POST['Hae']) && isset($_SESSION["Tuutoritunnus"])) {
                $Tuutoritunnus = $_SESSION["Tuutoritunnus"];
                $hae = pg_query("select nimi, sähköposti 
                                 from opiskelijat
                                 natural join htiedot
                                 natural join ryhmät
                                 where tuutoritunnus=$Tuutoritunnus and alkamisvuosi=(
                                 select date_part('year',current_date)-3);");

                while ($rivi = pg_fetch_array($hae)) {
                    ?>
                    <tr>
                        <td><?php echo $rivi['nimi'] ?></td>
                        <td><?php echo $rivi['sähköposti'] ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>

        <h2>Päivitä opiskelijan osallistuminen</h2>
        <form method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 
            Opiskelijanumero: <input type="text" name="Opiskelijanumero" required><br>
            <input type="checkbox" name="Lisää1" value="on"> Lisää Osallistuminen Ryhmäpalaveriin: <br>
            Palaveri1:
            <select name="Ryhmä1">
                <option value="NULL">Ei vielä pidetty</option>
                <option value="'t'">Läsnä</option>
                <option value="'f'">Poissa</option>
            </select><br>
            Palaveri2:
            <select name="Ryhmä2">
                <option value="NULL">Ei vielä pidetty</option>
                <option value="'t'">Läsnä</option>
                <option value="'f'">Poissa</option>
            </select><br> 
            <input type="checkbox" name="Lisää2" value="on">Lisää Osallistuminen Henkilökohtaiseen Palaveriin:<br>
            Lukuvuosi: <input type="number" name="Lukuvuosi" min="1" max="3" value="1"><br>
            Syksy:
            <select name="Syksy">
                <option value="NULL">Ei vielä pidetty</option>
                <option value="'t'">Läsnä</option>
                <option value="'f'">Poissa</option>
            </select><br>
            Kevät:
            <select name="Kevät">
                <option value="NULL">Ei vielä pidetty</option>
                <option value="'t'">Läsnä</option>
                <option value="'f'">Poissa</option>
            </select><br> 
            <input type="checkbox">Lisää Poisjäännin syy:
            <select name="Poisjäänti">
                <option value="NULL">Ei poisjäänyt</option>
                <option value="1">Ei aloita opiskelua vielä koska töissä</option>
                <option value="2">Ei aloita vielä koska joku muu tutkinto kesken</option>
                <option value="3">Ei aio opiskella tätä pääaineena</option>
                <option value="4">Ei aio opiskella lainkaan</option>
                <option value="5">Ei tiedossa</option>
            </select><br> 
            <input type="submit" name="Lähetä" value="Lähetä">
        </form>

    </body>
</html>