<?php
// Include math library so string math operations can be parsed correctly
include('/home/shiba009933/chriscarini.com/organizedPhotos/includes/EvalMath.class.php');

// Setup some functions...
function convertBytetoMB($bytes){return round($bytes/(1024*1024),2);}
function GCD($a, $b)
{
	while ($b != 0)
	{
		$remainder = $a % $b;
		$a = $b;
		$b = $remainder;
	}
	return abs($a);
}
//Pass in GPS.GPSLatitude or GPS.GPSLongitude or something in that format
function getGpsCoords($exifCoord)
{
	$m = new EvalMath;
	$degrees = count($exifCoord) > 0 ? $m->evaluate($exifCoord[0]) : 0;
	$minutes = count($exifCoord) > 1 ? $m->evaluate($exifCoord[1]) : 0;
	$seconds = count($exifCoord) > 2 ? $m->evaluate($exifCoord[2]) : 0;

	//normalize
	$minutes += 60 * ($degrees - floor($degrees));
	$degrees = floor($degrees);

	$seconds += 60 * ($minutes - floor($minutes));
	$minutes = floor($minutes);

	//extra normalization, probably not necessary unless you get weird data
	if($seconds >= 60)
	{
		$minutes += floor($seconds/60.0);
		$seconds -= 60*floor($seconds/60.0);
	}
	if($minutes >= 60)
	{
		$degrees += floor($minutes/60.0);
		$minutes -= 60*floor($minutes/60.0);
	}
	return array('degrees' => $degrees, 'minutes' => $minutes, 'seconds' => $seconds);
}
function formatGPSCoords($gpsArray, $gpsDirection)
{
	// Inputs:
	// 		$gpsArray -> Array ( [degrees] => 43 [minutes] => 2 [seconds] => 35.4 )
	// 		$gpsDirection -> [N,S,E,W] - one of these.
	// Return: 43° 2' 35.4" N
	return $gpsArray['degrees']."&deg; ".$gpsArray['minutes']."' ".$gpsArray['seconds']."\" ".$gpsDirection;
}
function formatGPSTimeStamp($timeArray)
{
	// Inputs:
	// 		$timeArray -> Array ( [0] => 15/1 [1] => 50/1 [2] => 2600/100 )
	// Return: 15:50:26 GMT
	$m = new EvalMath;
	return $m->evaluate($timeArray[0]).":".$m->evaluate($timeArray[1]).":".$m->evaluate($timeArray[2])." GMT";
}
function getGoogleMapURL($EXIFGPSArray)
{
	$m = new EvalMath;
	$degrees = count($EXIFGPSArray["GPSLatitude"]) > 0 ? $m->evaluate($EXIFGPSArray["GPSLatitude"][0]) : 0;
	$minutes = count($EXIFGPSArray["GPSLatitude"]) > 1 ? $m->evaluate($EXIFGPSArray["GPSLatitude"][1]) : 0;
	$seconds = count($EXIFGPSArray["GPSLatitude"]) > 2 ? $m->evaluate($EXIFGPSArray["GPSLatitude"][2]) : 0;
	$flip = ($EXIFGPSArray["GPSLatitudeRef"] == 'W' or $EXIFGPSArray["GPSLatitudeRef"] == 'S') ? -1 : 1;
	$lat = $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	$degrees = count($EXIFGPSArray["GPSLongitude"]) > 0 ? $m->evaluate($EXIFGPSArray["GPSLongitude"][0]) : 0;
	$minutes = count($EXIFGPSArray["GPSLongitude"]) > 1 ? $m->evaluate($EXIFGPSArray["GPSLongitude"][1]) : 0;
	$seconds = count($EXIFGPSArray["GPSLongitude"]) > 2 ? $m->evaluate($EXIFGPSArray["GPSLongitude"][2]) : 0;
	$flip = ($EXIFGPSArray["GPSLongitudeRef"] == 'W' or $EXIFGPSArray["GPSLongitudeRef"] == 'S') ? -1 : 1;
	$lon = $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	return "https://maps.google.com/maps?t=h&q=loc:".$lat.",".$lon."&z=16";
}
		

