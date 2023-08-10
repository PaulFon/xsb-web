<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Mass June 07 2020</title>

<!-- <link rel="stylesheet" type="text/css" href="./captionTranscript.css"> -->

</head>
<body>

<?php
    /*
    Name: Paul Fontaine
    Program Description: This program prints a mass text to be used for acapioning mass videos. User fills out infromation in captureMassInfo.php
    Date: 05/20/2020
    Version 1.3 06/03/2020


    if (isset($_POST['submit'])) { // Make sure that the form has been submitted
    	echo '<pre>';
    	print_r($_POST);
    	echo '</pre>';
    }

*/

?>
<link rel="stylesheet" type="text/css" href="./captionTranscript.css">
<p>Holy Redeemer Mass - <?php echo $_POST[massDate] ?> </p>
<h2> The Introductory Rites </h2>

    <p> Opening remarks</p>
    <p> ♫ <?php echo $_POST[entranceA]; ?> ♫ </p>
    <p> Greeting </p>
    <p> Penitential Act </p>
    <p> Kyrie </p>
    <p> ♫ Gloria ♫ </p>
    <p> Let us Pray. <?php readfile("../lectionary/Holy_Trinity_164_collect.html"); ?> </p>
    <p> <h2>The Liturgy of the Word</h2> </p>
    <p> A reading from </p>
    <p> <?php readfile("../lectionary/Holy_Trinity_164_r1.html"); ?> </p>
    <p> The Word of the Lord. R. Thanks be to God. </p>
    <p> ♫ <?php echo $_POST[respPsalm]; ?> ♫ </p>
    <p> A reading from </p>
    <p> <?php readfile("../lectionary/Holy_Trinity_164_r2.html"); ?></p>
    <p> The Word of the Lord. R. Thanks be to God.</p>

    <!-- <p> <?php readfile("../lectionary/Pentecost_sequence.html"); ?></p> -->

    <p> Gospel Acclamation</p>
    <p> ♫ Aleleluia <?php echo $_POST[gospelAccl]; ?> Aleleluia ♫ </p>
    <p> Dialogue at the Gospel</p>

    <p> The Lord be with you. R. and with your Spirit. </p>
    <p> A reading from the Gospel according to </p>
    <p> <?php readfile("../lectionary/Holy_Trinity_164_gospel.html"); ?> </p>
    <p> The Gospel of the Lord. R. Praise to you Lord Jesus Christ.</p>
    <p> Homily </p>
    <p> <?php echo " $_POST[homily]"; ?> </p>
    <p> <?php readfile("../Missal/Nicene_Creed.html"); ?> </p>
    <h3> Universal Prayer </h3>
    <p> <?php echo $_POST[universalPrayer]; ?> </p>
    <p> <h2>The Liturgy of the Eucharist</h2> </p>
    <h3> Offertory Hymn</h3>
    <p> ♫ <?php echo $_POST[offertoryHymn]; ?> ♫ </p>
    <h3> Presentation and Preparation of the Gifts </h3>
    <h3> Invitation to Prayer </h3>
    <h3> Prayer over the Offerings</h3>
    <h3> Preface Dialogue </h3>
    <p> <?php readfile("../lectionary/Holy_Trinity_preface.html"); ?> </p>
    <h3> Preface Acclamation</h3>
    <?php 
    switch ($_POST[whichEP]) {
        case EP1:
            readfile("../Missal/EP1.html"); 
            break;
        case EP2:
            readfile("../Missal/EP2.html"); 
            break;
        case EP3:
            readfile("../Missal/EP3.html"); 
            break;
        case none:
            echo "no Eucharistic prayer";
            break;
        default:
            echo "invalid Eucharistic prayer";
    }
    ?>

    <h3> Mystery of Faith</h3>
    <p> Concluding Doxology</p>
    <?php readfile("../Missal/CommunionRite.html"); ?> </p>
    <h3> Lamb of God</h3>
    <p> Sign of Peace</p>
    <p> Invitation to Communion</p>
    <p> The Communion</p>
    <p> <?php readfile("spiritualComunion.html"); ?> </p>
    <p> Prayer after Communion/Communion Ant.</p>
    <h2>The Concluding Rites</h2>
    <p> Final Blessing</p>
    <p> </p>
    <p> <?php /* readfile("../lectionary/easter_7_sunday_solemn_blessing.html");  */ ?> </p>
    <p> Dismissal</p>
    <p> <?php echo $_POST[recessional]; ?></p>

    </body>
</html>
