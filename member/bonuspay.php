<?php
include_once('../common/init.loader.php');
$counter= $_POST['counter'];
$user_id= $_POST['user_id'];
$mined_amount= $_POST['earnedAmount'];
$wallet= $_POST['wallet'];
$mbrstr = json_decode($_POST['mbrstr'],true);
// $decodeArray= json_decode($_POST['mbrstr']);
// $mbrstr= array() $decodeArray;
// 
// echo "<pre>";print_r($mbrstr['idspr']);exit();

$restult=updateBonusCounter($counter,$wallet,$user_id);

            // Earning commission list
   $first=($mined_amount/100)*8;
   $second=($mined_amount/100)*5;
   $third=($mined_amount/100)*2;

  $commission_percentage= $first.','.$second.','.$third;
                
            $sprstr = getmbrinfo($mbrstr['idspr']);
    // echo "<pre>";print_r($sprstr);
    
            $getcmlist = getcmlist($sprstr['mpid'], $mbrstr['sprlist'], $commission_percentage, $mbrstr);
          // echo "<pre>";print_r($getcmlist);
            addcmlist('Bonus Commission', 'TIER', $getcmlist, $mbrstr);
echo 1;
// echo json_encode($commission_percentage);
?>
