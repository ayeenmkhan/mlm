<?php 
ob_start();
// var_dump($_GET);exit();
// echo "new page";exit;
function export_report($data){
$datetime=date('Y-m-d H:i:s');
header("Content-Disposition: attachment; filename=\"member_reports-".$datetime.".xls\"");
header("Content-Type: application/vnd.ms-excel;");
header("Pragma: no-cache");
header("Expires: 0");
        $result= $data;
                 
           $array = array();

            $i=0;
            $sr=1;
            foreach ($result as $fkey => $res) {
                    $temp_array=Array();
                    $temp_array[]= $sr;
                    $temp_array[]= $res['firstname']." ".$res['lastname'];
                    $temp_array[]= $res['username'];
                    $temp_array[]= $res['email'];
                    $temp_array[]= $res['phone'];
                    $temp_array[]= $res['adhar_card'];
                    $temp_array[]= $res['pan_card'];
                    $temp_array[]= $res['mbrstatus'];
                    $temp_array[]= $res['level_bage'];
                // $j++;
                $array[$i]=$temp_array;
                // }
                
                $sr++;
                $i++;
            }
             // var_dump($array);exit;
$header=Array(
    1=>"Sr#",
    2=>"Name",
    3=>"Username",
    4=>"Email",
    5=>"Phone",
    6=>"Adhar Card",
    7=>"Pan Card",
    8=>"Staus",
    9=>"Leve badge"
);

$out = fopen("php://output", 'w');
fputcsv($out, $header,"\t");
foreach ($array as $data)
{
    fputcsv($out, $data,"\t");
}
fclose($out);
// redirect(SURL."employees");
exit;

}

?>