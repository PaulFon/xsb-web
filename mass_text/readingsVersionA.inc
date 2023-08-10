<?php
function get_the_readings($date) {
    
    $fullPage= file_get_contents('http://usccb.org/bible/readings/' . $date . '.cfm'); // Get the reading (HTML file) for the selected date from USCCB
    /* Find the name for the Liturgy. It is after a <h3> tag */
    
    $litNameStart=strpos($fullPage, "><h3>");
    $litNameEnd=strpos($fullPage, "<br />", $litNameStart);
    // $litNameEnd=strpos($fullPage, "<br />", $litNameStart);
    $lectionaryName=substr($fullPage, $lectionaryNumStart, 13);
    $lectionaryNumStart=strpos($fullPage,"Lectionary", $litNameEnd);
    $lectionaryNumEnd=strpos($fullPage,"</h3>", $lectionaryNumStart);
    $lectionaryNum=substr($fullPage, $lectionaryNumStart+12, $lectionaryNumEnd);
    $litName=substr($fullPage, $litNameStart+5, ($litNameEnd - ($litNameStart + 5)));
    $litNameS = preg_replace('/[^a-z]+/i', '', $litName); // Remove anything that is not a letter
    // Text to append to the start and to the end of the HTML files
    

    $htmlStart = ' <!doctype html>
      <html lang="en">';
    $htmlEnd = ' </html>';

    $r1start=strpos($fullPage,"<h4>Reading 1"); // find the start of the first reading
    $r1End=strpos($fullPage,'</div>',$r1start); // find the end of the first reading
    $r1=substr($fullPage,$r1start, $r1End-$r1start); /* parse Reading 1 */
    echo "ready to write reading 1 " . $r1;
    $outFile = fopen("../lectionary/" . $litNameS .  "_r1.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $r1);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);

    $respStart=strpos($fullPage,"<h4>Responsorial"); // find the start of the Responsorial Psalm
    $respEnd=strpos($fullPage,'</div>',$respStart); // find the end of the reading 2
    $resp=substr($fullPage,$respStart,$respEnd-$respStart); /* parse Responsorial */
    $outFile = fopen("../lectionary/" . $litNameS . "_resp.html" , "w") or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $resp);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);
    
    
   ; 
    $r2start=strpos($fullPage,"<h4>Reading 2"); // find the start of the reading 2
   // if  ($r2Start) { // Not all liturgies have a second reading as in weekday readings. Check to see if there is. The if statement code is not working so it is commented out
 
        $r2End=strpos($fullPage, '</div',$r2start); // find the end of the reading 2
        $r2=substr($fullPage,$r2start,$r2End-$r2start); /* parse Reading 2 */
        $outFile = fopen("../lectionary/" . $litNameS . "_r2.html" , "w")  or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $r2);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
   // }

    $seqStart=strpos($fullPage,"<h4>Sequence"); // find the start of the Sequence
    if  ($seqStart) { // Not all liturgies have a "sequence" check to see if there is.
        $seqEnd=strpos($fullPage,'</div>',$seqStart); // find the end of the sequense
        $seq=substr($fullPage,$seqStart,($seqEnd-$seqStart)); /* parse Sequense */

        $outFile = fopen("../lectionary/" . $litNameS . "_seq.html" , "w")  or die("Unable to open file!");
        fwrite($outFile, $htmlStart);
        fwrite($outFile, $seq);
        fwrite($outFile, $htmlEnd);
        fclose($outFile);
    }

    $alleluiaStart=strpos($fullPage,"<h4>Alleluia"); // find the start of the Alleluia
    $alleluiaEnd=strpos($fullPage,'</div>',$alleluiaStart); // find the end of the reading 2
    $alleluia=substr($fullPage,$alleluiaStart,($alleluiaEnd-$alleluiaStart));
    $outFile = fopen("../lectionary/" . $litNameS . "_alleluia.html" , "w")  or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $alleluia);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);

    $gospelStart=strpos($fullPage,"<h4>Gospel"); // find the start of the Gospel
    $gospelEnd=strpos($fullPage,'</div>', $gospelStart); // find the end of the gospel
    $gospelLen=$gospelEnd-$gospelStart;
    $gospel=substr($fullPage, $gospelStart, $gospelLen); /* parse the Gospel */
    $outFile = fopen("../lectionary/" . $litNameS . "_gospel.html" , "w")  or die("Unable to open file!");
    fwrite($outFile, $htmlStart);
    fwrite($outFile, $gospel);
    fwrite($outFile, $htmlEnd);
    fclose($outFile);

    $outFileName=$litNameS.$lectionaryName."_";
    return $litNameS;

}
?>