$load_ext = get_loaded_extensions();
if (!in_array(exif, $load_ext))
{
	echo "Exif is NOT available on this server.";die();
}
else
{
	// EXIF extension is available...
	
	// Setup the Headers
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	// Get the photo URL from Request...
	//$photo = "http://chriscarini.com/organizedPhotos/2013/03-March/2013-03-23-Light_Orbs_with_Brigham_by_the_Lake/DSC_0195.JPG";
	//substr($photo,39) -> 2013/03-March/2013-03-23-Light_Orbs_with_Brigham_by_the_Lake/DSC_0195.JPG
	$photo=( trim($_REQUEST["q"]) );//get the q parameter from URL
	
        include("/home/shiba009933/chriscarini.com/includes/functions.inc.php");

	/* Connect to MySQL */
	$mysqlLink = connectToMySQL("organizedphotodb");
	$result = mysql_query('SELECT * FROM `photos` WHERE `fullPath` LIKE \'%'.substr($photo,39).'%\' AND exifData = 1;', $mysqlLink);	
	$num_rows = mysql_num_rows($result);
	if($num_rows == 1)
	{
		// There is a row with EXIF Data, pull from there and return that...
		$row = mysql_fetch_array($result);
		
		// Close MySQL Connection
		mysql_close($mysqlLink);
		
		// Assemble Data
		if(!empty($row['FileSize'])){$hint = $hint."<tr><th>File Size:</th><td>".$row['FileSize'].'MB'."</td></tr>";}
		if(!empty($row['Model'])){$hint = $hint."<tr><th>Camera Model:</th><td>".$row['Model']."</td></tr>";}
		if(!empty($row['XResolution']) && !empty($row['YResolution'])){$hint = $hint."<tr><th>Resolution:</th><td>".$row['XResolution']."x".$row['YResolution'].'dpi'."</td></tr>";}
		if(!empty($row['ExifImageWidth']) && !empty($row['ExifImageLength'])){$hint = $hint."<tr><th>Dimensions:</th><td>".$row['ExifImageWidth']." x ".$row['ExifImageLength'].'px'."</td></tr>";}
		if(!empty($row['DateTimeOriginal'])){if(!empty($row['SubSecTime'])){$SSTime = ".".$row['SubSecTime'];}else{$SSTime="";}$hint = $hint."<tr><th>Date Time:</th><td>".$row['DateTimeOriginal'].$SSTime."</td></tr>";}		
		if(!empty($row['ExifVersion'])){$hint = $hint."<tr><th>EXIF Version:</th><td>".$row['ExifVersion']."</td></tr>";}
		if($row['ExposureMode']){switch($row['ExposureMode']){case 0:$var = "Auto Exposure";break;case 1:$var = "Manual Exposure";break;case 2:$var = "Auto Bracket";break;default:$var = "Unknown";}$hint = $hint."<tr><th>Exposure Mode:</th><td>".$var."</td></tr>";}
		if(!empty($row['ExposureTime'])){$hint = $hint."<tr><th>Exposure:</th><td>".$row['ExposureTime'].' sec'."</td></tr>";}
		if(!empty($row['ExposureBiasValue']) && $row['ExposureBiasValue'][0] != "0"){$hint = $hint."<tr><th>Exposure Bias:</th><td>".$row['ExposureBiasValue'].' EV'."</td></tr>";}
		if(!empty($row['FNumber'])){$hint = $hint."<tr><th>F-Stop:</th><td>".'f/'.$row['FNumber']."</td></tr>";}
		if(!empty($row['ISOSpeedRatings'])){$hint = $hint."<tr><th>Film Speed:</th><td>".'ISO '.$row['ISOSpeedRatings']."</td></tr>";}
		if(!empty($row['FocalLength'])){$hint = $hint."<tr><th>Focal Length:</th><td>".$row['FocalLength'].'mm'."</td></tr>";}
		if(!empty($row['FocalLengthIn35mmFilm'])){$hint = $hint."<tr><th>35mm Focal Length:</th><td>".$row['FocalLengthIn35mmFilm'].'mm'."</td></tr>";}
		if(!empty($row['MaxApertureValue'])){$hint = $hint."<tr><th>Lens Max Aperture:</th><td>".'f/'.$row['MaxApertureValue']."</td></tr>";}
		if(!empty($row['MeteringMode'])){switch($row['MeteringMode']){case 1:$var = "Average";break;case 2:$var = "Center Weighted Average";break;case 3:$var = "Spot";break;case 4:$var = "MultiSpot";break;case 5:$var = "Pattern";break;case 6:$var = "Partial";break;case 255:$var = "Other";break;default:$var = "Unknown";}$hint = $hint."<tr><th>Metering Mode:</th><td>".$var."</td></tr>";}
		if(!empty($row['Flash'])){$hint = $hint."<tr><th>Flash:</th><td>".$row['Flash']."</td></tr>";}
		if(!empty($row['WhiteBalance'])){switch($row['WhiteBalance']){case 0:$var = "Auto";break;case 1:$var = "Manual";break;default:$var = "Unknown";}$hint = $hint."<tr><th>White Balance:</th><td>".$var."</td></tr>";}
		if(!empty($row["GPSGoogleURL"]) && !empty($row["GPSLongitude"]) && !empty($row['GPSLongitudeRef']) && !empty($row["GPSLatitude"]) && !empty($row['GPSLatitudeRef'])){$hint = $hint."<tr><th>GPS Coords:</th><td><a href=\"".$row["GPSGoogleURL"]."\" target=\"_blank\">".formatGPSCoords(getGpsCoords($row["GPSLatitude"]), $row['GPSLatitudeRef']).", ".formatGPSCoords(getGpsCoords($row["GPSLongitude"]), $row['GPSLongitudeRef'])."</a></td></tr>";}
		if(!empty($row['GPSAltitude'])){$hint = $hint."<tr><th>GPS Altitude:</th><td>".$row['GPSAltitude']."m ".($row['GPSAltitudeRef']==0?"above":"below")." Sea Level</td></tr>";}
		if(!empty($row['GPSTimeStamp'])){$hint = $hint."<tr><th>GPS Time Stamp:</th><td>".$row['GPSTimeStamp']."</td></tr>";}
		if(!empty($row['GPSSatellites'])){$hint = $hint."<tr><th>GPS Satellites:</th><td>".$row['GPSSatellites']."</td></tr>";}
		
		$hint = $hint."<tr><th colspan='2'>Data Gathered From Database</th></tr>";
	}
	else
	{
		// There is NO row with EXIF Data, try to pull data from image and store to DB...
		// If we are dealing with a valid file extension...
		if(substr($photo,-4) == ".JPG" || substr($photo,-5) == ".JPEG" || substr($photo,-4) == ".TIFF")
		{
			// Get EXIF Data from photo...
			$exif = exif_read_data($photo,0,true);
		}
		else
		{
			echo "NO PHOTO DATA for:".$photo;
		}
		
		$m = new EvalMath;
		
		// Begin to add data from EXIF...
		if(!empty($exif['FILE']['FileSize']))
		{
			#$hint = $hint."<tr><th>File Name:</th><td>".$photo."</td></tr>";
			$FileSize = convertBytetoMB($exif['FILE']['FileSize']);
			$hint = $hint."<tr><th>File Size:</th><td>".$FileSize.'MB'."</td></tr>";
		}
		/*
		Ya, we know its an image...thats kind of a given for this application...
		if(!empty($exif['FILE']['MimeType']))
		{
			$hint = $hint."<tr><th>MIME Type:</th><td>".$exif['FILE']['MimeType']."</td></tr>";
		}
		*/
		/*
		Redundant, actual EXIF Value found below...
		if(!empty($exif['COMPUTED']['ApertureFNumber']))
		{
			$hint = $hint."<tr><th>F-Stop:</th><td>".$exif['COMPUTED']['ApertureFNumber']."</td></tr>";
		}
		*/
		$SectionsFound = explode(", ", $exif['FILE']['SectionsFound']);
		// If IFD0 Section is found, pull data from it...
		if(in_array("IFD0",$SectionsFound))
		{
			if(!empty($exif['IFD0']['Model']))
			{
				$Model = $exif['IFD0']['Model'];
				$hint = $hint."<tr><th>Camera Model:</th><td>".$Model."</td></tr>";
			}
			/*
			Pointless, will show Microsoft Windows Photo Viewer 6.1.7600.16385 if image has been rotated.. :/
			if(!empty($exif['IFD0']['Software']))
			{
				$hint = $hint."<tr><th>Camera Software:</th><td>".$exif['IFD0']['Software']."</td></tr>";
			}
			*/
			if(!empty($exif['IFD0']['XResolution']) && !empty($exif['IFD0']['YResolution']))
			{
				$XResolution = $m->evaluate($exif['IFD0']['XResolution']);
				$YResolution = $m->evaluate($exif['IFD0']['YResolution']);
				$hint = $hint."<tr><th>Resolution:</th><td>".$XResolution."x".$YResolution.'dpi'."</td></tr>";
			}
			/*
			Pointless, this will show the time that the image was modified if it was rotated..
			if(!empty($exif['IFD0']['DateTime']))
			{
				$hint = $hint."<tr><th>Date Time:</th><td>".$exif['IFD0']['DateTime'].".".$exif['EXIF']['SubSecTime']."</td></tr>";
			}
			*/
		}
		// If EXIF Section is found, pull data from it...
		if(in_array("EXIF",$SectionsFound))
		{
			if(!empty($exif['EXIF']['ExifImageWidth']) && !empty($exif['EXIF']['ExifImageLength']))
			{
				$ExifImageWidth  = $exif['EXIF']['ExifImageWidth'];
				$ExifImageLength = $exif['EXIF']['ExifImageLength'];
				$hint = $hint."<tr><th>Dimensions:</th><td>".$ExifImageWidth." x ".$ExifImageLength.'px'."</td></tr>";
			}
			if(!empty($exif['EXIF']['DateTimeOriginal']))
			{
				$DateTimeOriginal = $exif['EXIF']['DateTimeOriginal'];
				$SubSecTime       = ".".$exif['EXIF']['SubSecTime'];
				$hint = $hint."<tr><th>Date Time:</th><td>".$DateTimeOriginal.$SubSecTime."</td></tr>";
			}
			if(!empty($exif['EXIF']['ExifVersion']))
			{
				$ExifVersion = $exif['EXIF']['ExifVersion'];
				$hint = $hint."<tr><th>EXIF Version:</th><td>".$ExifVersion."</td></tr>";
			}
			if($exif['EXIF']['ExposureMode'])
			{
				/*
				ExposureMode 
					This tag indicates the exposure mode set when the image was shot. In auto-bracketing mode, the camera shoots a 
					series of frames of the same scene at different exposure settings. 
				0 =  Auto exposure 
				1 =  Manual exposure 
				2 =  Auto bracket 
				Other =  reserved 
				*/
				$ExposureMode = $exif['EXIF']['ExposureMode'];
				switch($ExposureMode)
				{
					case 0:
						$var = "Auto Exposure";break;
					case 1:
						$var = "Manual Exposure";break;
					case 2:
						$var = "Auto Bracket";break;
					default:
						$var = "Unknown";
				}
				$hint = $hint."<tr><th>Exposure Mode:</th><td>".$var."</td></tr>";
			}
			if(!empty($exif['EXIF']['ExposureTime']))
			{
				$a = explode("/",$exif['EXIF']['ExposureTime']);
				$gcd = GCD($a[0], $a[1]);
				if(	($a[0]/$gcd) > ($a[1]/$gcd) ){$ExposureTime = ($a[0]/$gcd)/($a[1]/$gcd);}
				else{$ExposureTime = ($a[0]/$gcd)."/".($a[1]/$gcd);}
				$hint = $hint."<tr><th>Exposure:</th><td>".$ExposureTime.' sec'."</td></tr>";
			}
			if(!empty($exif['EXIF']['ExposureBiasValue']) && $exif['EXIF']['ExposureBiasValue'][0] != "0")
			{
				$a = explode("/",$exif['EXIF']['ExposureBiasValue']);
				$gcd = GCD($a[0], $a[1]);
				if(	($a[0]/$gcd) >= ($a[1]/$gcd) ){$ExposureBiasValue = ($a[0]/$gcd)/($a[1]/$gcd);}
				else{$ExposureBiasValue = ($a[0]/$gcd)."/".($a[1]/$gcd);}
				$hint = $hint."<tr><th>Exposure Bias:</th><td>".$ExposureBiasValue.' EV'."</td></tr>";
			}
			if(!empty($exif['EXIF']['FNumber']))
			{
				$FNumber = $m->evaluate($exif['EXIF']['FNumber']);
				$hint = $hint."<tr><th>F-Stop:</th><td>".'f/'.$FNumber."</td></tr>";
			}
			if(!empty($exif['EXIF']['ISOSpeedRatings']))
			{
				$ISOSpeedRatings = $exif['EXIF']['ISOSpeedRatings'];
				$hint = $hint."<tr><th>Film Speed:</th><td>".'ISO '.$ISOSpeedRatings."</td></tr>";
			}
			if(!empty($exif['EXIF']['FocalLength']))
			{
				$FocalLength = $m->evaluate($exif['EXIF']['FocalLength']);
				$hint = $hint."<tr><th>Focal Length:</th><td>".$FocalLength.'mm'."</td></tr>";
			}
			if(!empty($exif['EXIF']['FocalLengthIn35mmFilm']))
			{
				$FocalLengthIn35mmFilm = $exif['EXIF']['FocalLengthIn35mmFilm'];
				$hint = $hint."<tr><th>35mm Focal Length:</th><td>".$FocalLengthIn35mmFilm.'mm'."</td></tr>";
			}
			if(!empty($exif['EXIF']['MaxApertureValue']))
			{
				$MaxApertureValue = $m->evaluate($exif['EXIF']['MaxApertureValue']);
				$hint = $hint."<tr><th>Lens Max Aperture:</th><td>".'f/'.$MaxApertureValue."</td></tr>";
			}
			if(!empty($exif['EXIF']['MeteringMode']))
			{
				/*
				MeteringMode 
					The metering mode.
				0 = unknown 
				1 = Average 
				2 = CenterWeightedAverage 
				3 = Spot 
				4 = MultiSpot 
				5 = Pattern 
				6 = Partial 
				Other = reserved 
				255 = other
				*/
				$MeteringMode = $exif['EXIF']['MeteringMode'];
				switch($MeteringMode)
				{
					case 1:
						$var = "Average";break;
					case 2:
						$var = "Center Weighted Average";break;
					case 3:
						$var = "Spot";break;
					case 4:
						$var = "MultiSpot";break;
					case 5:
						$var = "Pattern";break;
					case 6:
						$var = "Partial";break;
					case 255:
						$var = "Other";break;
					default:
						$var = "Unknown";
				}
				$hint = $hint."<tr><th>Metering Mode:</th><td>".$var."</td></tr>";
			}
			if(!empty($exif['EXIF']['Flash']))
			{
				$Flash = $exif['EXIF']['Flash'];
				$hint = $hint."<tr><th>Flash:</th><td>".$Flash."</td></tr>";
			}
			/*
			SubsecTime 
				A tag used to record fractions of seconds for the DateTime tag
			
			-Moved to DateTime & DateTimeOriginal
			
			if(!empty($exif['EXIF']['SubSecTime']))
			{
				$hint = $hint."<tr><th>Sub Sec Time:</th><td>".ltrim($exif['EXIF']['SubSecTime'],'0')."</td></tr>";
			}*/
			if(!empty($exif['EXIF']['WhiteBalance']))
			{
				/*
				WhiteBalance 
					This tag indicates the white balance mode set when the image was shot.
				0 		=  Auto white balance 
				1 		=  Manual white balance 
				Other 	=  reserved 
				*/
				$WhiteBalance = $exif['EXIF']['WhiteBalance'];
				switch($WhiteBalance)
				{
					case 0:
						$var = "Auto";break;
					case 1:
						$var = "Manual";break;
					default:
						$var = "Unknown";
				}
				$hint = $hint."<tr><th>White Balance:</th><td>".$var."</td></tr>";
			}	
		}
		// If GPS Section is found, pull data from it...
		if(in_array("GPS",$SectionsFound))
		{
			if(!empty($exif['GPS']["GPSLongitude"]) && !empty($exif['GPS']['GPSLongitudeRef']) && !empty($exif['GPS']["GPSLatitude"]) && !empty($exif['GPS']['GPSLatitudeRef']))
			{
				$GPSGoogleURL    = getGoogleMapURL($exif['GPS']);
				$GPSLatitude     = $exif['GPS']["GPSLatitude"];
				$GPSLatitudeRef  = $exif['GPS']['GPSLatitudeRef'];
				$GPSLongitude    = $exif['GPS']["GPSLongitude"];
				$GPSLongitudeRef = $exif['GPS']['GPSLongitudeRef'];
				$hint = $hint."<tr><th>GPS Coords:</th><td><a href=\"".$GPSGoogleURL."\" target=\"_blank\">".formatGPSCoords(getGpsCoords($GPSLatitude), $GPSLatitudeRef).", ".formatGPSCoords(getGpsCoords($GPSLongitude), $GPSLongitudeRef)."</a></td></tr>";
				//$hint = $hint."<tr><th>GPS Coords RAW:</th><td>".print_r($exif['GPS']["GPSLatitude"],true).", ".print_r($exif['GPS']["GPSLongitude"],true)."</td></tr>";
			}
			if(!empty($exif['GPS']['GPSAltitude']))
			{
				$GPSAltitude    = $m->evaluate($exif['GPS']['GPSAltitude']);
				$GPSAltitudeRef = $exif['GPS']['GPSAltitudeRef'];
				$hint = $hint."<tr><th>GPS Altitude:</th><td>".$GPSAltitude."m ".($GPSAltitudeRef==0?"above":"below")." Sea Level</td></tr>";
			}
			if(!empty($exif['GPS']['GPSTimeStamp']))
			{
				$GPSTimeStamp = formatGPSTimeStamp($exif['GPS']['GPSTimeStamp']);
				$hint = $hint."<tr><th>GPS Time Stamp:</th><td>".$GPSTimeStamp."</td></tr>";
			}
			if(!empty($exif['GPS']['GPSSatellites']))
			{
				$GPSSatellites = ltrim($exif['GPS']['GPSSatellites'],'0');
				$hint = $hint."<tr><th>GPS Satellites:</th><td>".$GPSSatellites."</td></tr>";
			}
		}
		
		$hint = $hint."<tr><th colspan='2'>Data Gathered From Image</th></tr>";
		
		mysql_query('UPDATE `organizedphotodb`.`photos` SET `exifData` = \'1\', `FileSize` = \''.$FileSize.'\', `Model` = \''.$Model.'\', `XResolution` = \''.$XResolution.'\', `YResolution` = \''.$YResolution.'\', `ExifImageWidth` = \''.$ExifImageWidth.'\', `ExifImageLength` = \''.$ExifImageLength.'\', `DateTimeOriginal` = \''.$DateTimeOriginal.'\', `SubSecTime` = \''.$SubSecTime.'\', `ExifVersion` = \''.$ExifVersion.'\', `ExposureMode` = \''.$ExposureMode.'\', `ExposureTime` = \''.$ExposureTime.'\', `ExposureBiasValue` = \''.$ExposureBiasValue.'\', `FNumber` = \''.$FNumber.'\', `ISOSpeedRatings` = \''.$ISOSpeedRatings.'\', `FocalLength` = \''.$FocalLength.'\', `FocalLengthIn35mmFilm` = \''.$FocalLengthIn35mmFilm.'\', `MaxApertureValue` = \''.$MaxApertureValue.'\', `MeteringMode` = \''.$MeteringMode.'\', `Flash` = \''.$Flash.'\', `WhiteBalance` = \''.$WhiteBalance.'\', `GPSGoogleURL` = \''.$GPSGoogleURL.'\', `GPSLatitude` = \''.$GPSLatitude.'\', `GPSLatitudeRef` = \''.$GPSLatitudeRef.'\', `GPSLongitude` = \''.$GPSLongitude.'\', `GPSLongitudeRef` = \''.$GPSLongitudeRef.'\', `GPSAltitude` = \''.$GPSAltitude.'\', `GPSAltitudeRef` = \''.$GPSAltitudeRef.'\', `GPSTimeStamp` = \''.$GPSTimeStamp.'\', `GPSSatellites` = \''.$GPSSatellites.'\' WHERE `fullPath` = \'.'.substr($photo,38).'\';', $mysqlLink);
		
		// Close MySQL Connection
		mysql_close($mysqlLink);
	}
	if($hint!=""){$response = "<table id='EXIFDataResults'><tbody>".$hint."</tbody></table>";}
	else{$response = "<table id='EXIFDataResults'><tbody><tr><td><a>No EXIF Data was found for:".$photo."</a></td></tr></tbody></table>";}

	echo $response;//output the response
}
?>
