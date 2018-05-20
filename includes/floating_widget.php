<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>

<div id="floating_widget_container" class="tab_content">
    
    <p>
    <label for="enable_floating_widget" class="label_switchery"><?php echo translate( 'Enable floating widget:', 'om-floating-widgets' ); ?></label>
    <input type="checkbox" class="js-switch" id="enable_floating_widget" name="enable_floating_widget" <?php if($this->fw_options['enable_floating_widget'] == "on") echo " checked "; ?>  />
    </p>
    
    <p class="mobile_hide"></p>
    <p class="mobile_hide"></p>
    <p class="mobile_hide"></p>

    <p><label for="floating_widget_location"><?php echo translate( 'Widget location:', 'om-floating-widgets' ); ?></label>

        <select id="floating_widget_location" name="floating_widget_location" >

            <?php           

                $positions = array("top", "bottom", "left", "right");

                for ($x = 0; $x < count($positions); $x++) 
                { 
                    echo "<option value='".$positions[$x]."' ";                             

                    if($this->fw_options['floating_widget_location'] == $positions[$x]) 
                        echo ' selected="selected" '; 

                    echo ">".translate( ucfirst ($positions[$x]), 'om-floating-widgets' )."</option>"; 

                } 

            ?>
        </select>
    </p>

    <p><label for="floating_widget_position"><?php echo translate( 'Widget position:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_position" name="floating_widget_position" 

    placeholder="<?php echo translate( 'px or %', 'om-floating-widgets' ); ?>" 

    value="<?php echo $this->fw_options['floating_widget_position']; ?>" /></p>

    <p><label for="floating_widget_width"><?php echo translate( 'Widget width:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_width" name="floating_widget_width" 

    placeholder="<?php echo translate( 'px or %', 'om-floating-widgets' ); ?>" 

    value="<?php echo $this->fw_options['floating_widget_width']; ?>" /></p>

    <p><label for="floating_widget_height"><?php echo translate( 'Widget height:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_height" name="floating_widget_height" 

    placeholder="<?php echo translate( 'px or %', 'om-floating-widgets' ); ?>" 

    value="<?php echo $this->fw_options['floating_widget_height']; ?>" /></p>

   	<p><label for="floating_widget_button_font_size"><?php echo translate( 'Widget button font size:', 'om-floating-widgets' ); ?></label>

        <select id="floating_widget_button_font_size" name="floating_widget_button_font_size" >

            <?php           

                for ($x = 10; $x < 36; $x++) 
                { 
                    echo "<option value='".$x."' ";                             

                    if($this->fw_options['floating_widget_button_font_size'] == $x) 
                        echo ' selected="selected" '; 

                    echo ">".$x."px</option>"; 

                } 

            ?>
        </select>
    </p>


   	<p><label for="floating_widget_button_mode"><?php echo translate( 'Floating button action:', 'om-floating-widgets' ); ?></label>

		<select id="floating_widget_button_mode" name="floating_widget_button_mode" >

			<?php           

				$modes = array("nothing", "widget", );

				for ($x = 0; $x < count($modes); $x++) 
				{ 
					echo "<option value='".$modes[$x]."' ";                             

					if($this->fw_options['floating_widget_button_mode'] == $modes[$x]) 
						echo ' selected="selected" '; 

					echo ">".translate( ucfirst ($modes[$x]), 'om-floating-widgets' )."</option>"; 

				} 

			?>
		</select>
	</p>

	<p><label for="floating_widget_rotated"><?php echo translate( 'Rotate Widget Button:', 'om-floating-widgets' ); ?></label>

		<select id="floating_widget_rotated" name="floating_widget_rotated" >

			<?php
				echo "<option value='no' ";                             

					if($this->fw_options['floating_widget_rotated'] == "no") 
						echo ' selected="selected" '; 

				echo ">".translate( 'No', 'om-floating-widgets' )."</option>"; 

				echo "<option value='yes' ";                             

					if($this->fw_options['floating_widget_rotated'] == "yes") 
						echo ' selected="selected" '; 

				echo ">".translate( 'Yes', 'om-floating-widgets' )."</option>"; 
				

			?>
			
		</select>
	</p>

    <p><label for="floating_widget_transition_delay"><?php echo translate( 'Widget delay appearance:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_transition_delay" name="floating_widget_transition_delay" 

    value="<?php echo $this->fw_options['floating_widget_transition_delay']; ?>" 
    placeholder="<?php echo translate( 'In seconds', 'om-floating-widgets' ); ?>" /></p>


    <p><label for="floating_widget_button_background_color"><?php echo translate( 'Widget button background color:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_button_background_color" name="floating_widget_button_background_color" 

    class="jscolor" value="<?php echo $this->fw_options['floating_widget_button_background_color']; ?>" /></p>

    <p><label for="floating_widget_button_font_color"><?php echo translate( 'Widget button font color:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_button_font_color" name="floating_widget_button_font_color" 

    class="jscolor" value="<?php echo $this->fw_options['floating_widget_button_font_color']; ?>" /></p>


    <p><label for="floating_widget_background_color"><?php echo translate( 'Widget background color:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_background_color" name="floating_widget_background_color" 

    class="jscolor" value="<?php echo $this->fw_options['floating_widget_background_color']; ?>" /></p>

    <p><label for="floating_widget_font_color"><?php echo translate( 'Widget font color:', 'om-floating-widgets' ); ?></label>

    <input type="text"  id="floating_widget_font_color" name="floating_widget_font_color" 

    class="jscolor" value="<?php echo $this->fw_options['floating_widget_font_color']; ?>" /></p>



</div>