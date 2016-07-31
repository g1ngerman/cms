<?php

class Site Extends App
 {

    private $deliverHtmlFromCache;
    private $currentTemplateInfo;

    function getSite()
    {

       // 1. WHAT AND WHERE CURRENT TEMPLATE IS
       $this->currentTemplateInfo = $this->getCurrentTemplateInfo();

       // 2. COMPILE ALL OF A CONTENT WE GONNA GIVE TO AN USER
       $modules =  $this->getModulesContent();

       // 3. SHOVE ALL OF THAT CONTENT INTO THE TEMPLATE
       return  $this->compileTemplate($this->currentTemplateInfo[path],$modules);

    } // FUNC END


    // return module html from $mod_result var
    private function runModule($filePath,$showTitleBlock=true,$showTitleCont=true,$currId,$modData)
    {
                $modPath = MOD_PATH . "/" .$filePath;
                // GET MODULE CONTENT BY ITS PATH
                if (file_exists($modPath))
                {
                    // ЗАПУСК МОДУЛЯ ПО ЙОГО ШЛЯХУ
                    // result is writing down it '$module_result' on side of an included file
                    include "$modPath";
                }
                else {$mod_result = "Wrong module file path. \n";}

                // GET WRITTEN A MODULE POSITION AS A KEY AND CONTENT AS A VALUE IN ARRAY

                return $mod_result."\n";

      } // FUNC END

    private function getModulesContent($otherWheres="")
    {

        global $pageTitle,$pageDescr;


        $info = mysql_query(
                "SELECT m.id,m.name,m.phpName,m.extension,e.path,m.position,m.params,m.html
                FROM modules m,extensions e
                WHERE m.published='1' and m.extension = e.id ".$otherWheres."
                ORDER BY ordering ASC"
                ,Config::$db);

        while($showInfo = mysql_fetch_array($info))
        {

              // ід поточної сторінки

            eval($showInfo[params]);

                    //////////////////////////
                    // this small block is realy important
                    // because it determines if module will be shown or not
                    // because if post belongs to parent that was blocked to show this particular module
                    // than we need to define this parent id here
                    //
                    // it's important if it's post get modules by id of its parent
                    // because this algorithm designated <<<<< for categories only >>>>>

                    if ($this->requestedDataArr[is_category] == 0)
                    {
                        $currId = $this->requestedDataArr[parent];
                    }
                    // or then by its own id
                    elseif ($this->requestedDataArr[is_category] == 1)
                    {
                        $currId = $this->requestedDataArr[id];
                    }

                    ////////////////////
                    //
                    ////////////////////

            // if there are in module settengs some extra pages ids merge arrays:
            if ( count($modSett[extraPage]) > 0 )
            { $modSett_allPages = array_merge ($modSett[page], $modSett[extraPage]); }
            else
            { $modSett_allPages = $modSett[page];  }

            // get module content into array
            // where
            // key is template's position
            // value rendered module's html

            if ($modSett[displayMethod]=="justThese")
            {

                if (in_array ( $currId, $modSett_allPages ) == true)
                {
                    $modules["<!--{{".$showInfo[position]."}}-->"]
                    .= $this->runModule($showInfo[path],$modSett[showTitleBlock],$modSett[showTitle],$currId,$showInfo);
                }

            }
            elseif ($modSett[displayMethod]=="exceptThese")
            {

                if (in_array ( $currId, $modSett_allPages ) == false)
                {
                $modules["<!--{{".$showInfo[position]."}}-->"]
                .= $this->runModule($showInfo[path],$modSett[showTitleBlock],$modSett[showTitle],$currId,$showInfo);
                }

            }
            elseif ($modSett[displayMethod]=="everywhere")
            {
                $modules["<!--{{".$showInfo[position]."}}-->"]
                .= $this->runModule($showInfo[path],$modSett[showTitleBlock],$modSett[showTitle],$currId,$showInfo);
            }


            // запобігаєм накопиченню масива даними
            // що відносяться до інших модулів
            unset($modSett);

        } // while end

            return $modules;

    } // FUNC getModulesContent END

    // preparing html template to fill its positions by content
    // and eventualy do it by 'strtr'
    function compileTemplate($templateSource,$modules)
    {
        // get template folder path
        $folderPath = dirname($templateSource);

        // Получить содержимое файла в виде массива. В данном примере мы используем
        // обращение по протоколу HTTP для получения HTML-кода с удаленного сервера.
        $lines = file($templateSource);
        // Осуществим проход массива и выведем номера строк и их содержимое в виде HTML-кода.
        foreach ($lines as $lineNum => $line)
        {
            if (substr_count ($line, "[file:]") == 1 )
            {
                $fileNameArrVal = explode("[file:]", $line);
                $fileName = trim($fileNameArrVal[1]);
                if (file_exists( $folderPath.'/'.$fileName ))
                {
                    $template .= file_get_contents( $folderPath.'/'.$fileName );
                    $template .= "\n";
                }
                else exit("<h2 align='center'>
                            Template compilation error! <span style='color:red;'>Line: ".($lineNum+1)."</span>
                            <br />Please, contact your webmaster.
                            </h2>");
            }
            elseif (substr_count ($line, "[file:]") > 1 )
            {
                $template .= "<h2>В строке не может быть определено более одного файла!</h2>";
            }
            else $template .= $line;
        }

        // Example of $modules for next line:
        // $modules = array("template-postion" => "module-content");
        return strtr($template,$modules);
    } // FUNC compileTemplate END


    function getCurrentTemplateInfo()
    {

      $getCurrentTemplate = mysql_query(
                                  "SELECT * FROM extensions
                                  WHERE
                                  type = 'template' and
                                  params LIKE '%tplSett[defa_lt]=1;%'",
                                  Config::$db);

      $template = mysql_fetch_array($getCurrentTemplate);

      $currTemplateInfo = $template;
      $currTemplateInfo[path] = TEMPL_PATH . "/" .$template[path];

  	  return $currTemplateInfo;

    } // FUNC getCurrentTemplateInfo END

    function checkCache()
    {
        // cache status check START
        $getGetSiteOptionsQ = mysql_query("SELECT value_int FROM `options` WHERE option_name='cache'",Config::$db);
        $siteOptionsResult = mysql_fetch_array($getGetSiteOptionsQ);

         if ($siteOptionsResult[value_int] == '1')
         { return true; } else return false;

        if ($siteOptionsResult[value_int] == '1')
         {
            if (substr_count ( $_SERVER[REQUEST_URI], ".css" ) == 0 and
                substr_count ( $_SERVER[REQUEST_URI], ".js" ) == 0 and
                substr_count ( $_SERVER[HTTP_USER_AGENT], "Googlebot" ) == 0 and
                substr_count ( $_SERVER[HTTP_USER_AGENT], "YandexBot" ) == 0)
             {
                $checkCacheQr = mysql_query("
                                SELECT postLink,html
                                FROM `cache`
                                WHERE `postLink`='".$_SERVER[REQUEST_URI]."'"
                                ,Config::$db);
             }
         }
        // cache status check END

        if ( mysql_num_rows($checkCacheQr) > 0 )
         {
            $checkCacheResult = mysql_fetch_array($checkCacheQr);
            echo base64_decode($checkCacheResult[html]);
            exit();
         }
    }  // FUNC END

    function decodeCache()
    {

    }  // FUNC END

 } // CLASS END














 /*

function getModulesContent COMMENTS START

        // MODULE DISPLAY METHODS:

        // $modSett[displayMethod]="exceptThese";
        //
        // $modSett[displayMethod]="justThese";
        //
        // $modSett[displayMethod]="nowhere";
        //
        // $modSett[displayMethod]="everywhere";

function getModulesContent COMMENTS END



function compileTemplate COMMENTS START

    EXAMPLE OF TEMPLATE PARAMS:

    $tplSett[default]=1;
    $tplSett[folder]="miotraBlack";
    $tplSett[pos][]="menu";
    $tplSett[pos][]="search";
    $tplSett[pos][]="left";
    $tplSett[pos][]="content";
    $tplSett[pos][]="right";
    $tplSett[pos][]="low-left";
    $tplSett[pos][]="low-content";
    $tplSett[pos][]="low-right";
    $tplSett[pos][]="bottom";
    $tplSett[main][tplPage]="main.html";
    $tplSett[category][tplPage]="main.html";
    $tplSett[details][tplPage][details]="main.html";

function compileTemplate COMMENTS END



 */