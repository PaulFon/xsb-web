<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assemble various texts for the Mass transcript</title>
<link rel="stylesheet" type="text/css" href="./captionTranscript.css">

</head>
<body>

<?php
    /*
    Name: Paul Fontaine
    Program Description: This program prints a mass text to be used for acapioning mass videos. User fills out infromation in captureMassInfo.php
    Date: 05/20/2020
    Version 1.5 07/25/2020

    if (isset($_POST['submit'])) { // Make sure that the form has been submitted
    	echo '<pre>';
    	print_r($_POST);
    	echo '</pre>';
    }
*/
// 2020-07-05 - Sean Bright
// date formats: https://www.php.net/manual/en/function.date.php
require_once 'readingsVersionA.inc';  // calls php to get the readings from USCCB

$massDate = new DateTime($_POST['massDate']);
$filenamePrefix = get_the_readings($massDate->format('mdy'));
?>

<p>Holy Redeemer Mass - <?php echo $_POST['massDate'] ?> </p>
<h2> The Introductory Rites </h2>
<?php   // check if the entrance antiphon is sung or recited. If sung, enter text from form, if not, add the file from the proper.
    if ($_POST['entranceA'] != "") {
        echo "[Music] ♫ " .  $_POST['entranceA'] .  " ♫ </p>";
        }
    else {
      if (file_exists("../lectionary/{$filenamePrefix}_entranceAnt.html")) // check is there is a second reading for this liturgy. There usually will not be for a weekday mass,
        {
        readfile("../lectionary/{$filenamePrefix}_entranceAnt.html");
        }  
    }
?>
<p> <?php readfile("../Missal/introductoryRites.htm"); ?> </p>
    
<p> Opening remarks</p>
<p> Greeting </p>
<p> Penitential Act </p>
<p> Kyrie </p>

<?php // If the Gloria is sung, insert a music tag. If recited, insert Gloria text.
if ($_POST[gloriaSung] == "sung") {
  echo "<p> [Music] ♫ Gloria ♫ </p>"; }
  else {
    readfile("../Missal/gloria.html");
  }
?>
    <p> Let us Pray. <?php readfile("../lectionary/{$filenamePrefix}_collect.html"); ?> </p>
    <h2>The Liturgy of the Word</h2>
    <p> A reading from </p>
    <p> <?php readfile("../lectionary/{$filenamePrefix}_r1.html"); ?> </p>
    <p>The Word of the Lord. <br>R. Thanks be to God. </p>
    <p> ♫ <?php echo $_POST[respPsalm]; ?> ♫ </p>
    
    <?php 
    if (file_exists("../lectionary/{$filenamePrefix}_r2.html")) // check is there is a second reading for this liturgy. There usually will not be for a weekday mass,
        {
        echo "<p> A reading from </p>";
        readfile("../lectionary/{$filenamePrefix}_r2.html");
        echo "<p> The Word of the Lord. <br> R. Thanks be to God.</p>";
        }
  
    if (file_exists("../lectionary/{$filenamePrefix}_seq.html")) // check is there is a Sequense for this liturgy. If so, add it to outpus
        {
        readfile("../lectionary/{$filenamePrefix}_seq.html");
        }
    ?>

    <p> Gospel Acclamation</p>
    <p> ♫ Aleleluia <?php echo $_POST[gospelAccl]; ?> Aleleluia ♫ </p>

    <p>The Lord be with you. <br>R. and with your Spirit. </p>
    <p>A reading from the Gospel according to </p>
    <p> <?php readfile("../lectionary/{$filenamePrefix}_gospel.html"); ?> </p>
    <p>The Gospel of the Lord. </p>
    <p>R. Praise to you Lord Jesus Christ.</p>

    <p> <?php echo " $_POST[homily]"; ?> </p>
    <p> <?php readfile("../Missal/Nicene_Creed.html"); ?> </p>
    <h3> Universal Prayer </h3>
    <p> <?php echo $_POST[universalPrayer]; ?> </p>
    <p> <h2>The Liturgy of the Eucharist</h2> </p>

    <p> ♫ <?php echo $_POST[offertoryHymn]; ?> ♫ </p>
    <h3> Invitation to Prayer </h3>
    <h3> Prayer over the Offerings</h3>
    <p> <?php readfile("../Missal/prepOfGifts.html");  ?> </p>
    <p> <?php readfile("../lectionary/{$filenamePrefix}_prayerOverOffer.html"); ?> </p>
    <p> <?php readfile("../Missal/PrefaceDialogue.html"); ?> </p>

    <p> <?php /* readfile("../lectionary/{$filenamePrefix}_preface.html"); */ ?> </p>
    <h3>Preface Acclamation</h3>
    <?php
    switch ($_POST[whichEP]) {
        case EP1:
            readfile("../Missal/EP1.php");
            break;
        case EP2:
            readfile("../Missal/EP2.html");
            break;
        case EP3:
            readfile("../Missal/EP3.html");
            break;
        case EP4:
            readfile("../Missal/EP4.html");
            break;
        case EPR1:
            readfile("../Missal/EPR1.html");
            break;
         case EPR2:
            readfile("../Missal/EPR2.html");
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
    <p> <?php /* readfile("../lectionary/{$filenamePrefix}_blessing.html");  */ ?> </p>
    <p> Dismissal</p>
    <p> <?php echo $_POST[recessional]; ?></p>

    </body>
</html>
