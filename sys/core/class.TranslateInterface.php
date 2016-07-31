<?php

class TranslateInterface{

    function __construct(){

        //$filename = "http://translate.reference.com/english/ukrainian/turn-down";

        //$getTrsl = file_get_contents ($filename);
        //file_put_contents('gtranslation.html',$getTrsl);

        $filename = "gtranslation.html";

        $getTrsl = file($filename);


        foreach($getTrsl as $val){
            if (substr_count($val, 'placeholder="Translation"') === 1){
                $word = file_put_contents('word.txt',trim(strip_tags($val)));
                break;
            }
        }

        //file_put_contents('gtranslation.html',$getTrsl);

        echo "<h2>TranslateInterface: yes</h2>";


        echo "<h2>".strip_tags("<colname>col-1</colname>")."</h2>";

        echo "<h2><time datetime='ГГГГ-ММ-ДДTчч:мм:сс'>сегодня</time></h2>";

        //echo "<pre>".file_get_contents("https://raw.githubusercontent.com/g1ngerman/cms/master/create-user-categories-table.sql")."</pre>";


    } // FUNC END
}