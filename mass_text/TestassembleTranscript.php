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
    Program Description: This program prints a mass text to be used for capioning mass videos. User fills out infromation in captureMassInfo.php
    Date: 05/20/2020
    Modified 02/20/2021 - Added some Preface conditions. Added "no gloria" condition
    Edited  1.5 07/27/2020
      added Preface files
      Took out the "include" 'readingsVersionA.inc' to get readings from USCCB. Because USCCB introduced a new web site and stuff does not work. Will need to be recoded.

    if (isset($_POST['submit'])) { // Make sure that the form has been submitted
    	echo '<pre>';
    	print_r($_POST);
    	echo '</pre>';
    }
*/
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

<p> <?php readfile("../Missal/introRites.html"); ?> </p>

<p> Opening remarks</p>
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
    
?>


    <?php // Print Preface
    // If the Eucharistic prayer is EP2 or EP4, do not add a preface file because the preface is included in the EP file;
    if (($_POST[whichEP]) == ("EP2")) {  // EP 2 and EP 4 have their own preface so you don't want to use one of the optionals.
        readfile("../Missal/Pref_EP2.html");
    }
    if (($_POST[whichEP]) != ("EP4")) {  // EP 2 and EP 4 have their own preface so you don't want to use one of the optionals.
        readfile("../Missal/Pref_EP4.html");
    }
    
     switch ($_POST[whichPref]) { 
 
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
        default:
            echo "invalid Preface";
    }

    ?>
    <h3>Preface Acclamation</h3>

    <p> <?php readfile("../Missal/PrefAcclaim.html"); ?> </p><?php

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

    <?php readfile("../Missal/Order/CommunionRite.html"); ?> </p>
    
    <h3> Lamb of God</h3>

    <?php // 4 options for Sanctus - english or latin, sung or recited each option displays a different html file. 

    switch ($_POST[holyHolyHoly]) { 
        case HolySung:
            echo "HolyHoly Sung";
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
    
?>

    <?php readfile("../Missal/Order/InvitationToCommunion.html"); ?> </p>
    <p> The Communion</p>
    <p> <?php readfile("spiritualComunion.html"); ?> </p>

    <h3> Prayer after Communion/Communion Ant.</h3>
    <p> <?php readfile("../lectionary/{$filenamePrefix}_PrayerafterCommunion.html"); ?>
    <h2>The Concluding Rites</h2>
    <p> Final Blessing</p>
    <p> </p>
    
    <p> <?php /* readfile("../lectionary/{$filenamePrefix}_blessing.html");  */ ?> </p>
    <p> Dismissal</p>
    <p> <?php echo $_POST[recessional]; ?></p>

    </body>
</html>
