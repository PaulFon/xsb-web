<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Get Readings</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- This line is required to make it a "responsive" page -->

<?php
  /*
  Author: Paul Fontaine
  Date created: 06/11/2020
  Version 1.0 06/24/2020
  Program Description: Prompt for date and go out to USCCB to get the readings for that day and writes each reading to an HTML file.
  */

$litDate = "06/28/20";
echo "the liturgy date is: " . $litDate ."<br>";

/* Set up beginning and ending of the HTML files. */

$htmlStart= '<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>' . $litName. ' </title>
</head>
<body>';

$htmlEnd = '</div>
</body>
</html>';

$fullPage= file_get_contents('http://usccb.org/bible/readings/062828.cfm'); // Get the reading (HTML file) for the selected date from USCCB
/* Find the name for the Liturgy. It is after a <h3> tag */
$litNameStart=strpos($fullPage, "><h3>");
$litNameEnd=strpos($fullPage, "<br />", $litNameStart);

$lectionaryNumStart=strpos($fullPage,"Lectionary", $litNameEnd); 

$lectionaryName=substr($fullPage, $lectionaryNumStart, 19);
echo  "$lectionaryName \n";


$litName=substr($fullPage, $litNameStart+5, (($litNameEnd ) - $litNameStart));
$litNameS=str_replace(' ' , '', $litName); // take the spaces out of the Title string to use for filename

$r1start=strpos($fullPage,"<h4>Reading 1"); // find the start of the first reading
$r1End=strpos($fullPage,'</div>',$r1start); // find the end of the first reading
$r1=substr($fullPage,$r1start, $r1End-$r1start); /* parse Reading 1 */
$outFile = fopen("r1.html" , "w") or die("Unable to open file!");
fwrite($outFile, $htmlStart);
fwrite($outFile, $r1);
fwrite($outFile, $htmlEnd);
fclose($outFile);

$respStart=strpos($fullPage,"<h4>Responsorial"); // find the start of the Responsorial Psalm
$respEnd=strpos($fullPage,'</div>',$respStart); // find the end of the reading 2
$resp=substr($fullPage,$respStart,$respEnd-$respStart); /* parse Responsorial */
$outFile = fopen("resp.html" , "w") or die("Unable to open file!");
fwrite($outFile, $htmlStart);
fwrite($outFile, $resp);
fwrite($outFile, $htmlEnd);
fclose($outFile);

$r2start=strpos($fullPage,"<h4>Reading 2"); // find the start of the reading 2
$r2End=strpos($fullPage,'</div>',$r2start); // find the end of the reading 2
$r2=substr($fullPage,$r2start,($r2End-$r2start)); /* parse Reading 2 */
$outFile = fopen("r2.html" , "w") or die("Unable to open file!");
fwrite($outFile, $htmlStart);
fwrite($outFile, $r2);
fwrite($outFile, $htmlEnd);
fclose($outFile);
    
$seqStart=strpos($fullPage,"<h4>Sequence"); // find the start of the Sequence
if  ($seq) { // Not all liturgies have a "sequence" check to see if there is.
    $seq=substr($fullPage,$seqStart,($alleluiaStart-$seqStart)); /* parse Sequense */
    $outFile = fopen("seq.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $seq);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);

} else {
  echo ("there is no Sequense <br>");
}

$alleluiaStart=strpos($fullPage,"<h4>Alleluia"); // find the start of the Alleluia
$alleluiaEnd=strpos($fullPage,'</div>',$alleluiaStart); // find the end of the reading 2
$alleluia=substr($fullPage,$alleluiaStart,($alleluiaEnd-$alleluiaStart));
$outFile = fopen("alleluia.html" , "w") or die("Unable to open file!");
fwrite($outFile, $htmlStart);
fwrite($outFile, $alleluia);
fwrite($outFile, $htmlEnd);
fclose($outFile);

$gospelStart=strpos($fullPage,"<h4>Gospel"); // find the start of the Gospel
$gospelEnd=strpos($fullPage,'</div>', $gospelStart); // find the end of the gospel
$gospelLen=$gospelEnd-$gospelStart;
$gospel=substr($fullPage, $gospelStart, $gospelLen); /* parse the Gospel */
$outFile = fopen('gospel.html', 'w') or die("Unable to open file!");
fwrite($outFile, $htmlStart);
fwrite($outFile, $gospel);
fwrite($outFile, $htmlEnd);
fclose($outFile);


$outFileName=$litNameS.$lectionaryName."_";
echo "$outFileName \n";

?>

</body>
</html>
