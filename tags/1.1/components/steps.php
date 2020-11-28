<?php
if(!class_exists('WP_List_Table')) {
    require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

if (!current_user_can('manage_options')) {
    die('Unauthorized action!');
}

$frmNonce = wp_create_nonce('wpintrojs_onetime_nonce');
global $wpdb;
if(isset($_POST['frm_steps_addEdit_submit'])){
    if (isset($_REQUEST['frm_wpintrojs_wpnonce']) && !empty($_REQUEST['frm_wpintrojs_wpnonce'])) {
        $nonce = esc_attr($_REQUEST['frm_wpintrojs_wpnonce']);
        if (!wp_verify_nonce($nonce, 'wpintrojs_onetime_nonce')){
            die('Unauthorized action!');
        }
    }
    $arrayData = [];
    for($i=0;$i<count($_POST['frm_step_intro']);$i++){
        $arrayData[$i]['intro'] = sanitize_text_field($_POST['frm_step_intro'][$i]);
        $arrayData[$i]['position'] = sanitize_text_field($_POST['frm_step_position'][$i]);
        $arrayData[$i]['element'] = sanitize_text_field($_POST['frm_step_element'][$i]);
    }
    $frmData = array('tour_steps'=>json_encode($arrayData));
    $wpdb->update($wpdb->prefix.'wpintrojs_tours',$frmData,array('id' => absint($_POST['frm_wpintrojs_id'])));
    wpintrojs_redirect(admin_url('admin.php?page=wpintrojs_tour'));
}

$options = '<option value="bottom">Bottom</option><option value="top">Top</option><option value="left">Left</option><option value="right">Right</option><option value="bottom-left-aligned">Bottom Left Aligned</option><option value="bottom-middle-aligned">Bottom Middle Aligned</option><option value="bottom-right-aligned">Bottom Right Aligned</option><option value="auto">Auto</option>';
$result = $wpdb->get_row($wpdb->prepare("SELECT tour_name,tour_steps FROM ".$wpdb->prefix."wpintrojs_tours WHERE id=%d",$_GET['tour']));
$steps = json_decode($result->tour_steps);


echo '<h3 align="left">Step Management for - '.$result->tour_name.'</h3>';
echo '<input type="button" value="Add New Step" class="button"  onclick="addFields()"></input>';
echo '<span style="width:10px;">&nbsp;</span><input type="button" value="Remove Last Step" class="button" onclick="removeRow()"></input>';
echo '<form  id="wpintrojs-frm-steps-addEdit-form"  method="post"><table id="wpintrojs-steps-table" width="30%"><thead><tr><th align="center">Order</th><th align="center">Element ID</th><th align="center">Position</th><th align="center">Intro</th></tr></thead><tbody id="sortable">';
$x=0;
if($steps) {
    foreach ($steps as $data) {
        echo '<tr><td align="center"><span class="dashicons dashicons-move" title="Drag to reorder"></span> '.($x+1).'.</td>
<td align="left"><input type="text" id="frm_step_element[]" name="frm_step_element[]" required spellcheck="true" autocomplete="off" class="all-options" value="' . $data->element . '" placeholder="Use #element_id or document.getElementById()"></td>
            <td align="left"><select id="frm_step_position[]" name="frm_step_position[]">
                    <option value="bottom"'.($data->position=="bottom"?' selected':'').'>Bottom</option>
                    <option value="top"'.($data->position=="top"?' selected':'').'>Top</option>
                    <option value="left"'.($data->position=="left"?' selected':'').'>Left</option>
                    <option value="right"'.($data->position=="right"?' selected':'').'>Right</option>
                    <option value="bottom-left-aligned"'.($data->position=="bottom-left-aligned"?' selected':'').'>Bottom Left Aligned</option>
                    <option value="bottom-middle-aligned"'.($data->position=="bottom-middle-aligned"?' selected':'').'>Bottom Middle Aligned</option>
                    <option value="bottom-right-aligned"'.($data->position=="bottom-right-aligned"?' selected':'').'>Bottom Right Aligned</option>
                    <option value="auto"'.($data->position=="auto"?' selected':'').'>Auto</option></select></td>
                <td align="left"><input type="text" id="frm_step_intro[]" name="frm_step_intro[]" required spellcheck="true" autocomplete="off" class="all-options" style="width: 700px;" placeholder="What do you want to say?" value="' . $data->intro . '"></td></tr>';
        $x++;
    }
}
echo '</table><br/>';
submit_button('Save Steps','primary','frm_steps_addEdit_submit',false);
echo '<input type="hidden" id="frm_wpintrojs_wpnonce" name="frm_wpintrojs_wpnonce" value="'.$frmNonce.'">';
echo '<input type="hidden" id="frm_wpintrojs_id" name="frm_wpintrojs_id" value="'.$_GET['tour'].'">';
echo '</form>';


?>
<script>
    var rowNum = <?=$x+1?>;
    function removeRow(){
        var table = document.getElementById("wpintrojs-steps-table");
        if(rowNum > 1){
            rowNum--;
            table.deleteRow(rowNum);
        }
    }
    function addFields(){
        var table = document.getElementById("wpintrojs-steps-table");
        var row = table.insertRow(rowNum);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        cell1.innerHTML = '<span class="dashicons dashicons-move"></span> '+rowNum + '.';
        cell1.align = "center";
        cell2.innerHTML = '<input type="text" id="frm_step_element[]" name="frm_step_element[]" required spellcheck="true" autocomplete="off" class="all-options" placeholder="Use #element_id or document.getElementById()">';
        cell2.align = "left";
        cell3.innerHTML = '<select id="frm_step_position[]" name="frm_step_position[]"><?=$options?></select>';
        cell3.align = "left";
        cell4.innerHTML = '<input type="text" id="frm_step_intro[]" name="frm_step_intro[]" required spellcheck="true" autocomplete="off" style="width: 700px;" class="all-options" placeholder="What do you want to say?">';
        cell4.align = "left";
        rowNum++;
    }

    jQuery(document).ready(function () {
        jQuery('#sortable').sortable();
    });

</script>