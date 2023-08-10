<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Mass May 24 2020</title>


</head>
<body>

<?php
    /*
    Name: Paul Fontaine
    Program Description: This program prints a mass text in a tabular format to be used for acapioning mass videos
    Date: 05/20/2020

    if (isset($_POST['submit'])) { // Make sure that the form has been submitted
    	echo '<pre>';
    	print_r($_POST);
    	echo '</pre>';
    }


    // $celebrant = $_POST['celebrantsName'];
  */
?>

<p>Holy Redeemer Mass - <?php echo $_POST[massDate] ?> </p>
<h2> The Introductory Rites </h2>

    <p> Opening remarks</p>
    <p> ♫ <?php echo $_POST[entranceA]; ?> ♫ </p>
    <p> Greeting </p>
    <p> Penitential Act </p>
    <p> Kyrie </p>
    <p> ♫ Gloria ♫ </p>
    <p> Let us Pray. <?php readfile("../lectionary/Ascension_collect.html"); ?> </p>
    <p> <h2>The Liturgy of the Word</h2> </p>
    <p> A reading from </p>
    <p> <?php readfile("../lectionary/Ascension_r1.html"); ?> </p>
    <p> The Word of the Lord. R. Thanks be to God. </p>
    <p> ♫ <?php echo $_POST[respPsalm]; ?> ♫ </p>
    <p> A reading from </p>
    <p> <?php readfile("../lectionary/Ascension_r2.html"); ?></p>
    <p> The Word of the Lord. R. Thanks be to God.</p>
    <p> Gospel Acclamation</p>
    <p> ♫ Aleleluia <?php echo $_POST[gospelAccl]; ?> Aleleluia ♫ </p>
    <p> Dialogue at the Gospel</p>
    <p> <?php readfile("../lectionary/Ascension_verse_before_gospel.html"); ?> </p>
    <p> The Lord be with you. R. and with your Spirit. <br>
    <p> A reading from the Gospel according to </p>
    <p> <?php readfile("../lectionary/Ascension_gospel.html"); ?> </p>
    <p> The Gospel of the Lord. R. Praise to you Lord Jesus Christ.</p>
    <p> Homily </p>
    <p> <?php echo " $_POST[homily]"; ?> </p>
    <p> <?php readfile("../Missal/Nicene_Creed.html"); ?> </p>
    <p> Universal Prayer </p>
    <p> <?php echo $_POST[universalPrayer]; ?> </p>
    <p> <h2>The Liturgy of the Eucharist</h2> </p>
    <p> Offertory Hymn</p>
    <p> ♫ <?php echo $_POST[offertoryHymn]; ?> ♫ </p>
    <p> Presentation and Preparation of the Gifts </p>
    <p> Invitation to Prayer </p>
    <p> Prayer over the Offerings</p>
    <p> Preface Dialogue </p>
    <p> Preface</p>
    <p> Preface Acclamation</p>
    <p> <?php readfile("../Missal/ep1.html"); ?> </p>
    <p> Mystery of Faith</p>
    <p> Concluding Doxology</p>
    <p> <h2>The Communion Rite</h2></p>
    <p> Lamb of God</p>
    <p> Sign of Peace</p>
    <p> Invitation to Communion</p>
    <p> The Communion</p>
    <p> Prayer after Communion/Communion Ant.</p>
    <h2>The Concluding Rites</h2>
    <p> Final Blessing</p>
    <p> </p>
    <p> <?php readfile("../lectionary/easter_7_sunday_solemn_blessing.html"); ?></p>
    <p> Dismissal</p>
    <p> <?php echo $_POST[recessional]; ?></p>

    </body>
</html>
