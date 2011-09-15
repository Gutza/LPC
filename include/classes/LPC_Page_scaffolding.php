<?php

  class LPC_Page_scaffolding extends LPC_Page
  {
    var $title_default='LPC';
    var $title_prefix='LPC &ndash; ';

    function renderHeader() 
    { 
      $title=$this->title; 
      if (!$title) { 
        $title=$this->title_default; 
      } else { 
        $title=$this->title_prefix.$title; 
      } 
      $result=''; 
      $result.="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">\n"; 
      $result.="<html>\n"; 
      $result.="<head>\n"; 
      $result.="        <title>".$title."</title>\n"; 
      $result.="        <link rel=\"stylesheet\" type=\"text/css\" href=\"".LPC_css."/LPC_default.css\">\n"; 
      $result.="        <link rel=\"stylesheet\" type=\"text/css\" href=\"".LPC_css."/jquery-ui.css\">\n"; 
      $result.="        <script type='text/javascript' src='".LPC_js."/jquery.js'></script>\n"; 
      $result.="        <script type='text/javascript' src='".LPC_js."/jquery-ui.js'></script>\n"; 
      $result.="        <meta http-equiv=\"Content-type\" content=\"text/html;charset=UTF-8\">\n"; 
      $result.="</head>\n"; 
      $result.="<body>\n"; 
      $result.="<div class='top_level content'>";
      $result.=<<<EOS
<script type="text/javascript"> 
        $(function() { 
                $(".date_input").datepicker(); 
                $('.date_input').datepicker('option', {dateFormat: "yy-mm-dd"}); 
        }); 
</script>
EOS;
      return $result; 
    } 
  
    function renderMenu()
    {
      $result='';
      $result.="<p class='menu'>";
      $result.="<b>Scaffolding</b>";
      $result.="</p>";
      return $result;
    }

  }
