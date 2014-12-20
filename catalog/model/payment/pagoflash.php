<?php

class ModelPaymentPagoflash extends Model
{

  public function getMethod($address, $total)
  {
    $this->language->load('payment/pagoflash');

    $v_datos = array(
      'code' => 'pagoflash',
      'title' => $this->language->get('text_title'),
      'sort_order' => 1
    );
    
    return $v_datos;
  }

}
