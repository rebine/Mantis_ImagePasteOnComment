<?php

# Copyright (C) 2016 - 2016 Ryuji Ebine

require_once( config_get( 'class_path' ) . 'MantisFormattingPlugin.class.php' );

class Mantis_ImagePasteOnCommentPlugin extends MantisFormattingPlugin {
  function register() {
    $this->name = 'Mantis ImagePasteOnComment';
    $this->description = 'CommentsでImageファイルをインラインに表示するPlugin';
    $this->page = '';         

    $this->version = '1.0.2';
    $this->requires = array(
      'MantisCore' => '1.3.0',
    );

    $this->author = 'Ryuji Ebine';
    $this->contact = 'rebine@redalarm.jp';
    $this->url = 'https://github.com/rebine/Mantis_ImagePasteOnComment';
  }

  function hooks() {
    return array(
      'EVENT_LAYOUT_RESOURCES'        => 'resources',                 # CSS JS include
      'EVENT_DISPLAY_TEXT'            => 'text',                      # Text String Display
      'EVENT_DISPLAY_FORMATTED'       => 'formatted',                 # Formatted String Display
      'EVENT_DISPLAY_RSS'             => 'rss',                       # RSS String Display
      'EVENT_DISPLAY_EMAIL'           => 'email',                     # Email String Display
      'EVENT_VIEW_BUG_ATTACHMENT'     => 'display_click_field',       # Display Insert Tags
                );
  } // f hooks

  public function install() {
    return true;
  } // f install

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
   * return an href anchor that links to a bug COMMENT page for the given images uploaded or download link at others
   * @param int $p_image_id
   * @return string
   */
  function string_get_bug_image_link( $match = null ) {
    $p_image_id = empty($match[2]) === false ? $match[2] : null;
    //error_log('$match'.print_r($match,true),3,'/tmp/test.log');
    preg_match('/^,rate(\d+).*/',$match[3],$match_rate);
    $p_image_rate      = empty($match_rate[1]) === false ? 'width:'.($match_rate[1] * 0.7).'%;'  : null;
    $p_image_rate_html = empty($match_rate[1]) === false ? ($match_rate[1] * 0.7).'%;'  : null;
    //error_log('$match_rate:'.print_r($match_rate,true),3,'/tmp/test.log');
  
    $attached_file['type']       = file_get_field($p_image_id,'file_type');
 
    $security_param  = form_security_param( 'file_show_inline' );

    if (empty($attached_file['type']) !== false){
      return 'Attached file not found <br ';
    } // if empty attached_file

    if (strpos($attached_file['type'] ,'image') !== false){

      $image_link      = <<< _HTML_
    <img class="Mantis_ImagePasteOnComment" style="{$p_image_rate}" width="{$p_image_rate_html}"
         src="file_download.php?file_id={$p_image_id}&type=bug&show_inline=1{$security_param}" >
  <br 
_HTML_;

    }else{

      $attached_file['filename']   = file_get_field($p_image_id,'filename');
      $image_link      = <<< _HTML_
    Attached filename:  
    <a href="file_download.php?file_id={$p_image_id}&type=bug&show_inline=1{$security_param}" >
      {$attached_file['filename']}
    </a>

_HTML_;

    } // if $attached_file
   
    return $image_link;
  } // f string_get_bug_image_link

  /**
   * return an href anchor that links to a bug COMMENT page for the given images uploaded
   * @param int $p_image_id
   * @return string
   */
  function string_process_image_link( $p_string ){
    $p_string = preg_replace_callback(
                '/(^|[^\w])' 
              . preg_quote( '%[', '/' ) . '(\d+)'.preg_quote( ']', '/' )
              . '(.*)\b/',array($this,'string_get_bug_image_link'),$p_string 
                );
        return $p_string ;
  } // f string_process_image_link


  /**
   * Display click for insert field
   * @param strings $p_event
   * @param array   $p_attachment An attachment array 
   * @return void
   */
  function display_click_field( $p_event ,$p_attachment ) {

    $t_show_attachment_preview = $p_attachment['preview'] && $p_attachment['exists'] && ( $p_attachment['type'] == 'text' || $p_attachment['type'] == 'image' );
   
    if($t_show_attachment_preview){
    $display_field =<<< _HTML_
    <div id="ImagePasteOnComment_{$p_attachment['id']}" class="Mantis_ImagePasteOnComment_Insert" >
      << Click here to insert image tag in text area
      ID: {$p_attachment['id']}
    </div>

_HTML_;
    }else{
      $display_field = '';
    } // if $t_show_attachment_previe
    return $display_field;

  } // f display_click_field

}
