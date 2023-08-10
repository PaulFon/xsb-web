<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title> Title</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- This line is required to make it a "responsive" page -->

<?php
  /*
  Author: Paul Fontaine
  Date created:
  Version 1.0 mm/dd/yyyy
  Program Description:
  */


  // prints e.g. 'Current PHP version: 4.1.1'
  echo 'Current PHP version: ' . phpversion();

  // prints e.g. '2.0' or nothing if the extension isn't enabled
  echo phpversion('tidy');

?>



</body>
</html>
