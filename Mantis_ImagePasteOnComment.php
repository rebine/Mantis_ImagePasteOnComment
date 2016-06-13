<?php

# Copyright (C) 2016 - 2016 Ryuji Ebine

require_once( config_get( 'class_path' ) . 'MantisFormattingPlugin.class.php' );

class Mantis_ImagePasteOnCommentPlugin extends MantisFormattingPlugin {
  function register() {
    $this->name = 'Mantis_ImagePasteOnComment';
    $this->description = 'CommentsでImageファイルをインラインに表示するPlugin';
    $this->page = '';         

    $this->version = '0.3';
    $this->requires = array(
      'MantisCore' => '1.3.0',
    );

    $this->author = 'Ryuji Ebine';
    $this->contact = 'rebine@redalarm.jp';
    $this->url = 'https://github.com/rebine/Mantis_ImagePasteOnComment';
  }

  function hooks() {
    return array(
      'EVENT_DISPLAY_TEXT'            => 'text',                      # Text String Display
      'EVENT_DISPLAY_FORMATTED'       => 'formatted',                 # Formatted String Display
      'EVENT_DISPLAY_RSS'             => 'rss',                       # RSS String Display
      'EVENT_DISPLAY_EMAIL'           => 'email',                     # Email String Display
      'EVENT_LAYOUT_RESOURCES'        => 'resources',                 # CSS JS include
      'EVENT_VIEW_BUG_ATTACHMENT'     => 'display_click_field',       # Display Insert Tags
                );
  } // f hooks

  public function install() {
    return true;
  } // f install


  /**
   * Formatted text processing.
   * @param string Event name
   * @param string Unformatted text
   * @param boolean Multiline text
   * @return multi Array with formatted text and multiline paramater
   */
  function formatted( $p_event, $p_string, $p_multiline = true ) {
    static $s_text, $s_urls, $s_buglinks, $s_vcslinks;

    //error_log('$test'.print_r($s_buglinks,true)."\n",3,'/tmp/test.log');
    $t_string = $this->string_process_image_link( $p_string );
    return $t_string;
  }

  /**
   * RSS text processing.
   * @param string Event name
   * @param string Unformatted text
   * @return string Formatted text
   */
  function rss( $p_event, $p_string ) {
    static $s_text, $s_urls, $s_buglinks, $s_vcslinks;

    // error_log('$test'.print_r($s_buglinks,true)."\n",3,'/tmp/test.log');
    $t_string = $this->string_process_image_link( $p_string );

    return $t_string;
  }

  /**
   * Email text processing.
   * @param string Event name
   * @param string Unformatted text
   * @return string Formatted text
   */
  function email( $p_event, $p_string ) {
    static $s_text, $s_buglinks, $s_vcslinks;

    //error_log('$test'.print_r($s_buglinks,true)."\n",3,'/tmp/test.log');
    $t_string = $this->string_process_image_link( $p_string );

    return $t_string;
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
    <img class="Mantis_ImagePasteOnComment" alt="" style="{$p_image_rate}" 
         src="file_download.php?file_id=${p_image_id}&type=bug&show_inline=1{$security_param}" >
  <br 
_HTML_;
   
    return $image_link;
  } // f string_get_bug_image_link

  /**
   * return an href anchor that links to a bug COMMENT page for the given images uploaded
   * @param int $p_image_id
   * @return string
   */
  function string_process_image_link( $p_string){
    $p_string = preg_replace_callback(
                '/(^|[^\w])' 
              . preg_quote( '%[', '/' ) . '(\d+)'.preg_quote( ']', '/' )
              . '(.*)\b/',array($this,'string_get_bug_image_link'),$p_string 
                );
        return $p_string ;
  } // f string_process_image_link

  /**
   * Include css ,js
   * @param int $p_event
   * @return string
   */
  function resources( $p_event){
    $resource  = '<link href="' . plugin_file( 'Mantis_ImagePasteOnComment.css' ) . '" media="all" rel="stylesheet" type="text/css"/>';
    $resource .= '<script type="text/javascript" src="' . plugin_file( 'Mantis_ImagePasteOnComment.js' ) . '"></script>';
    return $resource;
  } // f resources

  /**
   * Display click for insert field
   * @param strings $p_event
   * @param array   $p_attachment An attachment array 
   * @return void
   */
  function display_click_field( $p_event ,$p_attachment ) {
   
    $display_field =<<< _HTML_
    <div id="ImagePasteOnComment_{$p_attachment['id']}" class="Mantis_ImagePasteOnComment_Insert" >
      << Click this for insert text area
      ID: {$p_attachment['id']}
    </div>

_HTML_;
    return $display_field;

  } // f display_click_field

}
