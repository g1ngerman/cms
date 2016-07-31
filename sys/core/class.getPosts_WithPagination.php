<?php
	
class getPosts_WithPagination{
    
    var $currPage,$startingPost,$numPerPage,$totalPagesNum,$totalPostsNum,
    $countQuery,$postsQuery,$postsArr,$naviLink,$checkParentPublStatus;
    


    function __construct($countQuery,$postsQuery,$postsPerPage,$checkParentPublStatus=0){
      $this->checkParentPublStatus = $checkParentPublStatus;
  	  $this->getNumPerPage($postsPerPage);
      $this->getTotalPostsNum($countQuery);
      $this->getTotalPagesNum($this->totalPostsNum,$this->numPerPage);
      $this->getCurrPage($this->totalPagesNum);
	  $this->getStartingPost($this->currPage,$this->numPerPage);
      
      $this->getPostsArray($postsQuery,$this->startingPost,$this->numPerPage);
      
      } // FUNC END

    
    
    function getNumPerPage($postsPerPage)
    {						
			$this->numPerPage = $postsPerPage;
			
    } // FUNC END



    function getTotalPostsNum($countQuery){

         $getTotalNumOfPosts = mysql_query("SELECT COUNT(*) FROM $countQuery");
                 
         $totalNumOfPosts = mysql_fetch_array($getTotalNumOfPosts);

  	     $this->totalPostsNum = $totalNumOfPosts[0];
	
      } // FUNC END


  
    
    function getTotalPagesNum($totalPostsNum,$numPerPage){

  	  $this->totalPagesNum = ceil($totalPostsNum / $numPerPage);
	
      } // FUNC END 
 
    
    
    function getCurrPage($totalPages){

     if ($_GET[start] != ''){ $currPage = $_GET[start]; }
     elseif ($_GET[page] != ''){ $currPage = $_GET[page]; }
     
     // Определяем начало сообщений для текущей страницы
     $currPage = intval($currPage);
     // Если значение $currPage меньше единицы или отрицательно
     // переходим на первую страницу
     // А если слишком большое, то переходим на последнюю
     if(empty($currPage) or $currPage < 0) $currPage = 1;
     if($currPage > $totalPages) $currPage = $totalPages;

  	  $this->currPage = $currPage;
	
      } // FUNC END    



      
    function getStartingPost($currPage,$numPerPage){
 	  
        $this->startingPost = $currPage * $numPerPage - $numPerPage;
	
      } // FUNC END            



    function getPostsArray($query,$startingPost,$numPerPage){

        if ($this->checkParentPublStatus == 1)
        {
            $selPostFromDb = mysql_query("$query 
                LIMIT $startingPost, $numPerPage");
            if ($selPostFromDb)
            {
                $post = mysql_fetch_array($selPostFromDb);
                
                $checkParentPublStatusQ = mysql_query("SELECT published FROM data WHERE id = '$post[parent]'");
                $perentPublStatusResult = mysql_fetch_array($checkParentPublStatusQ);
                
                if ($perentPublStatusResult[published] == 1)
                {
                    $selectPostFromDb = mysql_query("$query 
                        LIMIT $startingPost, $numPerPage");
                    
                    if ($selectPostFromDb == true)
                    {
                         while ($postsArray = mysql_fetch_array($selectPostFromDb))
                        {
                            $this->postsArr[] = $postsArray;
                        }           
                    }
                    else
                    {
                        $this->postsArr[] = "";
                    }                
                } // if published END 
                else
                {
                    $this->postsArr[] = "";
                }                 
            
            }
            else
                {
                    $this->postsArr[] = "";
                }
            
                      
        } // if chechPublStatus on END
           else
           {
                $selectPostFromDb = mysql_query("$query 
                    LIMIT $startingPost, $numPerPage");
                
                if ($selectPostFromDb == true)
                {
                     while ($postsArray = mysql_fetch_array($selectPostFromDb))
                    {                       
                        $this->postsArr[] = $postsArray;
                    }
                    $this->getTotalPagesNum($this->totalPostsNum,$this->numPerPage);            
                }
                else
                {
                    $this->postsArr[] = "";
                }        
           } // else chechPublStatus off END 
          
	
      } // FUNC END    
    


    function getPagination($naviLink="index.php?page=",$currPage,$totalPages){

            $currPage = $this->currPage;
            $totalPages = $this->totalPagesNum;
     
             // Проверяем нужны ли стрелки назад
        if ($currPage != 1) $pervpage = '<a href='.$naviLink.'1>Первая</a> | <a href='.$naviLink. ($currPage - 1) .'>Предыдущая</a> | ';
        else $pervpage = '<span style="color:gray;">Первая | Предыдущая |</span> ';
             // Проверяем нужны ли стрелки вперед
             if ($currPage != $totalPages) $nextpage = ' | <a href='.$naviLink. ($currPage + 1) .'>Следующая</a> | <a href='.$naviLink .$totalPages. '>Последняя</a>';
             else $nextpage = '<span style="color:gray;"> | Следующая | Последняя</span>';
        
             // Находим две ближайшие станицы с обоих краев, если они есть
             /*if($currPage - 5 > 0) 
             $page5left = ' <a href='.$naviLink. ($currPage - 5) .'>'. ($currPage - 5) .'</a> | ';*/
             if($currPage - 4 > 0) 
             $page4left = ' <a href='.$naviLink. ($currPage - 4) .'>'. ($currPage - 4) .'</a> | ';
             if($currPage - 3 > 0) 
             $page3left = ' <a href='.$naviLink. ($currPage - 3) .'>'. ($currPage - 3) .'</a> | ';
             if($currPage - 2 > 0) 
             $page2left = ' <a href='.$naviLink. ($currPage - 2) .'>'. ($currPage - 2) .'</a> | ';
             if($currPage - 1 > 0) 
             $page1left = '<a href='.$naviLink. ($currPage - 1) .'>'. ($currPage - 1) .'</a> | ';
        
             /*if($currPage + 5 <= $totalPages) 
             $page5right = ' | <a href='.$naviLink. ($currPage + 5) .'>'. ($currPage + 5) .'</a>';*/
             if($currPage + 4 <= $totalPages) 
             $page4right = ' | <a href='.$naviLink. ($currPage + 4) .'>'. ($currPage + 4) .'</a>';
             if($currPage + 3 <= $totalPages) 
             $page3right = ' | <a href='.$naviLink. ($currPage + 3) .'>'. ($currPage + 3) .'</a>';
             if($currPage + 2 <= $totalPages) 
             $page2right = ' | <a href='.$naviLink. ($currPage + 2) .'>'. ($currPage + 2) .'</a>';
             if($currPage + 1 <= $totalPages) 
             $page1right = ' | <a href='.$naviLink. ($currPage + 1) .'>'. ($currPage + 1) .'</a>';
        
             
             
             // Вывод меню если страниц больше одной
        
             if ($totalPages > 1)
              {
               Error_Reporting(E_ALL & ~E_NOTICE);
               $pagination .= "<div class=\"page_navigation\">";
               $pagination .=  $pervpage.$page5left.$page4left.$page3left.$page2left.$page1left.'<b>'.$currPage.'</b>'.$page1right.$page2right.$page3right.$page4right.$page5right.$nextpage;
               $pagination .=  "</div>";
              }

  	     return $pagination;
	
      } // FUNC END

    	
} // CLASS END
    
    
?>