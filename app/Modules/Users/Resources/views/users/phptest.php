<?php
$mosaicKey = 'pM3-y@zEn*mp0+2Xq';
$decryptedMosaicKey = 'pM3-y@zEn*mp0+2Xq' . '5';

$keyPortion = substr( $decryptedMosaicKey,0,strlen($mosaicKey));
$minutesPortion = substr( $decryptedMosaicKey,strlen($mosaicKey));

echo ' key is '. $keyPortion . ' minutes is ' . $minutesPortion ;