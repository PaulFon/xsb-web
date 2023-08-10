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
    Date: 05/18/2020
    */
    if (isset($_POST['submit'])) { // Make sure that the form has been submitted
    	echo '<pre>';
    	print_r($_POST);
    	echo '</pre>';
    }

    // $celebrant = $_POST['celebrantsName'];

?>

    Holy Redeemer Mass - <?php echo $_POST[massDate] ?>
    <table width='100%' border='1'>
        <tbody>
          <tr>
              <td> <b> The Introductory Rites </b> </td>
            <td>&nbsp;  </td>
          </tr>
            <tr>
            <td> Entrance Antiphon/Hymn/Chant
            <td> ♫ <?php echo $_POST[entranceA]; ?> ♫
            </tr>

            <tr>
                <td> Greeting
                <td> <?php readfile("insert_text_here.html"); ?>
            </tr>

            <tr>
              <td> Penitential Act
              <td> <?php readfile("insert_text_here.html"); ?>
            </tr>

            <tr>
              <td> Kyrie
              <td> <?php readfile("insert_text_here.html"); ?>
            </tr>

            <tr>
              <td> Gloria
              <td> ♫ Gloria ♫"
            </tr>

            <tr>
              <td> Collect
              <td> <?php readfile("insert_text_here.html"); ?>
            </tr>

            <td> <b>The Liturgy of the Word</b>
              <td>&nbsp;  </td>
            </tr>

            <tr>
              <td> First Reading
              <td> <?php readfile("../lectionary/firstReading.html"); ?>
            </tr>

            <tr>
              <td> Responsorial Psalm
              <td> ♫ Psalm 66 R. Let all the earth cry out to God with Joy ♫
            </tr>

            <tr>
              <td> Second Reading
              <td> <?php readfile("../lectionary/secondReading.html"); ?>
            </tr>

            <tr>
              <td> Gospel Acclamation
              <td> ♫ Whoever loves me will keep my word, says the Lord, and my Father will love him and we will come to him ♫
            </tr>

            <tr>
              <td> Dialogue at the Gospel
              <td> <?php readfile("insert_text_here.html"); ?>
            </tr>

            <tr>
              <td> Gospel Reading
              <td> <?php readfile("../lectionary/gospelReading.html"); ?>
            </tr>

            <tr>
              <td> Homily
              <td> <?php echo " $_POST[homily]"; ?>
            </tr>

          <tr>
            <td> Profession of Faith
            <td> <php? readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Universal Prayer
            <td> pray to the Lord. For those prayers we hold in the silence of our hearts. We pray to the Lord";
          </tr>

          <tr>
            <td> <b>The Liturgy of the Eucharist</b>
            <td>&nbsp;  </td>
          </tr>

          <tr>
            <td> Presentation and Preparation of the Gifts
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Invitation to Prayer
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Prayer over the Offerings
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> The Eucharistic Prayer
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Preface Dialogue
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Preface
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Preface Acclamation
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Eucharistic Prayer
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Mystery of Faith
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Concluding Doxology
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <td> <b>The Communion Rite</h2></b>
            <td>&nbsp;  </td>
          </tr>

          <tr>
            <td> Lamb of God
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Sign of Peace
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Invitation to Communion
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> The Communion
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Prayer after Communion/Communion Ant.
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>";

          <tr>
            <td> <b>The Concluding Rites</b></td>
            <td>&nbsp;  </td>
          </tr>

          <tr>
            <td> Final Blessing
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Solemn Blessing or Prayer over the People
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Dismissal
            <td> <?php readfile("insert_text_here.html"); ?>
          </tr>

          <tr>
            <td> Recessional Hymn
            <td> <?php echo "♫ O God, Beyond All Praising ♫ "; ?>
          </tr>

      </tbody>
      </table>
    </body>
</html>
