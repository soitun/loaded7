<?php
/**
  $Id: data.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/
global $lC_Language, $pInfo, $tax_class_array; 
?>
<div id="section_data_content" class="with-padding">
  <fieldset class="fieldset">
    <legend class="legend"><?php echo $lC_Language->get('text_inventory_settings'); ?></legend>
    <div class="columns">
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('field_model'); ?></span><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_model')); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <input type="text" class="required input full-width" value="<?php echo (isset($pInfo) ? $pInfo->get('products_model') : null); ?>" id="products_model" name="products_model" />
        </div>
      </div>
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">
        <!-- lc_track_inventory_override begin -->
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_track_inventory_override'); ?></span><?php echo lc_go_pro(); ?>
          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_track_inventory_override'), null, 'info-spot on-left grey margin-left'); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <span class="button-group">
            <label for="ti_radio_1" class="button disabled">
              <input type="radio" name="track_inventory_radio_group" id="ti_radio_1" value="1" />
              <?php echo $lC_Language->get('text_default'); ?>
            </label>
            <label for="ti_radio_2" class="button disabled">
              <input type="radio" name="track_inventory_radio_group" id="ti_radio_2" value="2" />
              <?php echo $lC_Language->get('text_on'); ?>
            </label>
            <label for="ti_radio_3" class="button disabled">
              <input type="radio" name="track_inventory_radio_group" id="ti_radio_3" value="3" />
              <?php echo $lC_Language->get('text_off'); ?>
            </label>
          </span>
        </div>
        <!-- lc_track_inventory_override end -->
      </div>
    </div>
    <div class="columns">
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_msrp'); ?></span><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_msrp')); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <input type="text" class="required input full-width" value="<?php echo number_format($pInfo->get('products_msrp'), DECIMAL_PLACES); ?>" id="products_msrp" name="products_msrp" />
        </div>
      </div>
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">
        <!-- lc_vendor_supplier begin -->
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_vendor_supplier'); ?></span><?php echo lc_go_pro(); ?><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_vendor')); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <select class="select full-width small-margin-top" disabled>
            <option id="1" value="1">Vendor #1</option>
          </select>
        </div>
        <!-- lc_vendor_supplier end -->
      </div>
    </div>
    <div class="columns">
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">
        <!-- lc_inventory_control begin -->
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_inventory_control'); ?></span><?php echo lc_go_pro(); ?><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_inventory_control'), 'margin-left:20px', 'info-spot info-spot on-right grey'); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <span id=invControlButtons" class="button-group">
            <label for="ic_radio_1" class="oicb button blue-active<?php echo (isset($pInfo) && ($pInfo->getInt('has_children') == 1) ? '' : ' active'); ?>">
              <!-- move onclick to function later maestro -->
              <input type="radio" name="inventory_control_radio_group" id="ic_radio_1" value="1" />
              <?php echo $lC_Language->get('text_simple'); ?>
            </label>
            <label for="ic_radio_2" class="oicb button red-active<?php echo (isset($pInfo) && ($pInfo->getInt('has_children') == 1) ? ' active' : ''); ?>">
              <!-- move onclick to function later maestro -->
              <input type="radio" name="inventory_control_radio_group" id="ic_radio_2" value="2" />
              <?php echo $lC_Language->get('text_multi_sku'); ?>
            </label>
          </span>
        </div>
        <!-- lc_inventory_control end -->
      </div>
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">&nbsp;</div>
    </div>
    <div id="inventory_control_container" class="field-drop button-height black-inputs">
      <!-- lc_inventory_control_simple begin -->
      <div id="inventory_control_simple"<?php echo (isset($pInfo) && ($pInfo->getInt('has_children') == 1) ? ' style="display:none;"' : ''); ?>>
        <div>
          <label for="" class="label"><b><?php echo $lC_Language->get('text_qty_on_hand'); ?></b></label>
          <input type="text" name="products_quantity" id="products_quantity" value="<?php echo $pInfo->get('products_quantity'); ?>" class="input small-margin-right" style="width:60px;" />
          <input type="text" name="products_sku" id="products_sku" value="<?php echo $pInfo->get('products_sku'); ?>" class="input" />
          <b><?php echo $lC_Language->get('text_sku'); ?></b>
        </div>
        <div class="small-margin-top">
          <input type="text" name="products_cost" id="products_cost" value="<?php //echo number_format($pInfo->get('products_cost'), DECIMAL_PLACES); ?>" class="input small-margin-right" disabled /> <b><?php echo $lC_Language->get('text_cost'); ?></b><?php echo lc_go_pro(); ?>
        </div>
      </div>
      <!-- lc_inventory_control_simple end -->                                       
      <div id="inventory_control_multi"<?php echo (isset($pInfo) && ($pInfo->getInt('has_children') == 1) ? '' : ' style="display:none;"'); ?>>
        <span class="icon-warning icon icon-size2 icon-orange small-margin-right"></span> <?php echo $lC_Language->get('text_edit_qoh_sku'); ?>
      </div>
    </div>
  </fieldset>
  <fieldset class="fieldset">
    <legend class="legend"><?php echo $lC_Language->get('text_tax_settings'); ?></legend>
    <div class="columns no-margin-bottom">
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile mid-margin-bottom">
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_tax_class'); ?></span><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_tax_class')); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <?php echo lc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (isset($pInfo) ? $pInfo->getInt('products_tax_class_id') : null), 'class="select full-width small-margin-top" id="tax_class0"'); ?>
        </div>
      </div>
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile mid-margin-bottom">
        <div class="twelve-columns no-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_base_price_with_tax'); ?></span><?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_price_with_tax')); ?>
        </div>
        <div class="twelve-columns no-margin-bottom small-margin-top">
          <?php echo lc_draw_input_field('products_price_gross', (isset($pInfo) ? lc_round($pInfo->get('products_price'), DECIMAL_PLACES) : null), 'class="required input full-width blue-gradient strong" id="products_price0_gross" READONLY'); ?>
        </div>
      </div>
    </div>
  </fieldset>
  <fieldset class="fieldset">
    <legend class="legend"><?php echo $lC_Language->get('text_management_settings'); ?></legend>
    <div class="columns no-margin-bottom">
    
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile no-margin-bottom">
        <?php echo lC_Products_Admin::getProductAttributeModules('dataManagementSettings'); ?>
      </div>

      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile no-margin-bottom">                  
        <div class="twelve-columns small-margin-bottom strong">
          <span><?php echo $lC_Language->get('text_product_class'); ?></span><?php echo lc_go_pro(); ?>
          <?php echo lc_show_info_bubble($lC_Language->get('info_bubble_data_product_class')); ?>
        </div>                  
        <div class="twelve-columns margin-bottom">
          <select class="select full-width small-margin-top" disabled>
            <option id="1" value="1">Common</option>
            <option id="2" value="2">2nd Class</option>
            <option id="3" value="3">3rd Class</option>
            <option id="4" value="4">4th Class</option>
            <option id="5" value="5">5th Class</option>
          </select>
        </div>

      </div>
    </div>
  </fieldset>
  <fieldset class="fieldset">
    <legend class="legend"><?php echo $lC_Language->get('text_product_details'); ?></legend>
    <div class="columns no-margin-bottom">
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile no-margin-bottom">
        <?php //foreach() { ?>
        <div class="margin-bottom">
          <label for="" class="label strong">Custom Field 1</label>
          <input type="text" name="" id="" value="<?php echo $lC_Language->get('text_coming_soon'); ?>" class="input" disabled />
        </div>
        <?php //} ?>
        <div class="margin-bottom">
          <label for="" class="label strong">Custom Field 2</label>
          <input type="text" name="" id="" value="<?php echo $lC_Language->get('text_coming_soon'); ?>" class="input" disabled />
        </div>
        <div> 
          <label for="" class="label strong">Custom Field 3</label>
          <input type="text" name="" id="" value="<?php echo $lC_Language->get('text_coming_soon'); ?>" class="input" disabled />
        </div>
      </div>
      <div class="new-row-mobile six-columns six-columns-tablet twelve-columns-mobile">
        <!-- lc_products_custom begin -->
        <p class="button-height">
          <a class="button icon-star small-margin-right disabled" href="javascript:void(0)">Customize</a><?php echo lc_go_pro(); ?>
        </p>
        <!-- lc_products_custom end -->
      </div>
    </div>
  </fieldset>
</div>