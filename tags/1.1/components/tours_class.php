<?php
if(!class_exists('WP_List_Table')) {
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

if (!current_user_can('administrator')) {
    die('Unauthorized action!');
}

function wpintrojs_redirect($url){
    //redirect(admin_url('admin.php?page=go_to_site'));
    if(headers_sent()){
        $string = '<script type="text/javascript">';
        $string .= 'window.location = "' . $url . '"';
        $string .= '</script>';
        echo $string;
    } else {
        if (isset($_SERVER['HTTP_REFERER']) AND ($url == $_SERVER['HTTP_REFERER']))
            header('Location: '.$_SERVER['HTTP_REFERER']);
        else
            header('Location: '.$url);
    }
    exit;
}


if(isset($_POST['frm_tour_addEdit_submit'])){
    if (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce'])) {
        $nonce = esc_attr($_REQUEST['_wpnonce']);
        if (!wp_verify_nonce($nonce, 'wpintrojs_onetime_nonce')){
            die('Unauthorized action!');
        }
    }
    global $wpdb;
    $autoStart = (isset($_POST['frm_tour_auto_start'])?1:0);
    $exitOnEsc = (isset($_POST['frm_tour_exitOnEsc'])?1:0);
    $exitOnOverlayClick = (isset($_POST['frm_tour_exitOnOverlayClick'])?1:0);
    $showStepNumbers = (isset($_POST['frm_tour_showStepNumbers'])?1:0);
    $keyboardNavigation = (isset($_POST['frm_tour_keyboardNavigation'])?1:0);
    $showButtons = (isset($_POST['frm_tour_showButtons'])?1:0);
    $showBullets = (isset($_POST['frm_tour_showBullets'])?1:0);
    $showProgress = (isset($_POST['frm_tour_showProgress'])?1:0);
    $disableInteraction = (isset($_POST['frm_tour_disableInteraction'])?1:0);
    $tourComplete = (isset($_POST['frm_tour_complete'])?1:0);
    $hidePrev = (isset($_POST['frm_tour_hidePrev'])?1:0);
    $hideNext = (isset($_POST['frm_tour_hideNext'])?1:0);
    $scrollToElement = (isset($_POST['frm_tour_scrollToElement'])?1:0);
    $hintAnimation = (isset($_POST['frm_hint_animation'])?1:0);

    $frmData = array('tour_name'=>sanitize_text_field($_POST['frm_tour_name']),'tour_description'=>sanitize_text_field($_POST['frm_tour_description']),'page_id'=>sanitize_text_field($_POST['frm_tour_page']),'tour_auto_start'=>$autoStart,
        'exitOnEsc'=>$exitOnEsc,'exitOnOverlayClick'=>$exitOnOverlayClick,'showStepNumbers'=>$showStepNumbers,'keyboardNavigation'=>$keyboardNavigation,'showButtons'=>$showButtons,
        'showBullets'=>$showBullets,'showProgress'=>$showProgress,'disableInteraction'=>$disableInteraction,'tour_complete'=>$tourComplete,'hidePrev'=>$hidePrev,'hideNext'=>$hideNext,
        'scrollToElement'=>$scrollToElement,'nextLabel'=>sanitize_text_field($_POST['frm_tour_nextLabel']),'prevLabel'=>sanitize_text_field($_POST['frm_tour_prevLabel']),'skipLabel'=>sanitize_text_field($_POST['frm_tour_skipLabel']),'doneLabel'=>sanitize_text_field($_POST['frm_tour_doneLabel']),
        'tooltipPosition'=>sanitize_text_field($_POST['frm_tour_tooltipPosition']),'tooltipClass'=>sanitize_text_field($_POST['frm_tour_tooltipClass']),'highlightClass'=>sanitize_text_field($_POST['frm_tour_highlightClass']),'scrollTo'=>sanitize_text_field($_POST['frm_tour_scrollTo']),
        'scrollPadding'=>sanitize_text_field($_POST['frm_tour_scrollPadding']),'overlayOpacity'=>sanitize_text_field($_POST['frm_tour_overlayOpacity']),'hintAnimation'=>$hintAnimation,
        'hintLabel'=>sanitize_text_field($_POST['frm_hint_buttonLabel']),'hintPosition'=>sanitize_text_field($_POST['frm_hint_position']),'admin_page_id'=>sanitize_text_field($_POST['frm_admin_page_id']));

    if(isset($_POST['frm_tour_id']) && absint($_POST['frm_tour_id']) != 0){
        $wpdb->update($wpdb->prefix.'wpintrojs_tours',$frmData,array('id' => absint($_POST['frm_tour_id'])));
    } else {
        $wpdb->insert($wpdb->prefix.'wpintrojs_tours',$frmData);
    }
    wpintrojs_redirect(admin_url('admin.php?page=wpintrojs_tour'));
}

function wpintrojs_addEditTour(){
    if (isset($_REQUEST['frm_tour_wpnonce']) && !empty($_REQUEST['frm_tour_wpnonce'])) {
        $nonce = esc_attr($_REQUEST['frm_tour_wpnonce']);
        $action = 'bulk-tours';
        if (!wp_verify_nonce($nonce, $action) && !wp_verify_nonce($nonce, 'wpintrojs_onetime_nonce')){
            die('Unauthorized action!');
        }
    }
    if(isset($_POST['frm_tour_addnew'])){
        $frmTitle = "Add a Tour";
        $frmButton ="Add Tour";
        $frmName = "";
        $frmDescription = "";
        $frmPage = '';
        $frmID = "";
        $frmAutoStart = 1;
        $frmexitOnEsc = 1;
        $frmexitOnOverlayClick = 1;
        $frmshowStepNumbers = 1;
        $frmkeyboardNavigation = 1;
        $frmshowButtons = 1;
        $frmshowBullets = 1;
        $frmshowProgress = 0;
        $frmdisableInteraction = 1;
        $frmTourComplete = 0;
        $frmhidePrev = 0;
        $frmhideNext = 0;
        $frmscrollToElement = 1;
        $frmHintAnimation = 1;
        $frmnextLabel = "Next";
        $frmprevLabel = "Prev";
        $frmskipLabel = "Skip";
        $frmdoneLabel = "Done";
        $frmtooltipPosition = "bottom";
        $frmtooltipClass = "";
        $frmhighlightClass = "";
        $frmscrollTo = "element";
        $frmscrollPadding = "30";
        $frmoverlayOpacity = "0.8";
        $frmhintPosition = "top-middle";
        $frmhintButtonLabel = 'Got it';
        $frmAdminId='';
    } elseif (isset($_GET['action']) && $_GET['action']=='edit'){
        $frmTitle = "Edit a Tour";
        $frmButton ="Update Tour";
        global $wpdb;
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpintrojs_tours WHERE id=%d",$_GET['tour']));
        $frmName = $result->tour_name;
        $frmDescription = $result->tour_description;
        $frmPage = $result->page_id;
        $frmID = $result->id;
        $frmAutoStart = $result->tour_auto_start;
        $frmexitOnEsc = $result->exitOnEsc;
        $frmexitOnOverlayClick = $result->exitOnOverlayClick;
        $frmshowStepNumbers = $result->showStepNumbers;
        $frmkeyboardNavigation = $result->keyboardNavigation;
        $frmshowButtons = $result->showButtons;
        $frmshowBullets = $result->showBullets;
        $frmshowProgress = $result->showProgress;
        $frmdisableInteraction = $result->disableInteraction;
        $frmTourComplete = $result->tour_complete;
        $frmhidePrev = $result->hidePrev;
        $frmhideNext = $result->hideNext;
        $frmscrollToElement = $result->scrollToElement;
        $frmnextLabel = $result->nextLabel;
        $frmprevLabel = $result->prevLabel;
        $frmskipLabel = $result->skipLabel;
        $frmdoneLabel = $result->doneLabel;
        $frmtooltipPosition = $result->tooltipPosition;
        $frmtooltipClass = $result->tooltipClass;
        $frmhighlightClass = $result->highlightClass;
        $frmscrollTo = $result->scrollTo;
        $frmscrollPadding = $result->scrollPadding;
        $frmoverlayOpacity = $result->overlayOpacity;
        $frmHintAnimation = $result->hintAnimation;
        $frmhintPosition = $result->hintPosition;
        $frmhintButtonLabel = $result->hintLabel;
        $frmAdminId = $result->admin_page_id;
    }
    $frmNonce = wp_create_nonce('wpintrojs_onetime_nonce');
    $pages = get_pages();
    echo '<div class="wrap">';
    echo '<form id="wpintrojs-tour-addEdit-form" method="post"><fieldset>';
    echo '<h3><legend>'.$frmTitle.'</legend></h3>';
    echo '<div id="wpintrojs_tour_tabs">
               <ul>
                    <li><a href="#wpintrojs_tour_tab-1">Tour Information</a></li>
                    <li><a href="#wpintrojs_tour_tab-2">Tour Settings</a></li>
                    <li><a href="#wpintrojs_tour_tab-3">Tour Button Labels</a></li>
                    <li><a href="#wpintrojs_tour_tab-4">Tour CSS</a></li>
                    <li><a href="#wpintrojs_tour_tab-5">Hints Settings</a></li>
                </ul>
		<div id="wpintrojs_tour_tab-1">';
    echo '<p><label for="frm_tour_name">Tour Name</label><br /><input maxlength="50" type="text" id="frm_tour_name" name="frm_tour_name" required spellcheck="true" placeholder="Enter a descriptive name" autocomplete="off" class="all-options" value="'.$frmName.'"></p>';
    echo '<p><label for="frm_tour_description">Tour Description</label><br /><textarea id="frm_tour_description" name="frm_tour_description" placeholder="Enter a description of the tour" spellcheck="true" autocomplete="off" class="all-options">'.$frmDescription.'</textarea></p>';
    echo '<p><label for="frm_tour_page">Page for Tour</label><br /><select id="frm_tour_page" name="frm_tour_page" required>';
    echo '<option value="0">Choose a Front-End Page</option>';
    foreach ($pages as $page) {
      $slctd='';
      if(intval($page->ID)===intval($frmPage)){
          $slctd = ' selected';
      }
      echo '<option value="'.$page->ID.'"'.$slctd.'>'.$page->post_title.'</option>';
    }
    echo '</select></p>';
    echo '<p><label for="frm_admin_page_id">Admin Page ID</label><br /><input maxlength="50" type="text" id="frm_admin_page_id" name="frm_admin_page_id" spellcheck="true" autocomplete="off" class="all-options" placeholder="Enable Admin Page ID Banner to get ID" value="'.$frmAdminId.'"></p>';
    echo '</div>';
    echo '<div id="wpintrojs_tour_tab-2">';
    echo '<p><input type="checkbox" id="frm_tour_auto_start" name="frm_tour_auto_start" value="1" '.($frmAutoStart?' checked':'').'><label for="frm_tour_auto_start" style="padding-left:10px;">Auto Start Tour</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_complete" name="frm_tour_complete" value="1" '.($frmTourComplete?' checked':'').'><label for="frm_tour_complete" style="padding-left:10px;">Disable Load after Completion</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_exitOnEsc" name="frm_tour_exitOnEsc" value="1" '.($frmexitOnEsc?' checked':'').'><label for="frm_tour_exitOnEsc" style="padding-left:10px;">Exit On Escape</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_exitOnOverlayClick" name="frm_tour_exitOnOverlayClick" value="1" '.($frmexitOnOverlayClick?' checked':'').'><label for="frm_tour_exitOnOverlayClick" style="padding-left:10px;">Exit On Overlay Click</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_showStepNumbers" name="frm_tour_showStepNumbers" value="1" '.($frmshowStepNumbers?' checked':'').'><label for="frm_tour_showStepNumbers" style="padding-left:10px;">Show Step Numbers</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_keyboardNavigation" name="frm_tour_keyboardNavigation" value="1" '.($frmkeyboardNavigation?' checked':'').'><label for="frm_tour_keyboardNavigation" style="padding-left:10px;">Allow Keyboard Navigation</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_showButtons" name="frm_tour_showButtons" value="1" '.($frmshowButtons?' checked':'').'><label for="frm_tour_showButtons" style="padding-left:10px;">Show Buttons</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_showBullets" name="frm_tour_showBullets" value="1" '.($frmshowBullets?' checked':'').'><label for="frm_tour_showBullets" style="padding-left:10px;">Show Bullets</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_showProgress" name="frm_tour_showProgress" value="1" '.($frmshowProgress?' checked':'').'><label for="frm_tour_showProgress" style="padding-left:10px;">Show Progress</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_disableInteraction" name="frm_tour_disableInteraction" value="1" '.($frmdisableInteraction?' checked':'').'><label for="frm_tour_disableInteraction" style="padding-left:10px;">Disable Cutout Interaction</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_hidePrev" name="frm_tour_hidePrev" value="1" '.($frmhidePrev?' checked':'').'><label for="frm_tour_hidePrev" style="padding-left:10px;">Hide Previous Button</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_hideNext" name="frm_tour_hideNext" value="1" '.($frmhideNext?' checked':'').'><label for="frm_tour_hideNext" style="padding-left:10px;">Hide Next Button</label></p>';
    echo '<p><input type="checkbox" id="frm_tour_scrollToElement" name="frm_tour_scrollToElement" value="1" '.($frmscrollToElement?' checked':'').'><label for="frm_tour_scrollToElement" style="padding-left:10px;">Scroll to Element</label></p>';
    echo '</div>';
    echo '<div id="wpintrojs_tour_tab-3">';
    echo '<p><label for="frm_tour_nextLabel">Next Button Label</label><br /><input maxlength="50" type="text" id="frm_tour_nextLabel" name="frm_tour_nextLabel" required spellcheck="true" autocomplete="off" class="all-options" value="'.$frmnextLabel.'"></p>';
    echo '<p><label for="frm_tour_prevLabel">Previous Button Label</label><br /><input maxlength="50" type="text" id="frm_tour_prevLabel" name="frm_tour_prevLabel" required spellcheck="true" autocomplete="off" class="all-options" value="'.$frmprevLabel.'"></p>';
    echo '<p><label for="frm_tour_skipLabel">Skip Button Label</label><br /><input maxlength="50" type="text" id="frm_tour_skipLabel" name="frm_tour_skipLabel" required spellcheck="true" autocomplete="off" class="all-options" value="'.$frmskipLabel.'"></p>';
    echo '<p><label for="frm_tour_doneLabel">Done Button Label</label><br /><input maxlength="50" type="text" id="frm_tour_doneLabel" name="frm_tour_doneLabel" required spellcheck="true" autocomplete="off" class="all-options" value="'.$frmdoneLabel.'"></p>';
    echo '</div>';
    echo '<div id="wpintrojs_tour_tab-4">';
    echo '<p><label for="frm_tour_tooltipClass">Additional Tooltip Class</label><br /><input maxlength="50" type="text" id="frm_tour_tooltipClass" name="frm_tour_tooltipClass" spellcheck="true" autocomplete="off" class="all-options" value="'.$frmtooltipClass.'"></p>';
    echo '<p><label for="frm_tour_highlightClass">Additional Highlight Class</label><br /><input maxlength="50" type="text" id="frm_tour_highlightClass" name="frm_tour_highlightClass" spellcheck="true" autocomplete="off" class="all-options" value="'.$frmhighlightClass.'"></p>';
    echo '<p><label for="frm_tour_overlayOpacity">Overlay Opacity</label><br /><input min="0" max="1" type="number" step=".01" id="frm_tour_overlayOpacity" name="frm_tour_overlayOpacity" required class="all-options" value="'.$frmoverlayOpacity.'"></p>';
    echo '<p><label for="frm_tour_scrollPadding">Scroll To Padding</label><br /><input min="0" max="99" type="number" step="1" type="text" id="frm_tour_scrollPadding" name="frm_tour_scrollPadding" required  class="all-options" value="'.$frmscrollPadding.'"></p>';
    echo '<p><label for="frm_tour_scrollTo">Scroll To</label><br /><select id="frm_tour_scrollTo" name="frm_tour_scrollTo"><option value="element">element</option><option value="tooltip"'.($frmscrollTo=='tooltip'?' selected':'').'>tooltip</option></select></p>';
    echo '<p><label for="frm_tour_tooltipPosition">Tooltip Position</label><br /><select id="frm_tour_tooltipPosition" name="frm_tour_tooltipPosition">
            <option value="bottom"'.($frmtooltipPosition=='bottom'?' selected':'').'>Bottom</option>
            <option value="top"'.($frmtooltipPosition=='top'?' selected':'').'>Top</option>
            <option value="left"'.($frmtooltipPosition=='left'?' selected':'').'>Left</option>
            <option value="right"'.($frmtooltipPosition=='right'?' selected':'').'>Right</option>
            <option value="bottom-left-aligned"'.($frmtooltipPosition=='bottom-left-aligned'?' selected':'').'>Bottom Left</option>
            <option value="bottom-right-aligned"'.($frmtooltipPosition=='bottom-right-aligned'?' selected':'').'>bottom Right</option>
            <option value="auto"'.($frmtooltipPosition=='auto'?' selected':'').'>Auto</option>
            </select></p>';
    echo '</div>';
    echo '<div id="wpintrojs_tour_tab-5">';
    echo '<p><input type="checkbox" id="frm_hint_animation" name="frm_hint_animation" value="1" '.($frmHintAnimation?' checked':'').'><label for="frm_hint_animation" style="padding-left:10px;">Hint Animation</label></p>';
    echo '<p><label for="frm_hint_buttonLabel">Hint Button Label</label><br /><input maxlength="50" type="text" id="frm_hint_buttonLabel" name="frm_hint_buttonLabel" required spellcheck="true" autocomplete="off" class="all-options" value="'.$frmhintButtonLabel.'"></p>';
    echo '<p><label for="frm_hint_position">Hint Position</label><br /><select id="frm_hint_position" name="frm_hint_position">
            <option value="top-middle"'.($frmhintPosition=='top-middle'?' selected':'').'>Top Middle</option>
            <option value="top-left"'.($frmhintPosition=='top-left'?' selected':'').'>Top Left</option>
            <option value="top-right"'.($frmhintPosition=='top-right'?' selected':'').'>Top right</option>
            <option value="bottom-left"'.($frmhintPosition=='bottom-left'?' selected':'').'>Bottom Left</option>
            <option value="bottom-right"'.($frmhintPosition=='bottom-right'?' selected':'').'>Bottom Right</option>
            <option value="bottom-middle"'.($frmhintPosition=='bottom-middle'?' selected':'').'>Bottom Middle</option>
            <option value="middle-left"'.($frmhintPosition=='middle-left'?' selected':'').'>Middle Left</option>
            <option value="middle-right"'.($frmhintPosition=='middle-right'?' selected':'').'>Middle Right</option>
            <option value="middle-middle"'.($frmhintPosition=='middle-middle'?' selected':'').'>Middle Middle</option>
            </select></p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<input type="hidden" id="frm_tour_id" name="frm_tour_id" value="'.$frmID.'">';
    echo '<input type="hidden" id="frm_tour_wpnonce" name="frm_tour_wpnonce" value="'.$frmNonce.'">';
    echo '</fieldset><br />';
    submit_button($frmButton,'primary','frm_tour_addEdit_submit',false);
    echo '</div>';

    echo '<script type="text/javascript">
        jQuery(function() {
            jQuery("#wpintrojs_tour_tabs").tabs();
        });
</script>';


}

class WpintroJS_Tour_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct(
            array(
                'singular' => 'tour',
                'plural'   => 'tours',
                'ajax'     => false
            )
        );
    }

    function get_tours(){
        global $wpdb;
        $per_page = 10;
        $sql = "SELECT * FROM ".$wpdb->prefix."wpintrojs_tours";

        if (!empty($_POST['s'])) {
            $sql.=" WHERE tour_name LIKE '%".esc_sql($_POST['s'])."%' OR tour_description LIKE '%".esc_sql($_POST['s'])."%'";
        }

        if (!empty($_REQUEST['orderby'])) {
            $sql.=' ORDER BY '.esc_sql($_REQUEST['orderby']);
            $sql.=!empty($_REQUEST['order'])?' '.esc_sql($_REQUEST['order']):' ASC';
        }
        $sql.=" LIMIT $per_page";
        $page_number = 1;
        if (!empty($_REQUEST['paged'])) {
            $page_number = esc_sql($_REQUEST['paged']);
        }
        $sql.=' OFFSET '.($page_number - 1)*$per_page;
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }

    function column_tour_name($item) {
        $col_nonce = wp_create_nonce('wpintrojs_onetime_nonce');
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&tour=%s&_wpnonce=%s">Edit</a>',esc_attr($_REQUEST['page']),'edit',absint($item['id']),$col_nonce),
            'delete' => sprintf('<a href="?page=%s&action=%s&tour=%s&_wpnonce=%s">Delete</a>',esc_attr($_REQUEST['page']),'delete',absint($item['id']),$col_nonce),
            'steps' => sprintf('<a href="?page=%s&action=%s&tour=%s&_wpnonce=%s">Steps</a>',esc_attr($_REQUEST['page']),'steps',absint($item['id']),$col_nonce),
            'hints' => sprintf('<a href="?page=%s&action=%s&tour=%s&_wpnonce=%s">Hints</a>',esc_attr($_REQUEST['page']),'hints',absint($item['id']),$col_nonce),
        );
        return sprintf('%1$s %2$s', $item['tour_name'], $this->row_actions($actions) );
    }

    function column_page_id($item) {
        return get_the_title($item['page_id']);
    }

    function get_bulk_actions() {
        $actions = array('bulk-delete' => 'Delete');
        return $actions;
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="frm_tour_id[]" value="%s" />', $item['id']);
    }

    function get_columns(){
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'tour_name' => 'Name',
            'page_id' => 'Page/Post',
            'tour_description' => 'Description'
        );
        return $columns;
    }

    function prepare_items() {
        $this->process_bulk_action();
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $this->record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->get_tours();
    }
    function extra_tablenav($which) {
        if ($which == "top"){
            submit_button('Add New','secondary','frm_tour_addnew',false);
        }
    }

    function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."wpintrojs_tours";
        return $wpdb->get_var($sql);
    }

    function no_items() {
        _e( 'No tours available.', 'sp' );
    }

    function column_default($item, $column_name) {
        switch($column_name) {
            case 'tour_name':
            case 'tour_description':
            case 'page_id':
                return $item[$column_name];
            default:
                return print_r($item, true) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'tour_name'  => array('tour_name',true),
            'page_id'  => array('page_id',true),
            'tour_description'   => array('tour_description',false)
        );
        return $sortable_columns;
    }

    function search_box($text,$input_id){
        $tmpVal = "";
        if (isset($_POST['s'])){
            $tmpVal = sanitize_text_field($_POST['s']);
        }

        echo '<form id="wpintrojs-searchbox" method="post"><p class="search-box">
		<label class="screen-reader-text" for="search_id-search-input">
		search:</label> 
		<input id="search_id-search-input" type="text" name="s" value="'.$tmpVal.'" /> 
		<input id="search-submit" class="button" type="submit" name="" value="Search" />
		</p></form>';
    }

    function delete_tour($id) {
        global $wpdb;
        $wpdb->delete($wpdb->prefix."wpintrojs_tours",['id' => $id],['%d']);
    }

    function process_bulk_action() {
        if (isset($_REQUEST['_wpnonce']) && !empty($_REQUEST['_wpnonce'])) {
            $nonce = esc_attr($_REQUEST['_wpnonce']);
            $action = 'bulk-' . $this->_args['plural'];
            if (!wp_verify_nonce($nonce, $action) && !wp_verify_nonce($nonce, 'wpintrojs_onetime_nonce')){
                die('Unauthorized action!');
            }
        }

        $action = $this->current_action();
        switch ($action) {
            case 'delete':
                $this->delete_tour(absint($_GET['tour']));
                break;
            case 'bulk-delete':
                $delete_ids = esc_sql($_POST['frm_tour_id']);
                foreach ($delete_ids as $id) {
                    $this->delete_tour($id);
                }
                break;
            default:
                break;
        }
    }
}

$tourTable = new WpintroJS_Tour_Table();
?>