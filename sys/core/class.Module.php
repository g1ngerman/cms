<?php
	
class Module Extends App
 {
    function testModule()
    {
        echo" <strong>Module:</strong> ";
        echo " ".ADMIN_ROOT_REL." ";
    }

    function compileTemplate($templateSource,$modules)
    {
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
                if (file_exists( TEMPL_PATH . "orangeDarkGray/".$fileName ))
                {
                    $template .= file_get_contents( TEMPL_PATH . "orangeDarkGray/".$fileName );
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
    } // FUNC END

    function writeModule($filePath,$showTitleBlock=true,$showTitleCont=true,$currId,$modData)
    {

                $modPath = MOD_PATH . $filePath;
                // GET MODULE CONTENT BY ITS PATH
                if (file_exists($modPath))
                {
                    // ЗАПУСК МОДУЛЯ ПО ЙОГО ШЛЯХУ
                    include"$modPath";
                    // ??? ВИКОНАННЯ МОДУЛЯ ???
                    //     НАДАННЯ ЙОМУ ПАРАМЕТРІВ
                }
                else {$mod_result = "Wrong module file path. \n";}

                // GET WRITTEN A MODULE POSITION AS A KEY AND CONTENT AS A VALUE IN ARRAY

                return $mod_result."\n";

      } // FUNC END

    function getModulesContent($db,$otherWheres="")
    {

        global $pageTitle,$pageDescr;

        // $modSett[displayMethod]="exceptThese";
        //
        // $modSett[displayMethod]="justThese";
        //
        // $modSett[displayMethod]="nowhere";
        //
        // $modSett[displayMethod]="everywhere";


        // if no id, no alias give $currId home page id (0)
        if (empty($currId) )
        {
            $currId = 0;
        }

        //echo"<h3 style='color:silver;'>$currId</h3>";
        // дати сюда ід статті, категорії тощо
        // щоб поняти чи модуль дозволено відображати тут

        include_once"modules/func.writeModule.php";


        $info = mysql_query(
                "SELECT id,name,extension,path,position,params,html
                FROM modules
                WHERE published='1' ".$otherWheres."
                ORDER BY ordering ASC"
                ,$db);

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
                    if ($id[is_category] == 0)
                    {
                        if($showInfo[extension] == 10)
                        { $currId = $id[id]; }
                        else
                        { $currId = $id[parent]; }
                    }
                    // or then by its own id
                    elseif ($id[is_category] == 1)
                    {
                        $currId = $id[id];
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
                    .= writeModule($showInfo[path],$modSett[showTitleBlock],$modSett[showTitle],$currId,$showInfo);
                }

            }
            elseif ($modSett[displayMethod]=="exceptThese")
            {

                if (in_array ( $currId, $modSett_allPages ) == false)
                {
                    $modules["<!--{{".$showInfo[position]."}}-->"]
                    .= writeModule($showInfo[path],$modSett[showTitleBlock],$modSett[showTitle],$currId,$showInfo);
                }

            }
            elseif ($modSett[displayMethod]=="everywhere")
            {
                    $modules["<!--{{".$showInfo[position]."}}-->"]
                    .= writeModule($showInfo[path],$modSett[showTitleBlock],$modSett[showTitle],$currId,$showInfo);
            }


            // запобігаєм накопиченню масива даними
            // що відносяться до інших модулів
            unset($modSett);

        } // while end

            return $modules;

    } // FUNC END
    
    function getCurrentTemplateInfo()
    {

    $getCurrentTemplate = mysql_query(
                                "SELECT * FROM extensions
                                WHERE
                                type = 'template' and
                                params LIKE '%tplSett[defa_lt]=1;%'",
                                $this->db);

    $template = mysql_fetch_array($getCurrentTemplate);

    $currTemplateInfo = $template;
    $currTemplateInfo[path] = TEMPL_PATH . "/" .$template[path];

  	  return $currTemplateInfo;

    } // FUNC END    
    
 }