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
                <li><a href="OpettajaTuutori.php" class="linkki">Opettajatuutori</a></li> 
                <li><a href="Ylituutori.php" class="linkki" id="valittu">Ylituutori</a></li> 
            </ul>
        </nav>

        <h2>YliTuutorin versio</h2>

        <?php
        $y_tiedot = "host=dbstud.sis.uta.fi port=5432 dbname=pk81541 user=pk81541 password=salasana";

        if (!$yhteys = pg_connect($y_tiedot)) {
            die("Tietokantayhteyden luominen epäonnistui.");
        }

        if (isset($_POST['Lisää'])) {
            $nimi = $_POST['T_nimi'];
            $hetu = $_POST['T_hetu'];
            $sposti = $_POST['T_sposti'];
            $sql = "INSERT INTO htiedot (htunnus, nimi, sähköposti) VALUES($hetu, $nimi, $sposti)";
            $tulos = pg_query($sql);
            if (pg_affected_rows($tulos) > 0) {
                $tuutoritunnus = pg_fetch_result(pg_query("SELECT MAX(tuutoritunnus) FROM tuutorit;"), 0) + 1;
                pg_query("INSERT INTO tuutorit (htunnus,tuutoritunnus) VALUES($hetu, $tuutoritunnus);");
                echo "<h1>Lisätty!</h1>";
            } else {
                echo "<h1>Tarkista Tiedot!</h1>";
            }
        }
        if (isset($_POST['Lähetä'])) {
            $onro = $_POST['Opiskelijanumero'];
            $tuutoritunnus = $_POST['Tuutorinumero'];
            $vuosi = pg_fetch_result(pg_query("select date_part('year',current_date)"), 0);
            $hae = pg_query("select ryhmänro from ryhmät where aloitusvuosi=$vuosi and tuutoritunnus=$tuutoritunnus;");
            if (pg_num_rows($hae) > 0) {
                $ryhmänro = $hae;
            } else {
                $ryhmänro = pg_fetch_result(pg_query("SELECT MAX(ryhmänro) FROM ryhmät;"), 0) + 1;
                pg_query("INSERT INTO ryhmät VALUES($ryhmänro, $tuutoritunnus, $vuosi);");
            }
            $sql = "update opiskelijat set ryhmänro = $ryhmänro where opiskelijanro = $onro";
            $tulos = pg_query($sql);
            if (pg_affected_rows($tulos) > 0) {
                echo "<h1>Onnistui!</h1>";
            } else {
                echo "<h1>Tarkista Tiedot!</h1>";
            }
        }
        ?>

        <h2>Tuutorit</h2>
        <table align = "center">
            <tr>
                <td>
                    Tuutorin nimi
                </td>
                <td>
                    Tuutorin tunnusnumero
                </td>
            </tr >

            <?php
            $hae = pg_query("select nimi, tuutoritunnus from htiedot natural join tuutorit order by tuutoritunnus;");
            while ($rivi = pg_fetch_array($hae)) {
                ?>
                <tr>
                    <td><?php echo $rivi['nimi'] ?></td>
                    <td><?php echo $rivi['tuutoritunnus'] ?></td>
                </tr>
                <?php
            }
            ?>
        </table>

        <form method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 
            <h2>Lisää uusi Tuutori</h2>
            Tuutorin nimi: <input type="text" class="palaute" name="T_nimi" required>
            Tuutorin henkilötunnus: <input type="text" class="palaute" name="T_hetu" required>
            Tuutorin sähköposti: <input type="text" class="palaute" name="T_sposti" required>
            <input type="submit" name="Lisää" value="Lisää"> 
        </form>

        <h2>Uudet opiskelijat joilla ei vielä ryhmää</h2>
        <table align = "center">
            <tr>
                <td>
                    Nimi
                </td>
                <td>
                    Opiskelijanumero
                </td>
            </tr >

            <?php
            $hae = pg_query("select nimi, opiskelijanro from htiedot natural join opiskelijat where ryhmänro is NULL and alkamisvuosi=(select date_part('year',current_date));");
            while ($rivi = pg_fetch_array($hae)) {
                ?>
                <tr>
                    <td><?php echo $rivi['nimi'] ?></td>
                    <td><?php echo $rivi['opiskelijanro'] ?></td>
                </tr>
                <?php
            }
            ?>
        </table>

        <form method="post" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>"> 
            <h2>Allokoi uudet oppilaat tuutoreille</h2>
            Opiskelijanumero: <input type="text" class="palaute" name="Opiskelijanumero" required>
            Tuutorinumero: <input type="text" class="palaute" name="Tuutorinumero" required>
            <input type="submit" name="Lähetä" value="Lähetä"> 
        </form>

        <h2>Tuutoreiden tuutoroitavien yhteenlasketut kokonaismäärät</h2>
        <table align = "center">
            <tr>
                <td>
                    Nimi
                </td>
                <td>
                    Lukumäärä
                </td>
            </tr >

            <?php
            $hae = pg_query("select nimi, count(*) as lkm
                             from htiedot
                             natural join tuutorit
                             natural join ryhmät r
                             inner join opiskelijat o
                             on r.ryhmänro=o.ryhmänro 
                             group by nimi;");
            while ($rivi = pg_fetch_array($hae)) {
                ?>
                <tr>
                    <td><?php echo $rivi['nimi'] ?></td>
                    <td><?php echo $rivi['lkm'] ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </body>
</html>