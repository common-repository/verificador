<?php
/*
Plugin Name: Verificador
Plugin URI: http://www.karlankas.net/blog/
Description: &Uacute;til para comprobar si se ha rellenado el nombre, el comentario y el correo antes de mandar el comentario al servidor.
Author: KarlanKas & Coffelius
Version: 0.2
Author URI: http://karlankas.net/blog
*/ 

if(is_plugin_page()):
	$wppVerificador->options_page();
else:

class Verificador {
//	var $error_msgs;
//	var $error_fields;
	function Verificador() {
		$this->update();
		add_action('wp_head', array(&$this, 'wp_head'));
		add_action('comment_form', array(&$this, 'comment_form'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
	}


	function check_defaults() {
		$errorAuthor="Please, type your name";
		$errorEmail="Please, type your email address (can be fake)";
		$errorComment="Type your comment, please";
		$this->error_msgs=array($errorAuthor, $errorEmail, $errorComment);
		$this->error_fields=array('author', 'email', 'comment');
		for($i=0;$i<count($this->error_msgs);$i++):
			update_option('verificador_f'.$i, $this->error_fields[$i]);
			update_option('verificador_m'.$i, $this->error_msgs[$i]);
		endfor;
	}
	function update() {
		$this->error_msgs=array();
		$this->error_fields=array();
		for($i=0;true;$i++):
			$field=get_option('verificador_f'.$i);
			$msg=get_option('verificador_m'.$i);
			if(!$field || !$msg) break;
			$this->error_fields[]=$field;
			$this->error_msgs[]=$msg;
		endfor;
		if(!$this->error_fields) $this->check_defaults();
	}

	function admin_menu() {
		add_options_page(
			__("Verificador Options"),
			__("Verificador"), 5, basename(__FILE__));

	
	}

	function wp_head(){
		$fields='"'.(implode('","', $this->error_fields)).'"';
		$msgs='"'.(implode('","', $this->error_msgs)).'"';
		
echo'
<script language="JavaScript" type="text/javascript">
mal=false;
campos=['.$fields.'];
mensajesError=['.$msgs.'];
function comprobar(formulario){
	for(asd=0;asd<campos.length;asd++){
		if(formulario[campos[asd]].value==""){
			alert(mensajesError[asd]);
			formulario[campos[asd]].focus();
			mal=true;
			return false;
		}
	}
} 
</script>
';
}
function comment_form(){
echo "<script language='JavaScript' type='text/javascript'>document.getElementById('commentform').onsubmit=function(){return comprobar(this)}</script>";
}

    function options_page()
    {
	$options_len=count($this->error_fields);
        if (isset($_POST['Submit'])):
		$deleted=0;
		foreach(range(0, $options_len) as $i):
			$kfield='verificador_f'.$i;
			$kmsg='verificador_m'.$i;
			$okfield='verificador_f'.($i-$deleted);
			$okmsg='verificador_m'.($i-$deleted);

			if(isset($_POST[$kfield]) && isset($_POST[$kmsg])):
				if($_POST[$kfield]=='' and $i!=$options_len):
					delete_option($kfield);
					delete_option($kmsg);
					$deleted++;
				else:
					update_option($okfield, $_POST[$kfield]);
					update_option($okmsg, $_POST[$kmsg]);
				endif;
			endif;
		endforeach;
		if($deleted)
			for($i=0;$i<$deleted;$i++):
				delete_option('verificador_f'.($options_len-$i));
				delete_option('verificador_m'.($options_len-$i));
			endfor;
		$this->update();
		$options_len=count($this->error_fields);
?>
<div class="updated">
<p><strong><?php _e('Options saved.') ?></strong></p>
</div>
<?php
        endif;
?>
<div class="wrap">
<h2><?php echo __('Verificador Options'); ?></h2>
<form name="verificador" method="post" action="">
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="page_options" value="<?php 
echo "'verificador_f".(implode("','verificador_f", range(0, $options_len)))."'";
echo ","; 
echo "'verificador_m".(implode("','verificador_m", range(0, $options_len)))."'"; 

?>" />
  <fieldset class="options">
    <legend><?php echo __('Campos obligatorios y mensajes de error. '); ?></legend>
Si quieres borrar alguno, simplemente deja en blanco el nombre del campo obligatorio
    <table cellspacing="2" cellpadding="5" class="editform">
    <?php
for ($i=0;$i<$options_len;$i++):
?>	
      <tr valign="top">

	    <td><input name="verificador_f<?php echo $i ?>" type="text" value="<?php echo $this->error_fields[$i]; ?>" size="8" align="right" />
        </td>
	<td><textarea name="verificador_m<?php echo $i ?>"><?php echo $this->error_msgs[$i]; ?></textarea>
        </td>

      </tr>
<?php
endfor;
?>

    </table>
  </fieldset>
<fieldset class="options">
    <legend><?php echo __('Inserta un nuevo campo obligatorio y su mensaje de error'); ?></legend>
    <table cellspacing="2" cellpadding="5" class="editform">
    <?php
$i=$options_len;
?>	
      <tr valign="top">

	    <td><input name="verificador_f<?php echo $i; ?>" type="text" value="" size="8" align="right" />
        </td>
	<td><textarea name="verificador_m<?php echo $i; ?>"></textarea>
        </td>

      </tr>

    </table>
  </fieldset>
  
  <p class="submit">
      <input type="submit" name="Submit"
          value="<?php _e('Update Options') ?> &raquo;" />
  </p>
</form>
</div>
<?php
    }

}
$wppVerificador=& new Verificador; 

endif; 
?>