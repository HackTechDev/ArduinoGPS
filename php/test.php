<?php

// Example: $GPGGA,171544.000,4929.9587,N,00546.6857,E,1,6,1.59,348.7,M,47.6,M,,*56
// Result: +49° 29.9587', +005° 46.6857'

echo "Type 'GPGGA' NMEA sentence:\n";
$nmeaSentence = readline("NMEA sentence: ");

echo $nmeaSentence . "\n";

echo gpgga2latlon($nmeaSentence) . "\n";
echo gpgga2degree($nmeaSentence) . "\n";


function gpgga2latlon($sentence) {
    $data = explode(",", $sentence);
    $identifier = $data[0];
    $time = $data[1];
    $latitude = $data[2];
    $latitudeDirection = $data[3];
    $longitude = $data[4];
    $longitudeDirection = $data[5];
    $fixQuality = $data[6];
    $NumberSatellites = $data[7];
    $HorizontalDilutionPrecision = $data[8];
    $altitudeMSL = $data[9];
    $altitudeMSLUnity = $data[10];
    $HeightGeoid = $data[11];
    $HeightGeoidUnity = $data[12];
    $TimeSinceDGPSUpdate = $data[13];
    $DGPSReferenceStation = $data[13];
    $Checksum = $data[14];

    if($latitudeDirection == "N" or $longitudeDirection == "E") {
        $latSign = $longSign = "+";
    }

    if($latitudeDirection == "S" or $longitudeDirection == "W") {
        $latSign = $longSign = "-";
    }

    $dotPos = strpos($latitude, ".");
    
    $lat = substr($latitude, 0, $dotPos - 2);
    $latTime = substr($latitude, $dotPos - 2);

    $dotPos = strpos($longitude, ".");

    $long = substr($longitude, 0, $dotPos - 2);
    $longTime = substr($longitude, $dotPos - 2);

    $result = $latSign . $lat . "° " . $latTime . "', " . $longSign . $long . "° " . $longTime . "'";

    return $result;
}


// http://tvaira.free.fr/bts-sn/activites/activite-peripherique-usb/conversions.html
// https://stackoverflow.com/questions/36254363/how-to-convert-latitude-and-longitude-of-nmea-format-data-to-decimal
// https://www.raspberrypi.org/forums/viewtopic.php?t=175163

function gpgga2degree($sentence) {
    $data = explode(",", $sentence);
    $identifier = $data[0];
    $time = $data[1];
    $latitude = $data[2];
    $latitudeDirection = $data[3];
    $longitude = $data[4];
    $longitudeDirection = $data[5];
    $fixQuality = $data[6];
    $NumberSatellites = $data[7];
    $HorizontalDilutionPrecision = $data[8];
    $altitudeMSL = $data[9];
    $altitudeMSLUnity = $data[10];
    $HeightGeoid = $data[11];
    $HeightGeoidUnity = $data[12];
    $TimeSinceDGPSUpdate = $data[13];
    $DGPSReferenceStation = $data[13];
    $Checksum = $data[14];

    if($latitudeDirection == "N" or $longitudeDirection == "E") {
        $latSign = $longSign = "+";
    }

    if($latitudeDirection == "S" or $longitudeDirection == "W") {
        $latSign = $longSign = "-";
    }

    $dotPos = strpos($latitude, ".");
    
    $lat = substr($latitude, 0, $dotPos - 2);
    $latTime = substr($latitude, $dotPos - 2);

    $dotPos = strpos($longitude, ".");

    $long = substr($longitude, 0, $dotPos - 2);
    $longTime = substr($longitude, $dotPos - 2);

    $latDegree = $lat + $latTime / 60;
    $longDegree = $long + $longTime / 60;

    $result = $latDegree. ", " . $longDegree;

    return $result;
}



?>
