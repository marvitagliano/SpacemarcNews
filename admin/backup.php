<?php

/*****************************************************************
 *  Spacemarc News
 *  Author and copyright (C): Marcello Vitagliano
 *  Web site: www.spacemarc.it
 *  License: GNU General Public License
 *
 *  This program is free software: you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation, either version 3
 *  of the License, or (at your option) any later version.
 *****************************************************************/
 
if (session_id() == '') {
    session_start();
}

if (basename($_SERVER['SCRIPT_NAME']) == 'backup.php' || $_SESSION['livello_id'] != 1 || !isset($_SESSION['loggato'])) {
    die("Error");
}

if (isset($_POST['selected_tbl'])) {
		$selected_tbl = $_POST['selected_tbl'];
	}
	else {
		$selected_tbl = 'd';
	}

	$tab_permesse = @array(
		$tab_news,
		$tab_utenti,
		$tab_config,
		$tab_livelli,
		$tab_categorie,
		$tab_commenti,
		$tab_ban,
		$tab_tags,
		$tab_tags_rel
	);

	foreach ($selected_tbl as $st) {
		
		if (!in_array($st, $tab_permesse)) {
			die("No table selected");
		}
	}
		
	function backup_database( $directory, $outname, $dbhost, $dbuser, $dbpass, $dbname ) {
		global $lang;
		
	$selected_tbl = '\'' . implode("', '", $_POST['selected_tbl']) . '\'';
		
	$mysqli = @new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	
	if( $mysqli->connect_error ) {
		print_r( $mysqli->connect_error );
		return false;
	}

	$dir = $directory;
    $bck_name = $outname;
    $fullname = $dir . '/' . $bck_name . '.sql.gz';

    if (!$mysqli->error) {

		$sql = "SHOW TABLES FROM `$dbname` WHERE `Tables_in_$dbname` IN ($selected_tbl)";
		$show = $mysqli->query($sql);
		while ( $r = $show->fetch_array() ) {
			$tables[] = $r[0];
		}

	if (!empty($tables)) {

  $return = '';
  foreach( $tables as $table ) {
    $result     = $mysqli->query('SELECT * FROM ' . $table);
    $num_fields = $result->field_count;
    $row2       = $mysqli->query('SHOW CREATE TABLE ' . $table);
    $row2       = $row2->fetch_row();
    $return    .= 
"
-- ---------------------------------------------------------
--
-- Struttura della tabella `{$table}`
--
-- ---------------------------------------------------------

DROP TABLE IF EXISTS " . $table . ";
" . $row2[1] . ";\n";

    for ($i = 0; $i < $num_fields; ++$i) {

      $n = 1 ;
      while($row = $result->fetch_row()) { 

        if( $n++ == 1 ) {
          $return .= 
"
--
-- Dump dei dati per la tabella `{$table}`
--

";  

        $array_field = array();
         while( $field = $result->fetch_field() ) {
          $array_field[] = '`'. $field->name . '`';
        }
        
        $array_f[$table] = $array_field;

        $array_field = implode(', ', $array_f[$table]);

          $return .= "INSERT INTO `{$table}` ({$array_field}) VALUES\n(";
        } else {
          $return .= '(';
        }
        
        for($j=0; $j<$num_fields; $j++) {
          
          $search = array(
            "\x0a",
            "\x0d",
            "\x1a"
			);
		  $replace = array(
            "\\n",
            "\\r",
            "\Z"
			);
          
          if (isset($row[$j])) { 
				$return .= is_numeric($row[$j]) ? $row[$j] : "'" . str_replace($search, $replace, addslashes($row[$j])) . "'";
			} else { 
				$return .= 'NULL'; 
			}
			
          if ($j<($num_fields-1)) { $return .= ', '; }
        }
          $return .= "),\n";
      }

      @preg_match("/\),\n/", $return, $match, false, -3);
      if (isset($match[0])) {
        $return = substr_replace( $return, ";\n", -2);
      }

    }
    
      $return .= "\n";
  }
  
$phpversion = (!@phpversion()) ? "N/A" : phpversion();
$return = 
"-- 
-- Host: " . $mysqli->host_info . "
-- Generation Time: " . date('F d, Y \a\t H:i A ( e )') . "
-- Server version: " . $mysqli->server_info . "
-- PHP version: " . $phpversion . "
-- Database: `" . $dbname . "`
-- 

" . $return . "
";

# end values result

    @ini_set('zlib.output_compression','Off');
    $gzipoutput = gzencode($return, 6);
    
if (isset($_POST['bck']) && $_POST['bck'] == 'download') {

    $result = false;   
    header('Content-Type: application/x-gzip');
    header('Content-Description: File Transfer');
    header('Content-Length: ' . strlen($gzipoutput));
    header('Content-Disposition: attachment; filename="' . $bck_name . '_' . date("d-m-Y") . '.sql.gz' . '"');
    header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    header('Connection: Keep-Alive');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Pragma: no-cache');
    
    echo $gzipoutput;
    
    } else {
		
		if (@file_put_contents($fullname, $gzipoutput)) {
			
			$result = $bck_name;
  
		} else {
			
			$result = '<span style="color: rgb(255, 0, 0);">' . $lang['backup_save_error'] . '</span>';

		}
	}	

       } else {

         $result = '<span style="color: rgb(255, 0, 0);">' . $lang['backup_query_error'] . '</span>' . $mysqli->error;
       
       }
     }

 if( $mysqli && ! $mysqli->error ) {
      @$mysqli->close();
 }

  return $result;
}
?>
