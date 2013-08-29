<?php
/*
  $Id: lc_cfg_set_textarea_field.php v1.0 2013-01-01 datazen $

  LoadedCommerce, Innovative eCommerce Solutions
  http://www.loadedcommerce.com

  Copyright (c) 2013 Loaded Commerce, LLC

  @author     LoadedCommerce Team
  @copyright  (c) 2013 LoadedCommerce Team
  @license    http://loadedcommerce.com/license.html
*/

  function lc_cfg_set_readonly_input_field($default, $key = null) {

    $name = (!empty($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    return lc_draw_input_field($name, $default, 'class="input" onfocus="this.select();"  readonly');
  }
?>