<?php

require_once(DIR_SYSTEM . 'library/pagoflash.api.client.php');

class ControllerPaymentPagoflash extends Controller
{
  /**
   * Crea una nueva instancia del API de pagoflash
   * 
   * @return \apiPagoflash
   */
  private function instanciarApi()
  {
    $this->load->model('setting/setting');
    
    // obtiene la configuración del módulo
    $v_configuracion = $this->model_setting_setting->getSetting('pagoflash');
    
    // instancia la clase que hace uso de la API de PagoFlash
    $v_pagoflash = new apiPagoflash(
      $v_configuracion['pagoflash_key_token'],
      $v_configuracion['pagoflash_key_secret'],
      urlencode($v_configuracion['pagoflash_callback_url']),
      (1 == $v_configuracion['pagoflash_test_mode'])
    );
    
    return $v_pagoflash;
  }
  
  /**
   * Controlador que atiende el proceso de visualización del botón de pago
   * al momento de completar la orden
   */
  protected function index()
  {
    // carga los archivos con el texto correspondiente al lenguaje
    $this->language->load('payment/pagoflash');

    // se configuró una plantilla diferente para el módulo
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagoflash.tpl'))
    {
      $v_raiz_plantilla = $this->config->get('config_template');
    }
    // se está utilizando la plantilla por defecto
    else
    {
      $v_raiz_plantilla = 'default';
    }

    // establece los datos a utilizar dentro de la plantilla
    $this->data = array_merge($this->language->load('payment/pagoflash'), $this->data);
    $this->data['api_url'] = $this->url->link('payment/pagoflash/conectar');
    $this->data['imagen_boton'] = 'image/data/pagoflash/pagoflash.png';

    // establece la plantilla a utilizar
    $this->template = "{$v_raiz_plantilla}/template/payment/pagoflash.tpl";

    // muestra el resultado al cliente
    $this->render();
  }

  /**
   * Controlador que atiende la conexión con el servidor de PagoFlash a través
   * del API
   */
  public function conectar()
  {
    $v_total = 0;

    $this->load->model('setting/extension');
    $this->load->model('account/order');

    // obtiene los costos asociados a la compras
    $v_costos = $this->model_account_order->getOrderTotals($this->session->data['order_id']);
    
    // calcula el precio total de la compra
    foreach($v_costos as $v_costo)
    {
      if($v_costo['code'] != 'total')
      {
        continue;
      }
      
      $v_total = $v_costo['value'];
      break;
    }
    
    // coloca los datos de la cabecera de la llamada
    $v_cabecera = array(
      'pc_order_number' => $this->session->data['order_id'],
      'pc_amount' => $v_total
    );

    // inicializa el contenedor de los datos de los productos
    $v_datos_productos = array();

    // obtiene los productos del carro de compras
    $v_productos_compra = $this->cart->getProducts();

    // recorre cada producto para colocar sus datos en el contenedor
    foreach ($v_productos_compra as $v_producto)
    {
      $v_datos_productos[] = array(
        'pr_name' => substr($v_producto['name'], 0, 127),
        'pr_desc' => substr($v_producto['name'], 0, 230),
        'pr_price' => $v_producto['price'],
        'pr_qty' => $v_producto['quantity'],
        'pr_img' => HTTPS_SERVER . "image/{$v_producto['image']}"
      );
    }

    // instancia la clase que hace uso de la API de PagoFlash
    $v_pagoflash = $this->instanciarApi();

    // hace la llamada a la plataforma mediante el API
    $op_config = $this->model_setting_setting->getSetting('pagoflash');
    $v_respuesta = $v_pagoflash->procesarPago(
      array(
        'cabecera_de_compra' => $v_cabecera,
        'productos_items' => $v_datos_productos,
            "additional_parameters" => array(
            "url_ok_redirect" =>$op_config['pagoflash_callback_url'], // en esta url le muestas a tu cliente que el pago fue exitoso
            "url_ok_request" => $op_config['pagoflash_callback_url'] // en esta url debes verificar la transaccion
        )
      ),
      $_SERVER['HTTP_USER_AGENT']
    );

    // elimina las advertencias de la respuesta en caso que existan
    $v_respuesta = substr($v_respuesta, strpos($v_respuesta, '{'));

    // convierte la respuesta recibida al formato JSON para evaluarla
    $v_respuesta_json = json_decode($v_respuesta);

    // la autenticación fué satisfactoria
    if ($v_respuesta_json->success != '0')
    {
      // muestra al usuario la pantalla que le permite continuar el pago
      $this->redirect($v_respuesta_json->url_to_buy);
    }

    // escribe la respuesta en el log
    $this->log->write("Pagoflash: {$v_respuesta}");
    
    // envía al usuario a la página de login de la tienda
    $this->redirect('/');
  }

  /**
   * Controlador que atiende el proceso de obtención de la respuesta desde
   * PagoFlash luego de realizado el pago
   */
  public function procesar()
  {
    $this->load->model('checkout/order');
    $this->load->model('setting/setting');

    // no se recibió la ficha de confirmación
    if (false == isset($this->request->get['tk']) || false == isset($this->request->get['callback']))
    {
      // escribe la respuesta en el log
      $this->log->write("Pagoflash: no se recibió la ficha de confirmación");
    
      // envía al usuario a la página de inicio
      $this->redirect('/');
    }

    // se configuró una plantilla diferente para el módulo
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/pagoflash.tpl'))
    {
      $v_raiz_plantilla = $this->config->get('config_template');
    }
    // se está utilizando la plantilla por defecto
    else
    {
      $v_raiz_plantilla = 'default';
    }
    
    // establece los sub-elementos de la plantilla
    $this->children = array(
      'common/column_left',
      'common/column_right',
      'common/content_top',
      'common/content_bottom',
      'common/footer',
      'common/header'
    );
    
    // establece el texto a utilizar
    $this->data = array_merge($this->language->load('payment/pagoflash'), $this->data);
    $this->data['imagen'] = "image/data/pagoflash/pagoflash_l.png";
    
    // almacena la ficha de confirmación
    $v_ficha_confirmacion = $this->request->get['tk'];

    // instancia la clase que hace uso de la API de PagoFlash
    $v_pagoflash = $this->instanciarApi();

    // valida la ficha de confirmación para garantizar que el pago fué aceptado
    $v_respuesta = $v_pagoflash->validarTokenDeTransaccion($v_ficha_confirmacion, $_SERVER['HTTP_USER_AGENT']);

    // elimina las advertencias de la respuesta en caso que existan
    $v_respuesta = substr($v_respuesta, strpos($v_respuesta, '{'));

    // convierte la respuesta al formato JSON
    $v_respuesta_json = json_decode($v_respuesta);
    
    // la ficha de confirmación no es válida
    if ($v_respuesta_json->cod != '1' && $v_respuesta_json->cod != '3')
    {
      // establece la plantilla a utilizar
      $this->template = "{$v_raiz_plantilla}/template/payment/pagoflash_failure.tpl";
      
      // muestra el resultado al cliente
      $this->response->setOutput($this->render());
      
      return;
    }
    
    if($v_respuesta_json->cod == '1')
    {
      // confirma la orden de compra
      $this->model_checkout_order->confirm($v_respuesta_json->order_number, $this->config->get('pagoflash_order_default_status'));
    }

    // limpia el carro de compras
    $this->cart->clear();
    
    // envía al usuario a la ventana de confirmación configurada para la tienda
    $this->redirect($this->url->link('checkout/success'));
  }

}
