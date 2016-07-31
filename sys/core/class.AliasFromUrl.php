<?php
	
class AliasFromUrl{
    
    
    static function getMaterialAlias(){
        
        return ($_SERVER['REQUEST_URI'] == SITE_FOLDER) ? '' : basename($_SERVER['REQUEST_URI']);
    }
/**
     * Gets categories array from url in order as they are in url
     * @return numeric array    
     */    
    static function getCategoryAlias(){
        
        $pathInfo = pathinfo($_SERVER['REQUEST_URI']);
        
        $arr = explode('/',substr($pathInfo[dirname],1));
        
        $arr = array_diff($arr, array(''));
        
        print_r($arr);
        
        return $arr;
    }    
    
    
} // CLASS END