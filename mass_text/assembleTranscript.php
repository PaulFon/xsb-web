<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assemble various texts for the Mass transcript</title>
<meta name="Author" content="Paul Fontaine">
<meta name="Description" content="This program prints a mass text to be used for capioning mass videos. User fills out infromation in captureMassInfo.php">
<meta name="DateCreated" content="05/20/2020">
<meta name="LastModified" content="05/08/2022">
<meta name="Classification" content="Religion, Christianity, Catholicism, Theology, Liturgy">
<meta name="KeyWords" content="Kewords">
<link rel="stylesheet" type="text/css" href="./captionTranscript.css">
</head>
<body>
<?php
// 2020-07-05 - Sean Bright
// date formats: https://www.php.net/manual/en/function.date.php
// require_once 'readingsVersionA.inc';  // calls php to get the readings from USCCB 

$massDate = new DateTime($_POST['massDate']);
// $filenamePrefix = get_the_readings($massDate->format('mdy'));

$filenamePrefix = $_POST['fileNamePre'];
?>

<!-- <p>Holy Redeemer Mass - <?php echo $_POST['massDate'] ?> </p> -->
<h2> The Introductory Rites </h2>
<?php   // check if the entrance antiphon is sung or recited. If sung, enter text from form, if not, add the file from the proper.
    
    $timingMark = "0:,0:";
    if ($_POST['timingMarks'] == "timingY") {echo ($timingMark) . '<br>'; }

    echo  nl2br($_POST[Opening], false) .  "</p>"; 
    if ($_POST['entranceA'] != "") {
        echo "[Music] ♫ " .  $_POST['entranceA'] .  " ♫ </p>";
        }
    else {
      if (file_exists("../lectionary/{$filenamePrefix}_entranceAnt.txt")) 
        {
        readfile("../lectionary/{$filenamePrefix}_entranceAnt.txt");
        }
    }
?>

<p> <?php readfile("../Missal/introRites.html"); ?> </p>

<h3> Penitential Act </h3>
<p> <?php readfile("../Missal/Order/penetentialAct.html"); ?> </p>

<?php // If the Gloria is sung, insert a music tag. If recited, insert Gloria text.

switch ($_POST[gloriaSung]) { 
        case read:
            readfile("../Missal/gloria.html");
            break;  
        case sung:
            echo "<p> [Music] ♫ Gloria ♫ </p>";
            break;
        case none:
            echo " "; }
    if ($_POST['timingMarks'] == "timingY") {echo ($timingMark)."<br>"; }  
    echo "Let us Pray.  ";
    readfile("../lectionary/{$filenamePrefix}_collect.html"); ?> </p>
    <h2>The Liturgy of the Word</h2>
    <!-- <p> A reading from </p> -->
    <p> <?php readfile("../lectionary/{$filenamePrefix}_r1.html"); ?> </p>
    <p>The Word of the Lord. <br>R. Thanks be to God. </p>

<?php   // check if the Responsorial Psalm is sung or recited. If sung, enter text from form, if not, add the file from the proper.
    if ($_POST[respPsalm] != "") {
        echo "[Music] ♫ " .  $_POST[respPsalm] .  " ♫ </p>";
        }
    else {
      if (file_exists("../lectionary/{$filenamePrefix}_Psalm.html"))
        {
        readfile("../lectionary/{$filenamePrefix}_Psalm.html");
        }
    }
?>
   
    <?php
    if (file_exists("../lectionary/{$filenamePrefix}_r2.html")) // check is there is a second reading for this liturgy. There usually will not be for a weekday mass,
        {
 
        readfile("../lectionary/{$filenamePrefix}_r2.html");
        echo "<p> The Word of the Lord. <br> R. Thanks be to God.</p>";
        }

    if (file_exists("../lectionary/{$filenamePrefix}_seq.html")) // check is there is a Sequense for this liturgy. If so, add it to outpus
        {
        readfile("../lectionary/{$filenamePrefix}_seq.html");
        }
    ?>

<?php   // check if the Gospel Acclamation is sung or recited. If sung, enter text from form, if not, add the file from the proper.
    if ($_POST[gospelAccl] != "") {
        echo "♫ Alleluia, Alleluia, Alleluia ♫ <br> ♫ " . $_POST[gospelAccl] . " ♫ <br> ♫ Alleluia, Alleluia, Alleluia ♫ " ;
        }
    else {
      if (file_exists("../lectionary/{$filenamePrefix}_gospelAccl.html"))
        {
        readfile("../lectionary/{$filenamePrefix}_gospelAccl.html");
        }
    }
