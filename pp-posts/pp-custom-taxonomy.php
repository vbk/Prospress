<?php

define( 'PP_TAX_URL', admin_url( '/admin.php?page=custom_taxonomy_manage' ) );
define( 'PP_ADD_TAX_URL', admin_url( '/admin.php?page=custom_taxonomy_add' ) );

function pp_taxonomy_menus() {
	global $pp_core_admin_page;
	$base_title = 'Taxonomies';
	$base_page = "custom_taxonomy";

	add_submenu_page( 'hidden', 'Add New', 'Add New', 'administrator', $base_page.'_add', 'pp_add_new_page' );
	add_submenu_page( 'Prospress', __( 'Custom Prospress Taxonomies', 'prospress' ), __( 'Taxonomies', 'prospress' ), 'administrator', $base_page.'_manage', 'pp_manage_taxonomies' );
}
add_action('admin_menu', 'pp_taxonomy_menus');

function pp_manage_taxonomies() {
	global $market_system;
	?>
	<div class="wrap">
		<?php
		//check for success/error messages
		if ( isset( $_GET[ 'pp_msg' ] ) ) { ?>
		    <div id="message" class="updated">
				<p>
		    	<?php switch( $_GET[ 'pp_msg' ] ){
					case 'add': 
							_e('Taxonomy created.', 'prospress' );
							$edit_tax_url = admin_url( 'edit-tags.php' );
							$edit_tax_url = '<a href="' . add_query_arg( array( 'post_type' => $market_system->name(), 'taxonomy' => $_GET[ 'taxonomy' ] ), $edit_tax_url ) . '">' . $market_system->display_name() . '</a>';
							printf( __( 'Taxonomy created. You can add elements to it under the %s menu.', 'prospress' ), $edit_tax_url );
					        break;
					case 'del': 
							_e('Taxonomy deleted.', 'prospress' );
							break;
					case 'edit': 
							_e('Taxonomy updated.', 'prospress' );
					        break;
					}?>
				</p>
		    </div>
		    <?php
		}
		screen_icon( 'prospress' );
		?>
		<h2><?php echo $market_system->singular_name() . ' '; _e( 'Taxonomies', 'prospress' ) ?><a href="<?php echo PP_ADD_TAX_URL; ?>" class="button add-new-h2">Add New</a></h2>
		<p><?php printf( __( 'Taxonomies are used to classify and categorize %s. They can be used to group %s with similar characteristics and make it easier to find %s with a certain characteristic.', 'prospress' ), $market_system->display_name(), $market_system->display_name(), $market_system->display_name() ) ?></p>
		<p><?php printf( __( 'For example, to classify Motorcyle %s, an <em>Engine Size</em> taxonomy could be created to include <em>Less than 250cc</em>, <em>251 to 600cc</em> and <em>Greater than 600cc</em> categories.', 'prospress' ), $market_system->display_name() ) ?></p>
		<?php 
		$pp_tax_types = get_option( 'pp_custom_taxonomies' );
		if( !empty( $pp_tax_types ) ) { ?>
	        <table class="widefat post fixed">
				<thead>
		        	<tr>
		            	<th><strong><?php _e('Name', 'prospress' );?></strong></th>
		                <th><strong><?php _e('Label', 'prospress' );?></strong></th>
		                <th><strong><?php _e('Singular Label', 'prospress' );?></strong></th>
		            	<th><strong><?php _e('Actions', 'prospress' );?></strong></th>
		            </tr>
				</thead>
				<tfoot>
		        	<tr>
		            	<th><strong><?php _e('Name', 'prospress' );?></strong></th>
		                <th><strong><?php _e('Label', 'prospress' );?></strong></th>
		                <th><strong><?php _e('Singular Label', 'prospress' );?></strong></th>
		            	<th><strong><?php _e('Actions', 'prospress' );?></strong></th>
		            </tr>
				</tfoot>
				<tbody>
		        <?php
				foreach ($pp_tax_types as $tax_name => $pp_tax_type ) {
					$del_url = add_query_arg( 'deltax', $tax_name, PP_TAX_URL );
					$del_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $del_url, 'pp_delete_tax' ) : $del_url;
					$edit_url = add_query_arg( 'edittax', $tax_name, PP_ADD_TAX_URL );
					$edit_url = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $edit_url, 'pp_edit_tax' ) : $edit_url;
					$edit_types_url = add_query_arg( array( 'post_type' => $market_system->name(), 'taxonomy' => $tax_name ), $edit_tax_url );
				?>
		        	<tr>
		            	<td valign="top"><?php echo stripslashes( $tax_name ); ?></td>
		                <td valign="top"><?php echo stripslashes( $pp_tax_type[ 'label' ] ); ?></td>
		                <td valign="top"><?php echo stripslashes( $pp_tax_type[ 'labels' ][ 'singular_label' ] ); ?></td>
		            	<td valign="top">
							<div class="prospress-actions">
								<ul class="actions-list">
									<li class="base"><?php _e( 'Take Action:', 'prospress' );?></li>
									<li class="action"><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit Taxonomy', 'prospress' );?></a></li>
									<li class="action"><a href="<?php echo $del_url; ?>"><?php _e( 'Delete Taxonomy', 'prospress' );?></a></li>
									<li class="action"><a href="<?php echo $edit_types_url; ?>"><?php printf( __( 'Add New %s', 'prospress' ), $pp_tax_type[ 'labels' ][ 'singular_label' ] );?></a></li>
								</ul>
							</div>	
		            	</td>
					</tr>
				<?php
				} ?>
				</tbody>
			</table>
			<p><?php _e( 'Note: Deleting a taxonomy does not delete the posts and taxonomy types associated with it.', 'prospress' ) ?></p>
		<?php
		}else{
			echo '<p><a href="' . PP_ADD_TAX_URL . '" class="button add-new-h2">' . __( "Add New", 'prospress' ) . '</a></p>';
		}
		echo '</div>';
}

