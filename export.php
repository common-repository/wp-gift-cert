<?php
		/************************************************************************************************
		*	Function: exportCertificates																*
		*	Purpose: This function provides the ability to export the	list of certs to a 				*
		*	comma separated value list (CSV) for easy inclusion in excel.								*
		*																								*
		************************************************************************************************/
		if(isset($_POST['export']) && isset($_POST['data'])) {
			$_POST = array_map( 'stripslashes_deep', $_POST ); 
				$csvoutput = htmlspecialchars_decode($_POST['data']);
				
				//define the data type being output to the browser so it prompts for Download
				$file = date("Y-m-d").".csv";
				header("Content-Description: File Transfer");
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=$file");
				header("Pragma: no-cache");
				header("Expires: 0");
				print $csvoutput;
				exit(0); 
			}
?>