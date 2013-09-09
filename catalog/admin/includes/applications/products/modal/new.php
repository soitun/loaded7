<?php
/*
  $Id: new.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
?>
<style>
#newProduct { padding-bottom:20px; }
</style>
<script>
function newProduct() {
  var accessLevel = '<?php echo $_SESSION['admin']['access'][$lC_Template->getModule()]; ?>';
  if (parseInt(accessLevel) < 3) {
    $.modal.alert('<?php echo $lC_Language->get('ms_error_no_access');?>');
    return false;
  }
  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '=' . $_GET[$lC_Template->getModule()]); ?>'
  $.getJSON(jsonLink,
    function (data) {
      if (data.rpcStatus == -10) { // no session
        var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";
        $(location).attr('href',url);
      }
      $.modal({
          content: '<div id="newProduct">'+
                 '  <div id="newProductForm">'+
                 '    <form name="pNew" id="pNew" action="" method="post" enctype="multipart/form-data">'+
                 '      <p><?php echo $lC_Language->get('introduction_new_product'); ?></p>'+
                 '      <p class="button-height inline-label">'+
                 '        <label for="products_name" class="label" style="width:40%"><?php echo $lC_Language->get('field_products_name'); ?></label>'+
                 '        <input type="text" name="products_name" id="products_name" class="input unstyled" onblur="updatePermalink();"></span>'+
                 '      </p>'+
                 '      <p class="button-height inline-label">'+
                 '        <label for="products_permalink" class="label" style="width:40%"><?php echo $lC_Language->get('field_products_permalink'); ?></label>'+
                 '        <input type="text" name="products_keyword" id="products_keyword" class="input unstyled"></span>'+
                 '      </p>'+
                 '    </form>'+
                 '  </div>'+
                 '</div>',
          title: '<?php echo $lC_Language->get('modal_heading_new_product'); ?>',
          width: 500,
          scrolling: false,
          actions: {
            'Close' : {
              color: 'red',
              click: function(win) { win.closeModal(); }
            }
          },
          buttons: {
            '<?php echo $lC_Language->get('button_cancel'); ?>': {
              classes:  'glossy',
              click:    function(win) { win.closeModal(); }
            },
            '<?php echo $lC_Language->get('button_create_product'); ?>': {
              classes:  'blue-gradient glossy',
              click:    function(win) {
                var jsonVKUrl = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=validateKeyword'); ?>';
                var bValid = $("#pNew").validate({
                  rules: {
                    products_name: { required: true },
                    products_permalink: { 
                      required: true, 
                      remote: jsonVKUrl
                    }
                  },
                  invalidHandler: function() {
                  }
                }).form();
                if (bValid) {
                  var nvp = $("#pNew").serialize();
                  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=newProduct&BATCH'); ?>'
                  $.getJSON(jsonLink.replace('BATCH', nvp),
                    function (pdata) {
                      if (pdata.rpcStatus == -10) { // no session
                        window.location = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";
                      }
                      if (pdata.rpcStatus != 1) {
                        alert('<?php echo $lC_Language->get('ms_error_action_not_performed'); ?>');
                        return false;
                      }
                      if (pdata.rpcStatus == 1) {                      
                        var editLink = '<?php echo lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule() . '=PID&action=save'); ?>'
                        window.location = editLink.replace('PID', pdata.pid);
                      }
                    }
                  );
                }
              }
            }
          },
          buttonsLowPadding: true
      });
    }
  );
}
</script>