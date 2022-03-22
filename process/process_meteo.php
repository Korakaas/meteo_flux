<?php
require('class/MeteoData.php');
$meteoData = new meteoData;
$meteoDatas = $meteoData->getMeteoData('http://localhost/formation/devoir03_02/fluxmeteo.html');
$savedata = $meteoData->saveMeteoData($meteoDatas);
$today = date("Y-m-d");
$displaydatas = $meteoData->getj1j2Meteo($today);
foreach ($displaydatas as $displadata )
{
    echo $displadata . "<br>\r\n";
}