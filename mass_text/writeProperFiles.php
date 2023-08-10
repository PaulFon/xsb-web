<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title> Title</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- This line is required to make it a "responsive" page -->

<?php
  /*
  Author: Paul Fontaine
  Date created: 06/07/2020
  Version 1.0 06/07/2020
  Program Description: This program takes text that was pasted into the form captureProperInfo.html
  */
/* Diagnostic to print form fields
  if (isset($_POST['submit'])) { // Make sure that the form has been submitted
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
  }
*/

// Text to append to the start and to the end of the HTML files
$htmlStart = '<!doctype html>
  <html lang="en">';
$htmlEnd =
  '</p>
  </html>';

// Write the Entrance Antiphon if text was entered in that field
  if ($_POST['entranceA'] != "") {
    $outFile = fopen("../lectionary/" . $_POST['fileNamePrefix'] . "_entranceAnt.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $_POST['entranceA']);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Entrance Antiphon file written" . "<br>";
    }

// Write the Collect if text was entered in that field
  if ($_POST['collect'] != "") {
    $outFile = fopen("../lectionary/" . $_POST['fileNamePrefix'] . "_collect.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $_POST['collect']);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Collect file written" . "<br>";
    }
 

// Write the Gospel Acclamation if text was entered in that field
  if ($_POST['gospelAccl'] != "") {
    $outFile = fopen("../lectionary/" . $_POST['fileNamePrefix'] . "_gospelAccl.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $_POST['gospelAccl']);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Gospel Acclamation file written" . "<br>";
    }

// Write the Prayer Over Offerings if text was entered in that field
  if ($_POST['Prayer_Over_Offerings'] != "") {
    $outFile = fopen("../lectionary/" . $_POST['fileNamePrefix'] . "_prayerOverOffer.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $_POST['Prayer_Over_Offerings']);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Prayer over the Offerings file written" . "<br>";
    }

// Write the Communion Antiphon if text was entered in that field
  if ($_POST['CommunionAnt'] != "") {
    $outFile = fopen("../lectionary/" . $_POST['fileNamePrefix'] . "_CommunionAnt.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $_POST['CommunionAnt']);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Comunion Antiphon file written" . "<br>";
    }

// Write the Prayer after Communion if text was entered in that field
  if ($_POST['PrayerafterCommunion'] != "") {
    $outFile = fopen("../lectionary/" . $_POST['fileNamePrefix'] . "_PrayerafterCommunion.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $_POST['PrayerafterCommunion']);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Prayer after Communion file written" . "<br>";
    }
?>

</body>
</html>