?>

    <p>The Lord be with you. <br>R. and with your Spirit. </p>
    <!-- <p>A reading from the Gospel according to </p> -->
    <p> <?php readfile("../lectionary/{$filenamePrefix}_gospel.html"); ?> </p>
    <p>The Gospel of the Lord. </p>
    <p>R. Praise to you Lord Jesus Christ.</p>
    <?php if ($_POST['timingMarks'] == "timingY") {echo ($timingMark)."<br>"; } ?>
    
    <?php echo  nl2br($_POST[homily], false) .  "</p>"; ?>
    <?php if ($_POST['timingMarks'] == "timingY") {echo ($timingMark)."<br>"; } ?>
    <?php readfile("../Missal/Nicene_Creed.html"); ?> 
    <?php if ($_POST['timingMarks'] == "timingY") {echo ($timingMark)."<br>"; } ?>
    <h3> Universal Prayer </h3>

    <p> <?php echo nl2br($_POST[universalPrayer], false); ?> </p>
    <p> <h2>The Liturgy of the Eucharist</h2> </p>

    <p> [Music] ♫ <?php echo $_POST[offertoryHymn]; ?> ♫ </p>
    <h3> Invitation to Prayer </h3>
    <h3> Prayer over the Offerings</h3>
    <p> <?php readfile("../Missal/prepOfGifts.html");  ?> </p>
    <p> <?php readfile("../lectionary/{$filenamePrefix}_PrayerOverOfferings.html"); ?> </p>
    <p> <?php readfile("../Missal/PrefaceDialogue.html"); ?> </p>

    <?php // Print Preface
    $preface = ($_POST[whichPref]);
    /* removing the logic to set the preface if EP2 or EP4 because other prefaces can be said with these EPs
    if (($_POST[whichEP]) == ("EP2")) {
        $preface = "EP2";
    }
    if (($_POST[whichEP]) == ("EP4")) {
        $preface = "EP4";
    } */
    switch ($preface) { 
        case EP2:    readfile("../Missal/Order/Preface/Pref_EP2.html");
            break;
        case EP4:
            readfile("../Missal/Order/Preface/Pref_EP4.html");
            break; 
        case adv1:
            readfile("../Missal/Order/Preface/Pref_Advent_1.html");
            break;   
        case adv2:
            readfile("../Missal/Order/Preface/Pref_Advent_2.html");
            break;
        case nativ1:
            readfile("../Missal/Order/Preface/Pref_Nativity_1.html");
            break;
       case nativ2:
            readfile("../Missal/Order/Preface/Pref_Nativity_2.html");
            break;
       case nativ3:
            readfile("../Missal/Order/Preface/Pref_Nativity_3.html");
            break;
       case epip:
            readfile("../Missal/Order/Preface/Pref_Epiphany.html");
            break;
       case baptism:
            readfile("../Missal/Order/Preface/Pref_Baptism_of_Lord.html");
            break;
        case lent1:
            readfile("../Missal/Order/Preface/Pref_Lent_1.html");
            break;        
        case lent2:
            readfile("../Missal/Order/Preface/Pref_Lent_2.html");
            break;
        case lent3:
            readfile("../Missal/Order/Preface/Pref_Lent_3.html");
            break;
        case lent4:
            readfile("../Missal/Order/Preface/Pref_Lent_4.html");
            break;
         case first_Sun_Lent:
            readfile("../Missal/Order/Preface/Pref_1st_Sunday_Lent.html");
            break;
         case second_Sun_Lent:
            readfile("../Missal/Order/Preface/Pref_2nd_Sunday_Lent.html");
            break;
        case third_Sun_Lent:
            readfile("../Missal/Order/Preface/Pref_3rd_Sunday_Lent.html");
            break;
        case fourth_Sun_Lent:
            readfile("../Missal/Order/Preface/Pref_4th_Sunday_Lent.html");
            break;
        case fifth_Sun_Lent:
            readfile("../Missal/Order/Preface/Pref_5th_Sunday_Lent.html");
            break;
        case passSun:    readfile("../Missal/Order/Preface/Pref_PassionSunday.html");
            break;
        case easter1:
            readfile("../Missal/Order/Preface/Pref_Easter_1.html");
            break;
       case easter2:
            readfile("../Missal/Order/Preface/Pref_Easter_2.html");
            break;
       case easter3:
            readfile("../Missal/Order/Preface/Pref_Easter_3.html");
            break;
       case easter4:
            readfile("../Missal/Order/Preface/Pref_Easter_4.html");
            break;
       case easter5:
            readfile("../Missal/Order/Preface/Pref_Easter_5.html");
            break;
        case ass1:    readfile("../Missal/Order/Preface/Pref_Ascension_of_the_Lord_1.html");
            break;
        case ass2:        readfile("../Missal/Order/Preface/Pref_Ascension_of_the_Lord_2.html");
            break;
        case pentecost:
            readfile("../Missal/Order/Preface/Pref_Pentecost.html");
            break; 
        case ot1:
            readfile("../Missal/Order/Preface/Pref_OT_1.html");
            break;          
        case ot2:
            readfile("../Missal/Order/Preface/Pref_OT_2.html");
            break;
        case ot3:
            readfile("../Missal/Order/Preface/Pref_OT_3.html");
            break;
        case ot4:
            readfile("../Missal/Order/Preface/Pref_OT_4.html");
            break;
        case ot5:
            readfile("../Missal/Order/Preface/Pref_OT_5.html");
            break;
        case ot6:
            readfile("../Missal/Order/Preface/Pref_OT_6.html");
            break;
        case ot7:
            readfile("../Missal/Order/Preface/Pref_OT_7.html");
            break;
        case ot8:
            readfile("../Missal/Order/Preface/Pref_OT_8.html");
            break;
        case trinity:
            readfile("../Missal/Order/Preface/Pref_Holy_Trinity.html");
            break;
        case he1:
            readfile("../Missal/Order/Preface/Pref_Holy_Eucharist_1.html");
            break;
        case he2:
            readfile("../Missal/Order/Preface/Pref_Holy_Eucharist_2.html");
            break;
        case presentation:
            readfile("../Missal/Order/Preface/Pref_Presentation.html");
            break;
        case transfiguration:
            readfile("../Missal/Order/Preface/Pref_Transfiguration.html");
            break;
        case Christ_the_King:
            readfile("../Missal/Order/Preface/Pref_Christ_the_King.html");
            break;
        case Assumption:
            readfile("../Missal/Order/Preface/Pref_ Solemnity_of_the_Assumption_of_the_Blessed_Virgin_Mary.html");
            break;
        case apost1:
            readfile("../Missal/Order/Preface/Pref_Apostles_1.html");
            break;
        case apost2:
            readfile("../Missal/Order/Preface/Pref_Apostles_2.html");
            break;
        default:      
            echo "invalid Preface";
    }

    // 4 options for Sanctus - english or latin, sung or recited each option displays a different html file. 

    switch ($_POST[holyHolyHoly]) { 
        case HolySung:
            readfile("../Missal/Order/HolyHolySung.html");
            break;
        case HolyRead:
            readfile("../Missal/Order/HolyHoly.html");
            break;
        case SanctusSung:
            readfile("../Missal/Order/SanctusSung.html");
            break;        
        case SanctusRead:
            readfile("../Missal/Order/Sanctus.html");
            break;
        case none:
            echo " no Holy Holy"; }

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

    <?php readfile("../Missal/Order/CommunionRite.html"); ?> </p>
    <?php if ($_POST['timingMarks'] == "timingY") {echo ($timingMark)."<br>"; } ?>
    <?php switch ($_POST[lambOfGod]) { 
        case LambSung:
            readfile("../Missal/Order/LambOfGodSung.html");
            break;
        case LambRead:
            readfile("../Missal/Order/LambOfGodRead.html");
            break;
        case AgnusSung:
            readfile("../Missal/Order/AgnusSung.html");
            break;        
        case AgnusRead:
            readfile("../Missal/Order/Agnus.html");
            break;
        case none:
            echo " no Holy Holy"; }

        ?>
    <?php readfile("../Missal/Order/Communion_part_1.html"); ?> </p>
    <?php /* readfile("../Missal/Order/InvitationToCommunion.html"); */ ?> </p> 
    
<?php   // check if the Comunion antiphon is sung or recited. If sung, enter text from form, if not, add the file from the proper.
    if ($_POST[CommunionAnt] != "") {
        echo " [Music] <br> ♫ " . $_POST[CommunionAnt] . "♫ <br> [Music]"  ;
        }
    else {
      if (file_exists("../lectionary/{$filenamePrefix}_CommunionAnt.html"))
        {
        readfile("../lectionary/{$filenamePrefix}_CommunionAnt.html");
        }
    }
?>
    <p> <?php readfile("spiritualComunion.html"); ?> </p>
    <p> Let us Pray </p>
    <?php readfile("../lectionary/{$filenamePrefix}_PrayerafterCommunion.html"); 
        echo nl2br($_POST[Announcements], false) .  "</p>"; ?>
    <h2>The Concluding Rites</h2>
    <?php readfile("../Missal/ConcludingRites.html"); ?> 
    <?php readfile("../lectionary/{$filenamePrefix}_PrayerOverThePeople.html"); 
        echo "♫ " . $_POST[recessional] . " ♫ <br> [Music]"; 
         ?>

    </body>
</html>