// page to add a new taxonomy 
function pp_add_new_page() {

	if ( isset( $_GET[ 'edittax' ] ) ) {
		check_admin_referer('pp_edit_tax');

		$pp_tax_submit_name = __( 'Edit Taxonomy', 'prospress' );
		$tax_to_edit = $_GET[ 'edittax' ];
		$pp_taxonomies = get_option( 'pp_custom_taxonomies' );
		$pp_tax_label = $pp_taxonomies[ $tax_to_edit ][ 'label' ];
		$pp_singular_label = $pp_taxonomies[ $tax_to_edit ][ 'labels' ][ 'singular_label' ];
		$pp_add_or_edit = 'pp_edit_tax';
	}else{
		$pp_tax_submit_name = __( 'Create Taxonomy', 'prospress' );
		$tax_to_edit = '';
		$pp_tax_label = '';
		$pp_singular_label = '';
		$pp_add_or_edit = 'pp_add_tax';
	}

	if ( isset( $_GET[ 'pp_error' ] ) ) {
		$pp_tax_label = ( isset( $_GET[ 'l' ] ) ) ? $_GET[ 'l' ]: '';
		$pp_singular_label = ( isset( $_GET[ 'sl' ] ) ) ? $_GET[ 'sl' ]: '';
	}
	?>
	<div class="wrap">
	<?php 
		if ( isset( $_GET['pp_error' ] ) && $_GET['pp_error'] == 2 ){
			echo '<div class="error">';
			echo '<p>' . __( 'Taxonomy name is required.', 'prospress' ) . '<p>';
			echo '</div>';
		}
	?>
	<table border="0" cellspacing="10">
		<tr>
	        <td valign="top">
				<h2><?php echo $pp_tax_submit_name; ?></a></h2>
	        	<p><?php _e('Only a <strong>Taxonomy Name</strong> is required to create a custom taxonomy; however, label and singular labels are recommended.' );?></p>
	            <form method="post" action="">
	                <?php if ( function_exists('wp_nonce_field') )
	                    wp_nonce_field('pp_add_custom_taxonomy'); ?>
	                <?php if ( isset( $_GET[ 'edittax' ] ) ) { ?>
	                <input type="hidden" name="pp_edit_tax" value="<?php echo $tax_to_edit; ?>" />
	                <?php } ?>
	                <table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e( 'Taxonomy Name', 'prospress' ) ?> <span style="color:red;">*</span></th>
							<td>
								<input type="text" name="pp_custom_tax" tabindex="21" value="<?php echo esc_attr( $tax_to_edit ); ?>" />
								<label><?php _e( 'Used to define the taxonomy. Make it short and sweet. e.g. size', 'prospress' ); ?></label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e( 'Plural Label', 'prospress' ) ?></th>
							<td>
								<input type="text" name="label" tabindex="22" value="<?php echo esc_attr( $pp_tax_label ); ?>" />
								<label><?php _e( 'Used to refer to multiple items of this type. e.g. Engine Sizes', 'prospress' ); ?></label>
							</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php _e( 'Singular Label', 'prospress' ) ?></th>
							<td>
								<input type="text" name="singular_label" tabindex="23" value="<?php echo esc_attr( $pp_singular_label ); ?>" />
								<label><?php _e( 'Used to refer to a single item of this type. e.g. Engine Size', 'prospress' ); ?></label>
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" tabindex="24" name="<?php echo $pp_add_or_edit; ?>" value="<?php echo $pp_tax_submit_name; ?>" />
					</p>
	            </form>
	        </td>
		</tr>
	</table>
	</div>
	<?php 
}

function pp_delete_taxonomy() {

	if( !isset( $_GET[ 'deltax' ] ) ) 
		return;

	check_admin_referer( 'pp_delete_tax' );

	$delete = $_GET['deltax'];
	$pp_taxonomies = get_option( 'pp_custom_taxonomies' );

	unset( $pp_taxonomies[ $delete ] );

	update_option( 'pp_custom_taxonomies', $pp_taxonomies );

	wp_redirect( add_query_arg( 'pp_msg', 'del', PP_TAX_URL ) );
}
add_action( 'admin_init', 'pp_delete_taxonomy' );

