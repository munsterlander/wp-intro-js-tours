<?php
if(!class_exists('WP_List_Table')) {
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

if (!current_user_can('manage_options')) {
    die('Unauthorized action!');
}

$frmNonce = wp_create_nonce('wpintrojs_onetime_nonce');
global $wpdb;
if(isset($_POST['frm_hints_addEdit_submit'])){
    if (isset($_REQUEST['frm_wpintrojs_wpnonce']) && !empty($_REQUEST['frm_wpintrojs_wpnonce'])) {
        $nonce = esc_attr($_REQUEST['frm_wpintrojs_wpnonce']);
        if (!wp_verify_nonce($nonce, 'wpintrojs_onetime_nonce')){
            die('Unauthorized action!');
        }
    }
    $arrayData = [];
    for($i=0;$i<count($_POST['frm_hint_hint']);$i++){
        $arrayData[$i]['hint'] = sanitize_text_field($_POST['frm_hint_hint'][$i]);
        $arrayData[$i]['hintPosition'] = sanitize_text_field($_POST['frm_hint_position'][$i]);
        $arrayData[$i]['element'] = sanitize_text_field($_POST['frm_hint_element'][$i]);
    }
    $frmData = array('tour_hints'=>json_encode($arrayData));
    $wpdb->update($wpdb->prefix.'wpintrojs_tours',$frmData,array('id' => absint($_POST['frm_wpintrojs_id'])));
    wpintrojs_redirect(admin_url('admin.php?page=wpintrojs_tour'));
}

$options = '<option value="top-middle">Top Middle</option><option value="top-left">Top Left</option><option value="top-right">Top right</option><option value="bottom-left">Bottom Left</option><option value="bottom-right">Bottom Right</option><option value="bottom-middle">Bottom Middle</option><option value="middle-left">Middle Left</option><option value="middle-right">Middle Right</option><option value="middle-middle">Middle Middle</option>';

$result = $wpdb->get_row($wpdb->prepare("SELECT tour_name,tour_hints FROM ".$wpdb->prefix."wpintrojs_tours WHERE id=%d",$_GET['tour']));
$hints = json_decode($result->tour_hints);


echo '<h3 align="left">Hint Management for - '.$result->tour_name.'</h3>';
echo '<input type="button" value="Add New Hint" class="button"  onclick="addFields()"></input>';
echo '<span style="width:10px;">&nbsp;</span><input type="button" value="Remove Last Hint" class="button" onclick="removeRow()"></input>';
echo '<form  id="wpintrojs-frm-hints-addEdit-form"  method="post"><table id="wpintrojs-hints-table" width="30%"><thead><tr><th align="center">Order</th><th align="center">Element ID</th><th align="center">Position</th><th align="center">Hint</th></tr></thead><tbody id="sortable">';
$x=0;
if($hints) {
    foreach ($hints as $data) {
        echo '<tr><td align="center"><span class="dashicons dashicons-move" title="Drag to reorder"></span> '.($x+1).'.</td>
<td align="left"><input type="text" id="frm_hint_element[]" name="frm_hint_element[]" required spellcheck="true" autocomplete="off" class="all-options" value="' . $data->element . '" placeholder="Use #element_id or document.getElementById()"></td>
            <td align="left"><select id="frm_hint_position[]" name="frm_hint_position[]">
            <option value="top-middle"'.($data->hintPosition=='top-middle'?' selected':'').'>Top Middle</option>
            <option value="top-left"'.($data->hintPosition=='top-left'?' selected':'').'>Top Left</option>
            <option value="top-right"'.($data->hintPosition=='top-right'?' selected':'').'>Top right</option>
            <option value="bottom-left"'.($data->hintPosition=='bottom-left'?' selected':'').'>Bottom Left</option>
            <option value="bottom-right"'.($data->hintPosition=='bottom-right'?' selected':'').'>Bottom Right</option>
            <option value="bottom-middle"'.($data->hintPosition=='bottom-middle'?' selected':'').'>Bottom Middle</option>
            <option value="middle-left"'.($data->hintPosition=='middle-left'?' selected':'').'>Middle Left</option>
            <option value="middle-right"'.($data->hintPosition=='middle-right'?' selected':'').'>Middle Right</option>
            <option value="middle-middle"'.($data->hintPosition=='middle-middle'?' selected':'').'>Middle Middle</option></select></td>
            <td align="left"><input type="text" id="frm_hint_hint[]" name="frm_hint_hint[]" required spellcheck="true" autocomplete="off" class="all-options"  placeholder="What do you want to say?" style="width: 700px;" value="' . $data->hint . '"></td></tr>';
        $x++;
    }
}
echo '</table><br/>';
submit_button('Save Hints','primary','frm_hints_addEdit_submit',false);
echo '<input type="hidden" id="frm_wpintrojs_wpnonce" name="frm_wpintrojs_wpnonce" value="'.$frmNonce.'">';
echo '<input type="hidden" id="frm_wpintrojs_id" name="frm_wpintrojs_id" value="'.$_GET['tour'].'">';
echo '</form>';


?>
<script>
    var rowNum = <?=$x+1?>;
    function removeRow(){
        var table = document.getElementById("wpintrojs-hints-table");
        if(rowNum > 1){
            rowNum--;
            table.deleteRow(rowNum);
        }
    }
    function addFields(){
        var table = document.getElementById("wpintrojs-hints-table");
        var row = table.insertRow(rowNum);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        cell1.innerHTML = '<span class="dashicons dashicons-move"></span> '+rowNum + '.';
        cell1.align = "center";
        cell2.innerHTML = '<input type="text" id="frm_hint_element[]" name="frm_hint_element[]" required spellcheck="true" autocomplete="off" class="all-options" placeholder="Use #element_id or document.getElementById()">';
        cell2.align = "left";
        cell3.innerHTML = '<select id="frm_hint_position[]" name="frm_hint_position[]"><?=$options?></select>';
        cell3.align = "left";
        cell4.innerHTML = '<input type="text" id="frm_hint_hint[]" name="frm_hint_hint[]" required spellcheck="true" autocomplete="off" style="width: 700px;"  placeholder="What do you want to say?" class="all-options">';
        cell4.align = "left";
        rowNum++;
    }

    jQuery(document).ready(function () {
        jQuery('#sortable').sortable();
    });

</script>