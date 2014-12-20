<?php echo $header ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator'] ?><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a>
    <?php } ?>
  </div>
  <?php foreach($errors as $error){ ?>
    <div class="warning"><?php echo $error ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save ?></a><a onclick="location = '<?php echo $cancel ?>';" class="button"><?php echo $button_cancel ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td colspan="2">
              <img src="view/image/payment/pagoflash_admin.png" width="319" height="80" alt="" />
            </td>
          </tr>
          <tr>
            <td><label for="pagoflash_status"><?php echo $text_status ?></label></td>
            <td>
              <select name="pagoflash_status" id="pagoflash_status">
                <option value="1" <?php if (1 == $pagoflash_status){ ?>selected="selected"<?php } ?>><?php echo $text_enabled ?></option>
                <option value="0" <?php if (0 == $pagoflash_status){ ?>selected="selected"<?php } ?>><?php echo $text_disabled ?></option>
              </select>
            </td>
          </tr>
          <tr>
            <td><label for="pagoflash_order_default_status"><?php echo $text_default_order_status ?></label></td>
            <td>
              <select id="pagoflash_order_default_status" name="pagoflash_order_default_status">
                <?php foreach($order_statuses as $order_status){ ?>
                  <option value="<?php echo $order_status['order_status_id'] ?>" <?php if ($order_status['order_status_id'] == $pagoflash_order_default_status){ ?>selected="selected" <?php } ?>><?php echo $order_status['name'] ?></option>
                <?php } ?>
              </select>
            </td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_token ?></td>
            <td><input type="text" name="pagoflash_key_token" value="<?php echo $pagoflash_key_token ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_secret ?></td>
            <td><input type="text" name="pagoflash_key_secret" value="<?php echo $pagoflash_key_secret ?>" /></td>
          </tr>
          <tr>
            <td><label><?php echo $entry_callback ?></label></td>
            <td><label><?php echo $pagoflash_callback_url ?></label>
          </tr>
          <tr>
            <td><?php echo $entry_test ?></td>
            <td>
              <select name="pagoflash_test_mode">
                <option value="0" <?php if(0 == $pagoflash_test_mode){ ?> selected="selected" <?php } ?>><?php echo $entry_test_no ?></option>
                <option value="1" <?php if(1 == $pagoflash_test_mode){ ?> selected="selected" <?php } ?>><?php echo $entry_test_yes ?></option>
              </select>
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer ?>