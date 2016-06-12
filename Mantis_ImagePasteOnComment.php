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
      'EVENT_LAYOUT_RESOURCES'        => 'css',                       # CSS
      'EVENT_VIEW_BUG_ATTACHMENT'     => 'print_bug_attachment_preview_image',  # Attachment Previw Image 
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
  <a href="file_download.php?file_id={$p_image_id}&type=bug">
    <img class="Mantis_ImagePasteOnComment" alt="" style="{$p_image_rate}" 
         src="file_download.php?file_id=${p_image_id}&type=bug&show_inline=1{$security_param}" >
  </a> 
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
   * Include css 
   * @param int $p_event
   * @return string
   */
  function css( $p_event){
    return '<link href="' . plugin_file( 'Mantis_ImagePasteOnComment.css' ) . '" media="all" rel="stylesheet" type="text/css"/>';
  } // f css

  /**
   * Prints the preview of an image file attachment.
   * @param array $p_attachment An attachment array from within the array returned by the file_get_visible_attachments() function.
   * @return void
   */
  function print_bug_attachment_preview_image( array $p_attachment ) {
    $t_preview_style = 'border: 0;';
    $t_max_width = config_get( 'preview_max_width' );
    if( $t_max_width > 0 ) {
      $t_preview_style .= ' max-width:' . $t_max_width . 'px;';
    }
  
    $t_max_height = config_get( 'preview_max_height' );
    if( $t_max_height > 0 ) {
      $t_preview_style .= ' max-height:' . $t_max_height . 'px;';
    }
  
    $t_title = file_get_field( $p_attachment['id'], 'title' );
    $t_image_url = $p_attachment['download_url'] . '&show_inline=1' . form_security_param( 'file_show_inline' );
  
    echo "\n<div class=\"bug-attachment-preview-image\">";
    echo '<img src="' . string_attribute( $t_image_url ) . '" alt="' . string_attribute( $t_title ) . '" style="' . string_attribute( $t_preview_style ) . '" />';
    echo '</div>';
  } f print_bug_attachment_preview_image

}
