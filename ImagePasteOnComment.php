<?php

# Copyright (C) 2016 - 2016 Ryuji Ebine

require_once( config_get( 'class_path' ) . 'MantisFormattingPlugin.class.php' );

class ImagePasteOnCommentPlugin extends MantisFormattingPlugin {
  function register() {
    $this->name = 'ImagePasteOnComment';
    $this->description = 'CommentsでImageファイルをインラインに表示するPlugin';
    $this->page = '';         

    $this->version = '0.1';
    $this->requires = array(
      'MantisCore' => '1.2.0',
    );

    $this->author = 'Ryuji Ebine';
    $this->contact = 'rebine@redalarm.jp';
    $this->url = 'https://github.com/rebine/Mantis_ImagePasteOnComment';
  }

  function hooks() {
    $hooks = parent::hooks();
    $hooks['EVENT_LAYOUT_CONTENT_BEGIN'] = 'string_process_image_link';
    return $hooks;
  }

  /**
   * return an href anchor that links to a bug COMMENT page for the given images uploaded
   * @param int $p_image_id
   * @return string
   */
  function string_get_bug_image_link( $match = null ) {
    $p_image_id = empty($match[2]) === false ? $match[2] : null;
    //error_log('$match'.print_r($match,true),3,'/tmp/test.log');
    preg_match('/^,rate(\d+).*/',$match[3],$match_rate);
    $p_image_rate = empty($match_rate[1]) === false ? 'width:'.($match_rate[1] * 0.7).'%;'  : null;
    //error_log('$match_rate:'.print_r($match_rate,true),3,'/tmp/test.log');
  
    $security_param  = form_security_param( 'file_show_inline' );
    $image_link      = <<< _HTML_
  <a href="file_download.php?file_id={$p_image_id}&type=bug">
    <img alt="" style="border: solid 3px #000 ;{$p_image_rate}" src="file_download.php?file_id=${p_image_id}&type=bug&show_inline=1{$security_param}" /> 
  </a> <br>
_HTML_;
   
    return $image_link;
  } // f string_get_bug_image_link

  /**
   * return an href anchor that links to a bug COMMENT page for the given images uploaded
   * @param int $p_image_id
   * @return string
   */
  function string_process_image_link( $p_string){
    $p_string = preg_replace_callback( '/(^|[^\w])' . preg_quote( '%[', '/' ) . '(\d+)'.preg_quote( ']', '/' ).'(.*)\b/','string_get_bug_image_link',$p_string );
        return $p_string ;
  } // f string_process_image_link

}
