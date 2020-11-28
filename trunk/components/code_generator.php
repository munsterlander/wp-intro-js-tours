<?php
global $post;
global $wpdb;
$code = '';
if(isset($adminPage)){
    $link = get_current_screen()->id;
    if(get_current_screen()->parent_file=='wpintrojs_tour') {
        if (isset($_REQUEST['action'])) {
            $link .= '_' . $_REQUEST['action'];
        }
    }
    $isPage = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "wpintrojs_tours WHERE admin_page_id=%s",$link));
} else {
    $isPage = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "wpintrojs_tours WHERE page_id=%d", $post->ID));
}
if($isPage){
    $code .= '<script defer>';
    $code.='function wpIntroJs_StartTour(){
                    var intro = introJs();';
    //Set tour steps here
    if($isPage->tour_steps){
        $code.='intro.setOptions({
                        steps: '.$isPage->tour_steps.'
                      });';
    }
    //Set tour options here
    $code.="intro.setOption('exitOnEsc',".($isPage->exitOnEsc?'true':'false').");";
    $code.="intro.setOption('exitOnOverlayClick',".($isPage->exitOnOverlayClick?'true':'false').");";
    $code.="intro.setOption('showStepNumbers',".($isPage->showStepNumbers?'true':'false').");";
    $code.="intro.setOption('keyboardNavigation',".($isPage->keyboardNavigation?'true':'false').");";
    $code.="intro.setOption('showButtons',".($isPage->showButtons?'true':'false').");";
    $code.="intro.setOption('showBullets',".($isPage->showBullets?'true':'false').");";
    $code.="intro.setOption('showProgress',".($isPage->showProgress?'true':'false').");";
    $code.="intro.setOption('disableInteraction',".($isPage->disableInteraction?'true':'false').");";
    $code.="intro.setOption('hidePrev',".($isPage->hidePrev?'true':'false').");";
    $code.="intro.setOption('hideNext',".($isPage->hideNext?'true':'false').");";
    $code.="intro.setOption('scrollToElement',".($isPage->scrollToElement?'true':'false').");";
    $code.="intro.setOption('nextLabel','".$isPage->nextLabel."');";
    $code.="intro.setOption('prevLabel','".$isPage->prevLabel."');";
    $code.="intro.setOption('skipLabel','".$isPage->skipLabel."');";
    $code.="intro.setOption('doneLabel','".$isPage->doneLabel."');";
    $code.="intro.setOption('tooltipPosition','".$isPage->tooltipPosition."');";
    $code.="intro.setOption('tooltipClass','".$isPage->tooltipClass."');";
    $code.="intro.setOption('highlightClass','".$isPage->highlightClass."');";
    $code.="intro.setOption('scrollTo','".$isPage->scrollTo."');";
    $code.="intro.setOption('scrollPadding','".$isPage->scrollPadding."');";
    $code.="intro.setOption('overlayOpacity','".$isPage->overlayOpacity."');";

    if($isPage->tour_complete){
        $code .= 'intro.oncomplete(function () {
                                    jQuery.ajax({
                                        type: "post",
                                        dataType: "html",
                                        url: wpintrojsScript.ajaxurl,
                                        data: {
                                            _ajax_nonce: wpintrojsScript.nonce,
                                            action: "wpintrojs_tour_complete",
                                            page_id: '.$post->ID.'}
                                    });
                                });';
    }

    $code .= 'intro.start();';

    $code.='}';
    if($isPage->tour_auto_start) {
        $doTour = true;
        if($isPage->tour_complete){
            if(intval(get_the_author_meta('wpintrojs_tour_complete',get_current_user_id()))===intval($post->ID)){
                $doTour=false;
            }
        }
        if($doTour) {
            $code .= 'jQuery(document).ready(function(){
                wpIntroJs_StartTour();
                });';
        }
    }
    $code.='function wpIntroJs_ShowHints(){
                    var intro = introJs();';
    //Set tour hints here
    if($isPage->tour_hints){
        $code.='intro.setOptions({
                        hints: '.$isPage->tour_hints.'
                      });';
    }
    //Set hints options here
    $code.="intro.setOption('hintAnimation',".($isPage->hintAnimation?'true':'false').");";
    $code.="intro.setOption('hintButtonLabel','".$isPage->hintLabel."');";
    $code.="intro.setOption('hintPosition','".$isPage->hintPosition."');";
    $code.="intro.addHints();";
    $code.="}";
    $code.='</script>';
}