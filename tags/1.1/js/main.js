// Placeholder in case we need additional scripts
function wpintrojs_updateGlobalRegister(el){
    jQuery.ajax({
        type: "post",
        dataType: "html",
        url: wpintrojsScript.ajaxurl,
        data: {_ajax_nonce: wpintrojsScript.nonce,action: "wpintrojs_register_global",registerGlobal: el.value},
    success: function (e) {
            if(!jQuery('.wpintrojs.notice.notice-success.is-dismissible')[0]) {
                jQuery('#wpintrojs_tour_header').after('<div class="wpintrojs notice notice-success is-dismissible"><p>Option updated</p></div>');
            }
    },
    error: function (e) {
        if(!jQuery('.wpintrojs.notice.notice-error.is-dismissible')[0]) {
            jQuery('#wpintrojs_tour_header').after('<div class="wpintrojs notice notice-error is-dismissible"><p>Error updating option</p></div>');
        }
    }
});
}