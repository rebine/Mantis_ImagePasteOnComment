  window.addEventListener("load", function(){

      [].forEach.call( document.getElementsByClassName("Mantis_ImagePasteOnComment_Insert"),function(x){
        x.addEventListener("click",mantis_imagepasteoncomment_insert_textarea);
      });
  
    } // window onload
  , false); // window addEventListener

  function mantis_imagepasteoncomment_insert_textarea(mouseevent){
    var return_flg = false;
    var text_area_obj = document.getElementsByName("bugnote_text");
    text_area_obj[0].focus();

    var selected_obj  = document.activeElement;
    if(selected_obj.selectionStart <= selected_obj.selectionEnd ){
      string_start = selected_obj.selectionStart;
      string_end   = selected_obj.selectionEnd;
    } else {
      string_start = selected_obj.selectionEnd;
      string_end   = selected_obj.selectionStart;
    } // if 

    before_range  = selected_obj.value.substring(
                    0, string_start);
    range         = selected_obj.value.substring(
                    string_start , string_end);
    after_range   = selected_obj.value.substring(
                    string_end );

  /*   debug
      console.log(mouseevent);
 
      console.log(e);
      console.log("string_start:" + string_start);
      console.log("string_end:" + string_end);
      console.log("before_range:" + before_range);
      console.log("range:" + range);
      console.log("after_range:" + after_range);
      console.log("text_num:" + (range.match(/\r\n|\n/g)||[]).length);
      console.log(mouseevent.srcElement.id);
      console.log(image_id);
    */

    // 改行数の確認
    
    if( 1 < (range.match(/\r\n|\n/g)||[]).length ){
      return_flg = true;
    } // if match

    id_str   = mouseevent.srcElement.id;
    image_id = id_str.substring(id_str.indexOf("_")+1,id_str.length);

    if(image_id === "undefined"){ 
      before_insert_tag = '\n%[],rate100';
    }else{
      before_insert_tag = '\n%[' + image_id + '],rate100';
    }
    after_insert_tag  = '';

    if(return_flg){
      text_area_obj[0].value = before_range + before_insert_tag + '\n' + range + after_insert_tag + '\n' + after_range ; 
      var CaretPosition = string_start + before_insert_tag.length + range.length + after_insert_tag.length + 1;
    }else{
      text_area_obj[0].value = before_range + before_insert_tag + range + after_insert_tag + after_range ; 
      var CaretPosition = string_start + before_insert_tag.length + range.length + after_insert_tag.length ;
    } // if return_flg something

    if(!range){
      text_area_obj[0].value = before_range + before_insert_tag + '\n' + after_insert_tag + '\n' + after_range ; 
      var CaretPosition = string_start + before_insert_tag.length + 1;
    } // if range noting

    text_area_obj[0].setSelectionRange( CaretPosition , CaretPosition);

  } // f insert_textarea
