<div class="buttons">
  <p class="center">
    <a id="pf-do-payment" href="javascript:void(0)" data-link="<?php echo $api_url ?>" title="<?php echo $text_title ?>">
      <?php echo $text_description ?><br/>
      <img src="<?php echo $imagen_boton ?>" alt="<?php echo $text_title ?>" width="144" height="44" style="vertical-align: middle;"/>
    </a>
  </p>
</div>
<script type="text/javascript">
  $('#pf-do-payment').on('click.extra', function(){
    var v_ancho = 460;
    var v_alto = 390;
    var v_posicion_x = (screen.width/2)-(v_ancho/2);
    var v_posicion_y = (screen.height/2)-(v_alto/2);
    var v_caracteristicas = 'width='+v_ancho+',height='+v_alto+',menubar=0,toolbar=0,directories=0,scrollbars=yes,resizable=no,left='+v_posicion_x+',top='+v_posicion_y+', modal=yes';
    
    window.open($(this).data('link'), 'PagoFlash.com', v_caracteristicas);
  });
</script>