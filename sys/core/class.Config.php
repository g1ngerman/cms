<?php

class Config
 {

    public static $db = '';

    // ========================

    public static $dataBase = array(

            'server'    => 'localhost',
            'user'      => 'root',
            'passw'     => '',
            'name'      => 'cms_db',
            'charset'   => 'UTF8'

            );

    public static $path = array(

                        'rootLoc'   => '/',
                        'templ'       => '/templates',
                        'modules'     => '/modules',
                        'comm_files'  => '/files',
                        'mini_img'    => '/files/images/posts',

                        'admin'       => '/admin',

                        'sys_abs'     => '/sys/abs',
                        'sys_core'    => '/sys/core',
                        'sys_ext'     => '/sys/ext',
                        'sys_itrf'    => '/sys/itrf'

    );

    // Others

    public static $siteName = "SiteName";
    public static $siteTimeZone = "Europe/Kiev";


    // ========================

    public final static function defineConstants()
    {


      if (self::$path[rootLoc] == '/'){define(SITE_FOLDER, ''); }
	  else{ define(SITE_FOLDER, self::$path[rootLoc]); }

	  define(SITE_ROOT_URL, "http://".$_SERVER[SERVER_NAME].SITE_FOLDER);
      define(SITE_ROOT_INCLUDE, $_SERVER[DOCUMENT_ROOT].SITE_FOLDER);
      define(TEMPL_PATH, $_SERVER[DOCUMENT_ROOT].SITE_FOLDER.self::$path[templ]);
      define(MOD_PATH, $_SERVER[DOCUMENT_ROOT].SITE_FOLDER.self::$path[modules]);

      // paths for file manager:
      define(FILES_ROOT_INCLUDE, SITE_ROOT_INCLUDE.self::$path[comm_files]);
      define(FILES_URL, "http://".$_SERVER[SERVER_NAME].SITE_FOLDER.self::$path[comm_files]);

      define(MINI_IMG_ROOT_INCLUDE, SITE_ROOT_INCLUDE.self::$path[mini_img]);
      define(MINI_IMG_REL_PATH, self::$path[mini_img]);
      define(MINI_IMG_URL, "http://".$_SERVER[SERVER_NAME].SITE_FOLDER.MINI_IMG_REL_PATH);

      define(ADMIN_FOLDER, self::$path[admin]);
      define(ADMIN_ROOT_INCLUDE, SITE_ROOT_INCLUDE.ADMIN_FOLDER);
      define(ADMIN_ROOT_URL, SITE_ROOT_URL.ADMIN_FOLDER);
      define(ADMIN_ROOT_REL, SITE_FOLDER.ADMIN_FOLDER);

      // system general roots
      define(SYS_ABS, SITE_ROOT_INCLUDE.self::$path[sys_abs]);
      define(SYS_CORE, SITE_ROOT_INCLUDE.self::$path[sys_core]);
      define(SYS_EXT, SITE_ROOT_INCLUDE.self::$path[sys_ext]);
      define(SYS_ITRF, SITE_ROOT_INCLUDE.self::$path[sys_itrf]);

      //define(URL_ALIAS, SITE_ROOT_INCLUDE.self::$path[sys_itrf]);

    } // FUNC END

    // ========================

    public static function setMyTimeZone()
    {
      if (function_exists('date_default_timezone_set'))
      {date_default_timezone_set(self::$siteTimeZone);}

    } // FUNC END

/**
 *
 * GET POST/CATEGORY ALIAS FROM URL
 * SITE_FOLDER constant has to be already defined
 * @return string (unslashed)
 *
 */
    private function makeAliasFromUrl()
     {
        // remove site folder instead of server_name from request url
        // (because site could be located in subdirectory)

        $result = ($_SERVER['REQUEST_URI'] == SITE_FOLDER) ? '' : str_replace  (SITE_FOLDER, "", $_SERVER['REQUEST_URI']);

        // separate alias part from parametrs one
        $result = explode("?",$result);
        $alias = $result[0];

        // re-write '$alias' without the first and last slashes

        // we check for slash in the beginning '$alias{0}', because there are two possible situations:
        // site location is in root directory (most common sit) or in sub-directory (you sometimes need that)
        // in the last one '$alias' goes with the slash '/' in the beginning,
        // so we remove this one as well inside of next if-else body sections

        $last = substr($alias, -1);

        if ($last != '/') { $alias = ($alias{0} != '/') ? $alias : substr($alias,1); }

        else if ($last == '/') { $alias = ($alias{0} != '/') ? substr($alias,0,-1): substr($alias,1,-1); }

        //
        return $alias;

      } // FUNC END



 } // CLASS END