<?php

class ControllerPaymentPagoflash extends Controller
{

  private $_errors = array();

  public function install()
  {
    $this->load->model('payment/pagoflash');
    $this->load->model('setting/setting');
    
    $this->model_payment_pagoflash->install();
    
    // instancia el generador de rutas para el frontend
    $v_url = new Url(HTTP_CATALOG, $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG);
    
    $this->model_setting_setting->editSetting(
      'pagoflash',
      array(
        'pagoflash_callback_url' => $v_url->link('payment/pagoflash/procesar', '', 'SSL')
      )
    );
    
    $this->redirect($this->url->link('payment/pagoflash', 'token=' . $this->session->data['token'], 'SSL'));
  }

  public function uninstall()
  {
    $this->load->model('payment/pagoflash');
    $this->load->model('setting/setting');
    
    $this->model_payment_pagoflash->uninstall();
    $this->model_setting_setting->deleteSetting('pagoflash');
  }

  public function index()
  {
    $this->load->model('localisation/order_status');

    // carga los modelos a utilizar en el procesamiento
    $this->load->model('setting/setting');

    // se está recibiendo una solicitud POST
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
    {
      // normaliza los datos obtenidos a través del formulario
      $this->request->post['pagoflash_key_token'] = trim($this->request->post['pagoflash_key_token']);
      $this->request->post['pagoflash_key_secret'] = trim($this->request->post['pagoflash_key_secret']);

      // instancia el generador de rutas para el frontend
      $v_url = new Url(HTTP_CATALOG, $this->config->get('config_secure') ? HTTP_CATALOG : HTTPS_CATALOG);
    
      // almacena los datos de la configuración
      $this->model_setting_setting->editSetting(
        'pagoflash',
        $this->request->post
        + array(
          'pagoflash_callback_url' => $v_url->link('payment/pagoflash/procesar', '', 'SSL')
        )
      );

      // establece el texto que indica un resultado satisfactorio
      $this->session->data['success'] = $this->language->get('text_success');

      $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
    }

    // obtiene la configuración del módulo
    $v_configuracion = $this->model_setting_setting->getSetting('pagoflash');

    // establece los valores que se mostrarán en el formulario
    $this->data['pagoflash_key_token'] = $v_configuracion['pagoflash_key_token'];
    $this->data['pagoflash_key_secret'] = $v_configuracion['pagoflash_key_secret'];
    $this->data['pagoflash_callback_url'] = $v_configuracion['pagoflash_callback_url'];
    $this->data['pagoflash_status'] = $v_configuracion['pagoflash_status'];
    $this->data['pagoflash_order_default_status'] = $v_configuracion['pagoflash_order_default_status'];
    $this->data['pagoflash_test_mode'] = $v_configuracion['pagoflash_test_mode'];

    // establece el título del documento
    $this->document->setTitle($this->language->get('heading_title'));

    // establece los elementos del recorrido de la página
    $this->data['breadcrumbs'] = array(
      array(
        'text' => $this->language->get('text_opencart'),
        'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        'separator' => false
      ),
      array(
        'text' => $this->language->get('text_payment'),
        'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
        'separator' => ' :: '
      ),
      array(
        'text' => $this->language->get('heading_title'),
        'href' => $this->url->link('payment/pagoflash', 'token=' . $this->session->data['token'], 'SSL'),
        'separator' => ' :: '
      )
    );

    // agrega los errores en caso de existir
    $this->data['errors'] = $this->_errors;

    $this->children = array(
      'common/header',
      'common/footer'
    );

    // establece el texto a utilizar
    $this->data = array_merge($this->language->load('payment/pagoflash'), $this->data);
    $this->data = array_merge($this->language->load('common/header'), $this->data);
    $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
    $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
    $this->data['action'] = $this->url->link('payment/pagoflash', 'token=' . $this->session->data['token'], 'SSL');

    // establece la plantilla a utilizar
    $this->template = 'payment/pagoflash.tpl';

    // muestra el formulario al usuario
    $this->response->setOutput($this->render());
  }

  private function validate()
  {
    // el usuario no tiene permisos para editar el metodo de pago
    if (false == $this->user->hasPermission('modify', 'payment/pagoflash'))
    {
      $this->_errors[] = $this->language->get('error_permission');
    }

    // no se indicó el token de acceso
    if (empty($this->request->post['pagoflash_key_token']))
    {
      $this->_errors[] = $this->language->get('error_empty_key_token');
    }

    // no se indicó la clave de acceso
    if (empty($this->request->post['pagoflash_key_secret']))
    {
      $this->_errors[] = $this->language->get('error_empty_key_secret');
    }

    return empty($this->_errors);
  }

}
