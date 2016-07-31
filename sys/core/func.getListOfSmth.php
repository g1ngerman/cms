<?php
	
    function getListOfSmth($db,$query){

        $info = mysql_query($query,$db);
        
        while($showInfo = mysql_fetch_array($info))
        {
            $list[] = $showInfo;
        }

  	  return $list;
	
      } // FUNC END    
    
?>