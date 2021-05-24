<?php

namespace Helper;

class Core
{
    /**
     * @return mixed
     */
    public static function metabox_form_name($post_id){
        $value = get_post_meta($post_id->ID, 'meta-key-name', true);
        ?>
            <input style="width:50%;"  class="postbox" name="wporg_field_name" type="text" value="<?php if(isset($value)) echo $value;?>">
        <?php
    }

	public static function metabox_form_supcosku($post_id){
		$value = get_post_meta($post_id->ID, 'meta-key-supcosku', true);
		?>
        <input style="width:50%;"  class="postbox" name="wporg_field_supcosku" type="text" value="<?php if(isset($value)) echo $value;?>">
		<?php
	}

    public static function metabox_form_code($post_id){
        $value = get_post_meta($post_id->ID, 'meta-key-code', true);
        ?>
        <input  required class="postbox" name="wporg_field_code" type="text" value="<?php if(isset($value)) echo $value; ?>">
        <?php
    }

    public static function metabox_form_description($post_id){
        $value = get_post_meta($post_id->ID, 'meta-key-description', true);
        ?>
        <?php wp_editor( $value, 'biography', array( 'wpautop'       => true, 'media_buttons' => true, 'textarea_name' => 'wporg_field_description', 'textarea_rows' => 10, 'teeny'         => true ) ); ?>
        <?php
    }



}
