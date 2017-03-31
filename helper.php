<?php

  function getRandomBinary($l){
    $bin="";
    for ($i=0;$i<$l;$i++){
      $bin = $bin . rand(0,1);
    }
    return($bin);
  }

  function echoStr($str){
    echo("\n");
    echo($str);
    echo("\n");
  }

  function serialTest($testSeq, $n, $a){
    $l = strlen($testSeq);
    for($i=0;$i<pow(2,$n);$i++)
      $serialFreq[$i] = 0;
    $count = 0;

    for ($i=0;$i<$l;$i=$i+$n){
      if ($i+$n-1<$l){
        $ind = "";
        for ($j=0;$j<$n;$j++){
          $ind = $ind.$testSeq[$i+$j];
        }
        $serialFreq[bindec($ind)]++;
        $count ++;
      }
    }

    $theoreticFreq = $count / pow(2,$n);
    $xi = 0;

    for($i=0;$i<pow(2,$n);$i++){
      $xi+=pow($serialFreq[$i]-$theoreticFreq,2)/$theoreticFreq;
      // echoStr($serialFreq[$i]);
    }

    if ($xi > $a[1]){
      if ($xi < $a[0]){
        $str = "Good";
      }else{
        $str = "Bad";
      }
    }else{
      $str = "Something wrong";
    }

    // echoStr($xi);
    return $str;
  }

  function corelationTest($testSeq, $shift){
    $a = 0.05;
    $S = 0;
    $l = strlen($testSeq);
    for($i=0;$i<$l;$i++)
      $S+=bindec($testSeq[$i]);
    $Rkl = -1/($l-1)-(2/($l-2))*sqrt(($l*($l-3))/($l+1));
    $Rkr = 1/($l-1)+(2/($l-2))*sqrt(($l*($l-3))/($l+1));

    $temp1 = 0;
    for($i=0;$i<$l;$i++)
      $temp1+=bindec($testSeq[$i])*bindec($testSeq[($i+$shift)%$l]);

    $temp2 = 0;
    for($i=0;$i<$l;$i++)
      $temp2+=pow(bindec($testSeq[($i+$shift)%$l]),2);

    $temp3 = 0;
    for($i=0;$i<$l;$i++)
      $temp3+=pow(bindec($testSeq[$i]),2);

    $temp1 *=$l;
    $temp2 *=$l;
    $temp2-=pow($S,2);
    $temp3 *=$l;
    $temp3-=pow($S,2);

    $R = ($temp1 - pow($S,2))/sqrt(($temp2*$temp3));
    // echoStr($R);
    if ($R>=$Rkl && $R<=$Rkr){
      $str = "Good";
    }else{
      $str = "Bad";
    }
    return($str);
  }

  function getRegisterSeq($register, $pol, $filename, $n){
    $file = fopen($filename,'w+');
    $l = strlen($register);
    echoStr("INIT REGISTER WITH LENGTH ".$l.": ".$register."\n");
    for ($i=0;$i<$n;$i++){
      fwrite($file,$register[$l-1]);
      $temp=0;
      foreach ($pol as $p) {
        $temp+=bindec($register[$l-$p]);
      }
      $temp = $temp%2;
      for($j=$l-1;$j>0;$j--){
        $register[$j]=$register[$j-1];
      }
      $register=substr_replace($register,decbin($temp),0,1);
    }
    echo("\n");
    echo("DONE");
    echo("\n");
    fclose($file);
  }

  function textFileToBinFile($filename){
    $str = file_get_contents($filename);
    $length = strlen($str);
    $result = '';
    for ($i = 0; $i < $length; $i++) {
      $result .= str_pad(decbin(ord($str[$i])), 8, '0', STR_PAD_LEFT);
    }
    $temp = explode('.',$filename);
    $temp = explode("/",$temp[0]);
    $temp = explode("_",$temp[count($temp)-1]);
    $filename = "".$temp[0]."_bin.txt";
    $file = fopen($filename,'w+');
    fwrite($file, $result);
    fclose($file);
    return $filename;
  }

  function binFileToTextFile($filename,$exp){
    $str = file_get_contents($filename);
    $length = strlen($str);
    $result = '';
    for ($i = 0; $i < $length-8; $i+=8) {
        $temp="";
        for($j=0;$j<8;$j++)
          $temp = $temp.$str[$i+$j];
        $result .= chr(bindec($temp));
    }
    $temp = explode('.',$filename);
    $temp = explode("/",$temp[0]);
    $temp = explode("_",$temp[count($temp)-1]);
    $filename = "".$temp[0]."_decoded".$exp.".txt";
    $file = fopen($filename,'w+');
    fwrite($file, $result);
    fclose($file);
    return $filename;
  }

  function encodeBinFile($filename){
    $text = file_get_contents($filename);
    $n = strlen($text);
    $l = 32;
    $pol = [1,27,28,32];
    $init = getRandomBinary($l);
    $file = fopen("key.txt",'w+');
    fwrite($file, $init);
    fclose($file);
    $filename2 = "forEncode.txt";
    getRegisterSeq($init, $pol, $filename2 , $n);
    $seq = file_get_contents($filename2);
    $result="";
    for ($i=0;$i<$n;$i++){
      $result = $result.(decbin((bindec($text[$i])+bindec($seq[$i]))%2));
    }
    $temp = explode('.',$filename);
    $temp = explode("/",$temp[0]);
    $temp = explode("_",$temp[count($temp)-1]);
    $filename = "".$temp[0]."_encoded.txt";
    $file = fopen($filename,'w+');
    fwrite($file, $result);
    fclose($file);
    return $filename;
  }

  function decodeBinFile($filename,$key){
    $text = file_get_contents($filename);
    $n = strlen($text);
    $l = 32;
    $pol = [1,27,28,32];
    $init=file_get_contents($key);
    $filename2 = "forDecode.txt";
    getRegisterSeq($init, $pol, $filename2 , $n);
    $seq = file_get_contents($filename2);
    $result="";
    for ($i=0;$i<$n;$i++){
      $result = $result.(decbin((bindec($text[$i])+bindec($seq[$i]))%2));
    }
    $temp = explode('.',$filename);
    $temp = explode("/",$temp[0]);
    $temp = explode("_",$temp[count($temp)-1]);
    $filename = "".$temp[0]."_decoded.txt";
    $file = fopen($filename,'w+');
    fwrite($file, $result);
    fclose($file);
    return $filename;
  }

?>
