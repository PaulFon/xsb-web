<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Write the readings files</title>
<meta name="Author" content="Paul Fontaine">
<meta name="DateCreated" content="07/24/2020">
<meta name="LastModified" content="02/04/2022">
<meta name="Description" content="This program takes text that was pasted into the form captureAllReadings.html and writes an HTML file for each..">
<meta name="Classification" content="Religion, Christianity, Catholicism, Theology, Liturgy">
<meta name="KeyWords" content="Order of Mass, Roman Missal">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php

$htmlStart = '<!doctype html>
  <html lang="en">
  <body>
  <p>';
$htmlEnd =
  '</p>
  </body>
  </html>';
    $htmlText = $_POST['entranceA'] ;
// Write the Entrance Antiphon if text was entered in that field
  if ($_POST['entranceA'] != "") {
    $htmlText = $_POST['entranceA'] ;
    $htmlText = nl2br($htmlText, false);
     
    $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_entranceAnt.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $htmlText);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Entrance Antiphon file written" . "<br>";
    }

// Write the Collect if text was entered in that field
  if ($_POST['collect'] != "") {
    $htmlText = $_POST['collect'] ;
    $htmlText = nl2br($htmlText, false);
      // $collectA =str_replace(\n,"<br>",$collectA);
    // echo $collectA;
    $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_collect.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    // fwrite($outFile, $_POST['collect']);
    // fwrite($outFile, nl2br($_POST['collect']),false);
    fwrite($outFile, $htmlText);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Collect file written" . "<br>";
    }

    // Write the First Reading if text was entered in that field
      if ($_POST['firstReading'] != "") {
        $htmlText = $_POST['firstReading'] ;
        $htmlText = nl2br($htmlText, false);
        $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_r1.html" , "w") or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $htmlText);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
        echo "First Reading file written" . "<br>";
        }

     if ($_POST['responsorialPsalm'] != "") {
        $htmlText = $_POST['responsorialPsalm'] ;
        $htmlText = nl2br($htmlText, false);
        $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_Psalm.html" , "w") or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $htmlText);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
        echo "responsorial Psalm file written" . "<br>";
        }

     if ($_POST['secondReading'] != "") {
        $htmlText = $_POST['secondReading'] ;
        $htmlText = nl2br($htmlText, false);
        $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_r2.html" , "w") or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $htmlText);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
        echo "Second Reading file written" . "<br>";
        }

 if ($_POST['sequence'] != "") {
        $htmlText = $_POST['sequence'] ;
        $htmlText = nl2br($htmlText, false);
        $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_seq.html" , "w") or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $htmlText);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
        echo "Sequence file written" . "<br>";
        }

// Write the Gospel Acclamation if text was entered in that field
  if ($_POST['gospelAccl'] != "") {
    $htmlText = $_POST['gospelAccl'] ;
    $htmlText = nl2br($htmlText, false);
    $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_gospelAccl.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $htmlText);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Gospel Acclamation file written" . "<br>";
    }

    if ($_POST['gospel'] != "") {
        $htmlText = $_POST['gospel'] ;
        $htmlText = nl2br($htmlText, false);
        $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_gospel.html" , "w") or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $htmlText);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
        echo "Gospel file written" . "<br>";
        }

// Write the Prayer Over Offerings if text was entered in that field
  if ($_POST['PrayerOverOfferings'] != "") {
        $htmlText = $_POST['PrayerOverOfferings'] ;
        $htmlText = nl2br($htmlText, false);
        $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_PrayerOverOfferings.html" , "w") or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $htmlText);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
        echo "PrayerOverOfferings file written" . "<br>";
        }

// Write the Communion Antiphon if text was entered in that field
  if ($_POST['CommunionAnt'] != "") {
    $htmlText = $_POST['CommunionAnt'] ;
    $htmlText = nl2br($htmlText, false);
    $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_CommunionAnt.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $htmlText);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Comunion Antiphon file written" . "<br>";
    }

// Write the Prayer after Communion if text was entered in that field
  if ($_POST['PrayerafterCommunion'] != "") {
    $htmlText = $_POST['PrayerafterCommunion'] ;
    $htmlText = nl2br($htmlText, false);
    $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_PrayerafterCommunion.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $htmlText);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Prayer after Communion file written" . "<br>";
    }


// Write the Prayer over the People if text was entered in that field
  if ($_POST['PrayerOverThePeople'] != "") {
    $htmlText = $_POST['PrayerOverThePeople'] ;
    $htmlText = nl2br($htmlText, false);
    $outFile = fopen("../lectionary/" . $_POST['lectionaryCycle'] . "_PrayerOverThePeople.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $htmlText);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    echo "Prayer Over the People file written" . "<br>";
    }
?>

</body>
</html>
