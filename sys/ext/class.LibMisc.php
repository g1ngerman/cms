<?php

class LibMisc{

/**
 *
 * Loads all classes needed; use preferably with 'spl_autoload_register('this_loader_Function_Name');'
 * @param $class - get filled automaticly
 *
 */

static function loadClass($class) {

    // SET CONFIGURATION PARAMETERS
    include_once"sys/core/class.Config.php";
    Config::defineConstants();

    // SEARCH FOR NEEDED CLASS
    switch($class)
    {

        case file_exists('sys/core/class.' . $class . '.php') : include_once 'sys/core/class.' . $class . '.php';
        break;

        case file_exists('sys/core/interfaces/class.' . $class . '.php') : include_once 'sys/core/interfaces/class.' . $class . '.php';
        break;

        case file_exists('sys/abs/class.' . $class . '.php') : include_once 'sys/abs/class.' . $class . '.php';
        break;

        case file_exists('sys/ext/class.' . $class . '.php') : include_once 'sys/ext/class.' . $class . '.php';
        break;

// GET CLASS FROM SUBDIRECTORY (LIKE 'SITE.COM/ADMIN' ETC)

        case file_exists('../sys/core/class.' . $class . '.php') : include_once '../sys/core/class.' . $class . '.php';
        break;

        case file_exists('../sys/core/interfaces/class.' . $class . '.php') : include_once '../sys/core/interfaces/class.' . $class . '.php';
        break;

        case file_exists('../sys/ext/class.' . $class . '.php') : include_once '../sys/ext/class.' . $class . '.php';
        break;

// GET CLASS FOR MODULES

        case file_exists(MOD_PATH.'/mod_'.$class.'/class.' . $class . '.php') : include_once MOD_PATH.'/mod_'.$class.'/class.' . $class . '.php';
        break;


        default: exit("Front-end Loader (class.LibMisc) error:  _CLASS '<strong>$class</strong>' NOT FOUND_");
    }


 } // FUNC loadClass END

/**
 *
 * Gets you 'fetched' array from data base by sql query sting
 * @param $query string
 *
 */

function getListOfSmth($query){

        $info = mysql_query($query);

        while($showInfo = mysql_fetch_array($info))
        {
            $list[] = $showInfo;
        }

  	  return $list;

      } // FUNC END
	

/**
 *
 * Updates ini parameters in asigned ini-file
 * @param $search string - exact string from ini-file with actual parameter, name and value
 * @param $replace string - new parameter, name and value (ini-string)
 * @param $iniPath string - path to the ini-file
 * @return an array from updated ini-file
 *
 */
      function setNewIniParam($current,$candidate,$iniPath){
			
			// [navigation]
			$readMore = 'read_more = '.$iniArr[read_more];
			$prev = 'prev = '.$iniArr[prev];
			$next = 'next = '.$iniArr[next];
			// [general]
			$homePage = 'home_page = '.$iniArr[home_page];
			$homePageRus = 'home_page_rus = '.$iniArr[home_page_rus];
			$email = 'email = '.$iniArr[email];
			
			switch($current){
				// [navigation]
				case 'read_more = '.$current: $new = 'read_more = '.$candidate;
				break;
				case 'prev = '.$current: $new = 'prev = '.$candidate;
				break;
				case 'next = '.$current: $new = 'next = '.$candidate;
				break;
				// [general]
				case 'home_page = '.$current: $new = 'home_page = '.$candidate;
				break;	
				case 'home_page_rus = '.$current: $new = 'home_page_rus = '.$candidate;
				break;
				case 'email = '.$current: $new = 'email = '.$candidate;
				break;
				default: throw new Exception ('Cant find match among cases in setNewIniParam function');
			}
			
			return $this->updateIni($current,$new,$iniPath);

      } // func updateIni - END
	  
/**
 *
 * Updates ini parameters in asigned ini-file
 * @param $search string - exact string from ini-file with actual parameter, name and value
 * @param $replace string - new parameter, name and value (proper ini-string)
 * @param $iniPath string - path to the ini-file
 * @return an array from updated ini-file
 *
 */
      public function updateIni($search,$replace,$iniPath){
			
			/* $iniFile = file_get_contents($iniPath);
			
			  //'\b' pattern unfortunatly cannot be recognized by 'preg_replace()' for cyrillic symboles
			
			  $newIni = preg_replace ( "/\b".$search."\b/", $replace, $iniFile); */
			
			$iniFile = file($iniPath);
			
			foreach($iniFile as $param){
				$newIni .= (mb_substr_count($param,$search) === 0 )? $param : $replace."\n";
			}

			file_put_contents($iniPath,$newIni);

			return parse_ini_file ( $iniPath );


      } // func updateIni - END	  

/**
 *
 * Adjust display of time to custom GMT preferences
 * @param $gmtime TIMESTAMP - date and time by GMT, as in "2016-03-27 00:15:58"
 * @param $gmtShift STRING - GMT time shift, as in "+0300"
 * @param $mask STRING - mask for the result, as in "Y-m-d H:i:s"
 * @return TIMESTAMP
 *
 */
      function getCustomGMTime($gmtime,$gmtShift,$mask,$gmt="shift"){

         $datetime = explode(' ',$gmtime);
         $date = explode('-',$datetime[0]);
         $time = explode(':',$datetime[1]);

         $sign = substr($gmtShift,0,1);
         $hour = substr($gmtShift,1,2);
         $min  = substr($gmtShift,3,2);



         if ($sign == '+'){
            return date($mask, mktime($time[0]+$hour, $time[1]+$min, $time[2],
                                    $date[1],$date[2],$date[0]) );
         }
         elseif ($sign == '-'){
            return date($mask, mktime($time[0]-$hour, $time[1]-$min, $time[2],
                                    $date[1],$date[2],$date[0]) );
         }
         else{
            return date($mask, mktime($time[0], $time[1], $time[2],
                                    $date[1],$date[2],$date[0]) );
         }


      } // func getCustomGMTime - END

/**
 *
 * Adjust display of time to custom GMT preferences
 * @param $customGMTime TIMESTAMP - date and time by GMT, as in "2016-03-27 00:15:58"
 * @param $gmtShift STRING - GMT time shift, as in "+0300"
 * @param $mask STRING - mask for the result, as in "Y-m-d H:i:s"
 * @return TIMESTAMP
 *
 */
      function getOriginalGMTime($customGMTime,$gmtShift,$mask = "Y-m-d H:i:s"){

         $datetime = explode(' ',$customGMTime);
         $date = explode('-',$datetime[0]);
         $time = explode(':',$datetime[1]);

         $sign = substr($gmtShift,0,1);
         $hour = substr($gmtShift,1,2);
         $min  = substr($gmtShift,3,2);



         if ($sign == '+'){
            return date($mask, mktime($time[0]-$hour, $time[1]-$min, $time[2],
                                    $date[1],$date[2],$date[0]) );
         }
         elseif ($sign == '-'){
            return date($mask, mktime($time[0]+$hour, $time[1]+$min, $time[2],
                                    $date[1],$date[2],$date[0]) );
         }
         else{
            return date($mask, mktime($time[0], $time[1], $time[2],
                                    $date[1],$date[2],$date[0]) );
  }


      } // func end
      function __construct(){
                          echo "<h3>LibMisc</h3>";
      } // func end

} // class LibMisc - END