<?php
  require("helper.php");

  $a[2][0] = 6.251;
  $a[2][1] = 0.584;
  $a[3][0] = 12.017;
  $a[3][1] = 2.833;
  $a[4][0] = 22.307;
  $a[4][1] = 8.547;
  $shifts = [1,2,8,9];

  $filename = textFileToBinFile("text.txt");

  $testSeq = file_get_contents($filename);
  foreach($shifts as $shift){
    $result = corelationTest($testSeq,$shift);
    echoStr("------DONE CORELATIONTEST FOR ".$shift." shift of seq with result ".$result."------");
  }
  for($i=2;$i<5;$i++){
    $result = serialTest($testSeq, $i, $a[$i]);
    echoStr("------DONE SERIALTEST FOR ".$i." length of serial with result ".$result."------");
  }

  $filename = encodeBinFile($filename);
  binFileToTextFile($filename,'1');
  $testSeq = file_get_contents($filename);
  foreach($shifts as $shift){
    $result = corelationTest($testSeq,$shift);
    echoStr("------DONE CORELATIONTEST FOR ".$shift." shift of seq with result ".$result."------");
  }
  for($i=2;$i<5;$i++){
    $result = serialTest($testSeq, $i, $a[$i]);
    echoStr("------DONE SERIALTEST FOR ".$i." length of serial with result ".$result."------");
  }

  $filename = decodeBinFile($filename,"key.txt");
  binFileToTextFile($filename,'2');
?>