function pp_register_settings() {
	global $market_system;

	if( !isset( $_POST[ 'pp_add_tax' ] ) && !isset( $_POST[ 'pp_edit_tax' ] ) )
		return;
	elseif( isset( $_POST[ 'pp_add_tax' ] ) || isset( $_POST[ 'pp_edit_tax' ] ) )
		check_admin_referer('pp_add_custom_taxonomy');

	$pp_tax_name = strip_tags( $_POST[ 'pp_custom_tax' ] );

	if ( empty( $pp_tax_name ) ) {
		wp_redirect( add_query_arg( array( 'pp_error' => 2, 'l' => $_POST[ 'label' ], 'sl' => $_POST[ 'singular_label' ]), PP_ADD_TAX_URL ) );		
		exit();
	}

	$pp_new_tax[ 'label' ] 			= ( !$_POST[ 'label' ] ) ? $pp_tax_name : strip_tags( $_POST[ 'label' ] );
	$pp_new_tax[ 'object_type' ] 	= $market_system->name();
	$pp_new_tax[ 'capabilities' ] 	= array( 'assign_terms' => 'edit_prospress_posts' );
	$pp_new_tax[ 'labels' ] 		= array();
	$pp_new_tax[ 'labels' ][ 'singular_label' ]	= ( !$_POST[ 'singular_label' ] ) ? $pp_tax_name : strip_tags( $_POST[ 'singular_label' ] );
	//other taxonomy args are defined dynamically to help future proof custom taxonomies

	$pp_taxonomies = get_option( 'pp_custom_taxonomies' );

	$pp_taxonomies[ strip_tags( $pp_tax_name ) ] = $pp_new_tax;

	update_option( 'pp_custom_taxonomies' , $pp_taxonomies );

	if( isset( $_POST[ 'pp_add_tax' ] ) )
		$msg = 'add';
	elseif ( isset( $_POST[ 'pp_edit_tax' ] ) )
		$msg = 'edit';

	wp_redirect( add_query_arg( array( 'pp_msg' => $msg, 'taxonomy' => $pp_tax_name ), PP_TAX_URL ) );		
	exit();
}
add_action( 'admin_init', 'pp_register_settings' );

function pp_create_custom_taxonomies() {
	global $market_system;

	$pp_tax_types = get_option('pp_custom_taxonomies');

	if ( empty( $pp_tax_types ) )
		return;

	foreach( $pp_tax_types as $pp_tax_name => $pp_tax_type ) {
		$pp_object_type = $pp_tax_type[ 'object_type' ];
		unset( $pp_tax_type[ 'object_type' ] );

		// Define taxonomy parameters dynamically to improve forward compatability
		$pp_tax_type[ 'public' ] 		= true;
		$pp_tax_type[ 'hierarchical' ] 	= true;
		$pp_tax_type[ 'show_ui' ] 		= true;
		$pp_tax_type[ 'show_tagcloud' ]	= false;
		$pp_tax_type[ 'query_var' ] 	= $pp_tax_name;
		$pp_tax_type[ 'rewrite' ] 		= array( 'slug' => $pp_object_type . '/' . $pp_tax_name, 'with_front' => false );
		$pp_tax_type[ 'capabilities' ] 	= array( 'assign_terms' => 'edit_prospress_posts' );
		$pp_tax_type[ 'labels' ][ 'search_items' ] 	= __( 'Search ', 'prospress' ) . $pp_tax_type[ 'label' ];
		$pp_tax_type[ 'labels' ][ 'popular_items' ]	= __( 'Popular ', 'prospress' ) . $pp_tax_type[ 'label' ];
		$pp_tax_type[ 'labels' ][ 'all_items' ] 	= __( 'All ', 'prospress' ) . $pp_tax_type[ 'label' ];
		$pp_tax_type[ 'labels' ][ 'parent_item' ] 	= __( 'Parent ', 'prospress' ) . $pp_tax_type[ 'label' ];
		$pp_tax_type[ 'labels' ][ 'parent_item_colon' ] 	= __( 'Parent ', 'prospress' ) . $pp_tax_type[ 'label' ] . ':';
		$pp_tax_type[ 'labels' ][ 'edit_item' ]		= __( 'Edit ', 'prospress' ) . $pp_tax_type[ 'labels' ][ 'singular_label' ];
		$pp_tax_type[ 'labels' ][ 'update_item' ]	= __( 'Update ', 'prospress' ) . $pp_tax_type[ 'labels' ][ 'singular_label' ];
		$pp_tax_type[ 'labels' ][ 'add_new_item' ]	= __( 'Add New ', 'prospress' ) . $pp_tax_type[ 'labels' ][ 'singular_label' ];
		$pp_tax_type[ 'labels' ][ 'new_item_name' ]	= __( 'New ', 'prospress' ) . $pp_tax_type[ 'labels' ][ 'singular_label' ];

		register_taxonomy( $pp_tax_name,
			$pp_object_type,
			$pp_tax_type 
			);
	}
}
add_action( 'init', 'pp_create_custom_taxonomies', 0 );