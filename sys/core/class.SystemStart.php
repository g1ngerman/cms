<?php

class SystemStart{


  function __construct(){

            // SET CONFIGURATION PARAMETERS
            $this->include_levelcorrected("sys/core/class.Config.php");
            Config::defineConstants();

           spl_autoload_register('SystemStart::loadClass');

  } // FUNC __construct END

  static function loadClass($class) {

      // SEARCH FOR NEEDED CLASS

      switch($class)
      {
          // 1
          case file_exists(SYS_ABS.'/class.' . $class . '.php') : include_once SYS_ABS.'/class.' . $class . '.php';
          break;

          // 2
          case file_exists(SYS_CORE.'/class.' . $class . '.php') : include_once SYS_CORE.'/class.' . $class . '.php';
          break;

          // 3
          case file_exists(SYS_ITRF.'/class.' . $class . '.php') : include_once SYS_INRF.'/class.' . $class . '.php';
          break;

          // 4
          case file_exists(SYS_EXT.'/class.' . $class . '.php') : include_once SYS_EXT.'/class.' . $class . '.php';
          break;



  // GET CLASS FOR MODULES

          case file_exists(MOD_PATH.'/mod_'.$class.'/class.' . $class . '.php') : include_once MOD_PATH.'/mod_'.$class.'/class.' . $class . '.php';
          break;


          default: exit("SystemStart Front-end Loader:  _CLASS '<strong>$class</strong>' NOT FOUND_");
      }


   } // FUNC loadClass END

static function getLevelCorrected($path,$cycleMaxTimes = 30){

        // Check in if function's got path to a file with proper extension; START
        $aproved_extension = array('php','inc');

        $pathinfo = pathinfo($path);

        if (!in_array ($pathinfo[extension], $aproved_extension )){
            throw new Exception ('The extension "'.$pathinfo[extension].'" is not supported in this function.
            <h3>Details</h3>
            <h4>Path pretendent: '.$pathinfo[dirname].'/'.$pathinfo[basename].'/'.$pathinfo[extension].'</h4>
            ');
        }
        // Proper extension check-in; END



        if (file_exists($path)){ return $path; }

        else {// MAIN BODY of this function; START

            if ($path{0} == '/' or substr_count($path,'\\') > 0 ){
                        throw new Exception ('You can not use slash "/" at the start of a path in the param for this fuction; as well as backslashes "\" are not supported in here.');
                    } else {


                         // lowering level for-cycle; START

                        for ($level="", $for2=0; file_exists($level.$path) != true; $for2++)
                             {
                                $level .= "../";

                                // to prevent running cycle with no stop
                                if ($for2 >= $cycleMaxTimes) {

                                        $uppingLevelSuccess = false;

                                        // paths with '../' can not possibly need instructions contained below
                                        if (substr($path,0,3) != '../') {

                                            // upping (cuting '../', one by one) level for-cycle; START

                                            for ($for1=0; file_exists(substr($path,3)) != true;$for1++)
                                                 {


                                                    if (substr($path,0,3) == '../') { $path = substr($path,3);  }
                                                    else { $loweringLevelSuccess = false; break; }

                                                    // to prevent running cycle with no stop
                                                    if ($for1 >= $cycleMaxTimes) { $loweringLevelSuccess = false; break; }

                                                 }  // upping level for-cycle; END

                                        } else { $loweringLevelSuccess = false; break; }

                                            break;
                                } // prevention body END


                             }  // lowering level for-cycle; END


                    } // else if no slashes where no need to END

                    if ($loweringLevelSuccess === false and $uppingLevelSuccess === false){
                        throw new Exception ('Both Lowering and Upping level "for-cycles" failed: File with "'.$path.'" path has not been found');
                    } elseif ($uppingLevelSuccess !== false ){
                        return $level.$path;
                    } elseif ($loweringLevelSuccess !== false ){
                        return $path;
                    }

        } // MAIN BODY of this function; END


   } // FUNC getLevelCorrected END

/**
 * tries to include a file adding or cutting(30 times max by default)
 * level prefix like '../' to or from a path like 'dir/dir1/file.php'
 * until includes the file, otherwise throws exception;
 * excepted extensions: .inc,.php; but you can change them
 * by changing the array right at the start of function body
 * @param $path string
 * @param $cycleMaxTimes int
 * @return boolen - true if succsessfuly and throw exception if false
 */

   function include_levelcorrected($path,$cycleMaxTimes = 30){

        // Check in if function's got path to a file with proper extension; START
        $aproved_extension = array('php','inc');

        $pathinfo = pathinfo($path);

        if (!in_array ($pathinfo[extension], $aproved_extension )){
            throw new Exception ('The extension "'.$pathinfo[extension].'" is not supported in this function.');
        }
        // Proper extension check-in; END



        if (file_exists($path)){ include_once $path; return true; }

        else {// MAIN BODY of this function; START

            if ($path{0} == '/' or substr_count($path,'\\') > 0 ){
                        throw new Exception ('You can not use slash "/" at the start of a path in the param for this fuction; as well as backslashes "\" are not supported in here.');
                    } else {


                         // lowering level for-cycle; START

                        for ($level="", $for2=0; (@include_once $level.$path) != true; $for2++)
                             {
                                $level .= "../";

                                // to prevent running cycle with no stop
                                if ($for2 >= $cycleMaxTimes) {

                                        $uppingLevelSuccess = false;

                                        // paths with '../' can not possibly need instructions contained below
                                        if (substr($path,0,3) != '../') {

                                            // upping (cuting '../', one by one) level for-cycle; START

                                            for ($for1=0; (@include_once substr($path,3)) != true;$for1++)
                                                 {


                                                    if (substr($path,0,3) == '../') { $path = substr($path,3);  }
                                                    else { $loweringLevelSuccess = false; break; }

                                                    // to prevent running cycle with no stop
                                                    if ($for1 >= $cycleMaxTimes) { $loweringLevelSuccess = false; break; }

                                                 }  // upping level for-cycle; END

                                        } else { $loweringLevelSuccess = false; break; }

                                            break;
                                } // prevention body END


                             }  // lowering level for-cycle; END


                    } // else if no slashes where no need to END

                    if ($loweringLevelSuccess === false and $uppingLevelSuccess === false){
                        throw new Exception ('Both Lowering and Upping level "for-cycles" failed: File with "'.$path.'" path has not been found');
                    } else return true;

        } // MAIN BODY of this function; END


   } // FUNC correctLeveledInclude END



} // CLASS SystemStart END