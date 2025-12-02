<?php
$mystring = 'abc';
$findme   = 'a';
$pos = strpos($mystring, $findme);
$order_id = '12_20200406_1_M1';
echo $order_id;
        $rejection_Count_position =(int) (strpos($order_id,'R') +1);
       // $rejection_Count = ((int) $order_id[$rejection_Count_position]) +1;
       // $order_id[$rejection_Count_position] = $rejection_Count;
echo $rejection_Count_position;
echo $order_id;

?>