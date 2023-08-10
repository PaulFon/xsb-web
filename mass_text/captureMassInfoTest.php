<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mass Info Form</title>
<meta name="Author" content="Paul Fontaine">
<meta name="DateCreated" content="06/20/2020">
<meta name="LastModified" content="03/01/2022">
<meta name="Description" content="This This program presents a form to prompt user for the information needed to build the captioning transcript.">
<meta name="Classification" content="Religion, Christianity, Catholicism, Theology, Liturgy">
<meta name="KeyWords" content="Order of Mass, Roman Missal">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" media="all" href="style/Input_Form.css">

</head>
<body>
<h1>Enter info about the mass you want to caption</h1>
<p>Complete this form only after generating all of the readings files using the <a href="captureAllReadings.html" target="_blank">Capture Readings app.</a></p>

<p>Check the <a href="https://canonicaleye.org/lectionary/Lectionary_index.html" target="_blank">Lectionary - Index of Readings</a> for names for File Name prefix.</p>
<div class="container">
  <form action="assembleTranscript.php" method="post" id="" class="">

  <!--   <div class="row">
      <div class="col-25">
        <label for="cName">Celebrant's Name</label>
      </div>
      <div class="col-75">
        <input type="text" id="cName" name="celebrantsName" placeholder="Celebrant's Name..">
      </div>
    </div>
-->
<!--
    <div class="row">
      <div class="col-25">
        <label for="massDate">Date of Mass</label>
      </div>
      <div class="col-75">
        <input type="date" id="massDate" name="massDate" placeholder="Date of Mass..">
      </div>
    </div>


<div class="row">
      <div class="col-25">
        <label for="timingMarks">Add timing marks Y/N</label>
      </div>
      <div class="col-75">
        <input type="radio" name="timingMarks" value="timingY" required >Timing
        <input type="radio" name="timingMarks" value="timingN">No timing marks
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="fileNamePre">File Name prefix</label>
      </div>
      <div class="col-75">
        <input type="text" id="fileNamePre" name="fileNamePre" placeholder="e.g. 18thSundayinOrdinaryTime">
      </div>
    </div>
-->
 <div class="row">
      <div class="col-25">
        <label for="OpeningWlecome">Welcome/Opening Remarks</label>
      </div>
      <div class="col-75">
        <textarea id="Opening" name="Opening" wrap="soft" placeholder="e.g. Hello and welcome to Holy Redeemer..." style="height:100px"></textarea>
      </div>
    </div>    
      
    <div class="row">
      <div class="col-25">
        <label for="entranceA">Entrance Hymn</label>
      </div>
      <div class="col-75">
        <textarea id="entranceA" name="entranceA" wrap="soft" placeholder="Entrance Hymn if sung, otherwise leave blank for spoken antiphon/.." ></textarea>
      </div>
    </div>
<!--
    <div class="row">
      <div class="col-25">
        <label for="gloriaSung">Gloria</label>
      </div>
      <div class="col-75">
        <input type="radio" name="gloriaSung" value="sung" required >Gloria Sung
        <input type="radio" name="gloriaSung" value="read">Gloria Read
        <input type="radio" name="gloriaSung" value="none">No Gloria
      </div>
    </div>

   <div class="row">
      <div class="col-25">
        <label for="respPsalm">Responsorial Psalm</label>
      </div>
      <div class="col-75">
        <textarea id="respPsalm" name="respPsalm" placeholder="Responsorial Psalm.." ></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="gospelAccl">Gospel Acclamation</label>
      </div>
      <div class="col-75">
        <textarea id="gospelAccl" name="gospelAccl" placeholder="Gospel Acclamation.." ></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="homily">Homily</label>
      </div>
      <div class="col-75">
        <textarea id="homily" name="homily" placeholder="Homily.." style="height:100px"></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="universalPrayer">Universal Prayer</label>
      </div>
      <div class="col-75">
        <textarea id="universalPrayer" name="universalPrayer" placeholder="Universal Prayer.." style="height:100px"></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="offertoryHymn">Offertory Hymn </label>
      </div>
      <div class="col-75">
        <textarea id="offertoryHymn" name="offertoryHymn" placeholder="Offertory Hymn.." ></textarea>
      </div>
    </div>

<div class="row">
      <div class="col-25">
        <label for="whichEP">Eucharistic Prayer</label>
      </div>
      <div class="col-75">
          <ul>
            <li><input type="radio" name="whichEP" value="EP1" required >Eucharistic Prayer I</li>
            <li><input type="radio" name="whichEP" value="EP2">Eucharistic Prayer II</li>
            <li><input type="radio" name="whichEP" value="EP3">Eucharistic Prayer III</li>
            <li><input type="radio" name="whichEP" value="EP4">Eucharistic Prayer IV</li>
            <li><input type="radio" name="whichEP" value="EPR1">Eucharistic Prayer for Reconciliation I</li>
            <li><input type="radio" name="whichEP" value="EPR2">Eucharistic Prayer for Reconciliation II</li>
            <li><input type="radio" name="whichEP" value="none">none</li>
            </ul>
      </div>
    </div>
-->
<?php require_once('prefaceSelectForm.php'); ?>
      
<!--
<div class="row">
      <div class="col-25">
        <label for="holyHolyHoly">Sanctus</label>
      </div>
      <div class="col-75">
        <input type="radio" name="holyHolyHoly" value="HolySung" required >Holy, Holy, Holy - Sung
        <input type="radio" name="holyHolyHoly" value="HolyRead">Holy, Holy, Holy - Read
        <input type="radio" name="holyHolyHoly" value="SanctusSung" required >Sanctus - Sung
        <input type="radio" name="holyHolyHoly" value="SanctusRead" required >Sanctus - Read
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="lambOfGod">Lamb of God</label>
      </div>
      <div class="col-75">
        <input type="radio" name="lambOfGod" value="LambSung" required >Lamb of God - Sung
        <input type="radio" name="lambOfGod" value="LambRead">Lamb of God - Read
        <input type="radio" name="lambOfGod" value="AgnusSung" required >Agnus Dei - Sung
        <input type="radio" name="lambOfGod" value="AgnusRead" required >Agnus Dei - Read
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="CommunionAnt">Comunion Antiphon</label>
      </div>
      <div class="col-75">
        <textarea id="CommunionAnt" name="CommunionAnt" placeholder="Comunion Antiphon.."></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="Announcements">Announcements</label>
      </div>
      <div class="col-75">
          <textarea id="Announcements" name="Announcements" placeholder="e.g. This week..."></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-25">
        <label for="recessional">Recessional Hymn</label>
      </div>
      <div class="col-75">
        <textarea id="recessional" name="recessional" placeholder="Recessional Hymn.."></textarea>
      </div>
    </div>
-->
    <div class="row">
      <input type="submit" name="submit" value="submit" />
      <input type="reset" name="reset" value="reset" />
    </div>
  </form>
</div>

</body>
</html>
