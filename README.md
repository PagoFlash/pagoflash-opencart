# pagoflash-opencart (v.1)
-- -------------------------------------------------------------------------------------------------
-- Aspectos técnicos
-- -------------------------------------------------------------------------------------------------

Requerimientos
--------------
- PHP 5.4 o superior
- Opencart 1.x 

Instalación
------------
1. Descarga el plugin
2. Descomprimir y pegar carpetas en la raíz de tu proyecto. Ej: /mi_proyecto/{{ Carpetas descargadas }}
3. Ingresa a tu cuenta admin de Opencart y entra a "Extensiones/Payments"
4. Busca PagoFlash y ahora clic a **Instalar** para instalar y configurarlo.

-- -------------------------------------------------------------------------------------------------
-- Configura el plugin
-- -------------------------------------------------------------------------------------------------

El área de configuración del plugin se muestra una vez que le das click a instalar y posee las siguientes opciones configurables:

  - Status: Habilita o desabilita el plugin para ser utilizado como método de pago.

  - Next order status: Status que retornarán los pedidos una vez que han sido pagados y validados.

  - Key Token: Ficha única que genera PagoFlash al momento de registrar un punto de venta virtual.

  - Key Secret: Ficha única complementaria que genera PagoFlash al momento de registrar un punto de venta virtual.

  - Callback URL: Solo lectura. El contenido de este campo debe ser copiado y pegado en el campo "URL callback" del formulario de registro del punto de venta virtual en PagoFlash.

  - Test mode: Indica si el plugin será usado en ambiente de prueba.
  

¡Listo! Ya tus clientes podrán pagar sus productos a través de PagoFlash.