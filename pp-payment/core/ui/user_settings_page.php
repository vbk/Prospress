<div class="wrap">
<form method='POST'>
<h2><?php _e("Payment Settings", 'prospress') ?></h2>
<?php // wpi_qc($user_settings); ?>
	<style>
		<?php if($user_settings[paypal_allow] != 'true') : ?>
			.paypal_settings { display:none; }
		<?php endif; ?>
		<?php if($user_settings[cc_allow] != 'true') : ?>
			.gateway_info{ display:none; }
		<?php endif; ?>
		<?php if($user_settings[draft_allow] != 'true') : ?>
			.draft_info{ display:none; }
		<?php endif; ?>
	</style>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function(jQuery) {
		jQuery("#pp_invoice_settings_page ul").idTabs();
			// add/remove payments from default payment dropdown based on used payment venues
			jQuery(".pp_invoice_payment_option input").change(function() {
				var payment_venue = jQuery(this).attr('id');
				var action = (jQuery(this).is(":checked") ? "turn_on" : "turn_off");
				if(action == "turn_on") {
					if(payment_venue == 'cc')
						jQuery("#default_payment_venue").append("<option class='cc' value='cc'>Credit Card</option>");
					if(payment_venue == 'paypal')
						jQuery("#default_payment_venue").append("<option class='paypal' value='paypal'>PayPal</option>");
					if(payment_venue == 'draft')
						jQuery("#default_payment_venue").append("<option class='draft'  value='draft'>Draft</option>");
				} else {
					if(payment_venue == 'cc')
						jQuery("#default_payment_venue .cc").remove();
					if(payment_venue == 'paypal')
						jQuery("#default_payment_venue .paypal").remove();
					if(payment_venue == 'draft')
						jQuery("#default_payment_venue .draft").remove();
				}
			});
			jQuery('.pp_invoice_payment_option #paypal').click(function() {
				if(jQuery(this).is(":checked"))
					jQuery(".paypal_settings").show();
				else
					jQuery(".paypal_settings").hide();
			});
			jQuery('.pp_invoice_payment_option #cc').click(function() {
				if(jQuery(this).is(":checked"))
					jQuery(".gateway_info").show();
				else
					jQuery(".gateway_info").hide();
			});
			jQuery('.pp_invoice_payment_option #draft').click(function() {
				if(jQuery(this).is(":checked"))
					jQuery(".draft_info").show();
				else
					jQuery(".draft_info").hide();
			});
		});
		//]]>
	</script>
	<div id="selling" >
 		<table class="form-table">
			<tr>
				<th width="200">Basic Settings</th>
				<td>
				<?php echo wpi_checkbox("group=pp_invoice_user_settings&name=show_address_on_invoice&label=Display my address on invoice page (set your address under <a href='profile.php#billing_info'>Profile</a>).&value=true", $user_settings[show_address_on_invoice]); ?><br />
				Tax Label: <?php echo wpi_input("group=pp_invoice_user_settings&name=tax_label&value={$user_settings[tax_label]}&style=width: 80px;"); ?><br />
				</td>
			</tr>
			<tr>
				<th><?php _e("Payment Settings:");?></th>
				<td>
					<?php echo wpi_checkbox("group=pp_invoice_user_settings&name=can_change_payment_method&label=Payer can choose payment method.&value=true", $user_settings[can_change_payment_method]); ?><br />
					<?php echo wpi_checkbox("group=pp_invoice_user_settings&name=payment_received_notification&label=Notify me when payment is made.&value=true", $user_settings[payment_received_notification]); ?><br />
				</td>
			</tr>
			<tr>
				<th><?php _e("Payment Types:");?></th>
				<td>
					<div class="pp_invoice_payment_option"><?php // Do not remove div.pp_invoice_payment_option -> it is used by jQuery to select payment venue checkboxes ?>
					<?php echo wpi_checkbox("group=pp_invoice_user_settings&name=paypal_allow&label=PayPal.&value=true&id=paypal", $user_settings[paypal_allow]); ?><br />
					<?php echo wpi_checkbox("group=pp_invoice_user_settings&name=cc_allow&label=Credit Cards.&value=true&id=cc", $user_settings[cc_allow]); ?><br />
					<?php echo wpi_checkbox("group=pp_invoice_user_settings&name=draft_allow&label=Bank Transfer.&value=true&id=draft", $user_settings[draft_allow]); ?><br />
					</div>
				</td>
			</tr>
			<tr>
				<th>Default Payment Method:</th>
				<td>
 				<select id="default_payment_venue" name="pp_invoice_user_settings[default_payment_venue]" style="width: 100px;">
					<?php if($user_settings['paypal_allow']): ?>
						<option class="paypal" value="paypal" <?php selected( $user_settings[default_payment_venue], 'paypal' ); ?>>PayPal</option>
					<?php endif; ?>
					<?php if($user_settings['cc_allow']): ?>
					<option class="cc" value="cc"  <?php selected( $user_settings[default_payment_venue], 'cc' ); ?>>Credit Card</option>
					<?php endif; ?>
					<?php if($user_settings['draft_allow']): ?>
					<option class="draft" value="draft"  <?php selected( $user_settings[default_payment_venue], 'draft' ); ?>>Bank Transfer</option>
					<?php endif; ?>
				</select>
			</td>
			</tr>
			<tr class="paypal_settings">
				<th><?php _e( 'PayPal Settings:', 'prospress' ); ?></th>
				<td>
				<table>
					<tr class="paypal_settings">
						<th><?php _e("PayPal Username: "); ?></th>
						<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[paypal_address]',$user_settings[paypal_address]); ?></td>
					</tr>
					<?php //if( is_super_admin() ) {?>
					<tr class="paypal_settings">
						<th>&nbsp;</th>
						<td><?php echo wpi_checkbox('group=pp_invoice_user_settings&name=paypal_sandbox&value=true&label=PayPal Sandbox Mode',$user_settings[paypal_sandbox]); ?></td>
					</tr>
					<?php //} ?>
				</table>
				</td>
			</tr>
			<tr class="gateway_info">
				<th><?php _e( 'Credit Card Settings', 'prospress' ); ?></th>
				<td>
				<table>
				<tr class="gateway_info">
					<th><a class="pp_invoice_tooltip" title="<?php _e('Your credit card processor will provide you with a gateway username.', 'prospress'); ?>"><?php _e('Gateway Username', 'prospress'); ?></a></th>
					<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[gateway_username]',$user_settings[gateway_username], ' AUTOCOMPLETE="off"  '); ?></td>
				</tr>
				<tr class="gateway_info">
					<th><a class="pp_invoice_tooltip" title="<?php _e("You will be able to generate this in your credit card processor's control panel.", 'prospress'); ?>"><?php _e('Gateway Transaction Key', 'prospress'); ?></a></th>
					<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[gateway_tran_key]',$user_settings[gateway_tran_key], ' AUTOCOMPLETE="off"  '); ?></td>
				<tr class="gateway_info payment_info">
					<th width="300"><a class="pp_invoice_tooltip"  title="<?php _e('This is the URL provided to you by your credit card processing company.', 'prospress'); ?>"><?php _e('Gateway URL', 'prospress'); ?></a></th>
					<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[gateway_url]',$user_settings[gateway_url]); ?><br />
					</td>
				</tr>
				<tr class="gateway_info">
					<th width="300"><a class="pp_invoice_tooltip"  title="<?php _e('Get this from your credit card processor. If the transactions are not going through, this character is most likely wrong.', 'prospress'); ?>"><?php _e('Delimiter Character', 'prospress'); ?></a></th>
					<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[gateway_delim_char]',$user_settings[gateway_delim_char]); ?>
				</tr>
				<tr class="gateway_info">
					<th width="300"><a class="pp_invoice_tooltip" title="<?php _e('Authorize.net default is blank. Otherwise, get this from your credit card processor. If the transactions are going through, but getting strange responses, this character is most likely wrong.', 'prospress'); ?>"><?php _e('Encapsulation Character', 'prospress'); ?></a></th>
					<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[gateway_encap_char]',$user_settings[gateway_encap_char]); ?></td>
				</tr>
				<tr class="gateway_info">
					<th width="300"><?php _e('Security: MD5 Hash', 'prospress'); ?></th>
					<td><?php echo pp_invoice_draw_inputfield('pp_invoice_user_settings[gateway_MD5Hash]',$user_settings[gateway_MD5Hash]); ?></td>
				</tr>
				<tr class="gateway_info">
					<th><?php _e('Delim Data:', 'prospress'); ?></th>
					<td><?php echo pp_invoice_draw_select('pp_invoice_user_settings[gateway_delim_data]',array("TRUE" => "True","FALSE" => "False"), $user_settings[gateway_delim_data]); ?></td>
				</tr>
				</table>
			</td>
			</tr>
			<tr class="draft_info">
				<th><?php _e( 'Draft Instructions:', 'prospress' ); ?></th>
				<td><textarea name='pp_invoice_user_settings[draft_text]'><?php echo $user_settings[draft_text]; ?></textarea></td>
			</tr>
		</table>
		<div class="clear"></div>
		<p class="submit">
			<input type="submit" value="Save Settings" class="button-primary"></div>
		</p>
	</div>
</form>
</div>