<?php
	
/* AJAX function for add role */
	function wl_add_role($test) {

		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			$new_role = $_POST['new_role'];
			$new_role_slug = str_replace(' ', '_', strtolower( $new_role));
			add_role(
				$new_role_slug,
				__( $new_role ),
				array(
//					'read'         => true,  // true allows this capability
//					'edit_posts'   => true,
				)
			);
			$result = 'success';
			$message = _("Role added.");
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}

		echo json_encode(array('result'=>$result, 'message'=>$message));
		wp_die();
	}

	add_action('wp_ajax_add_role', 'wl_add_role');
//add_action( 'wp_ajax_nopriv_add_role', 'wl_add_role' );
/* end AJAX function for add role */
	
/* AJAX function for remove role */
	function wl_remove_role($test) {

		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			$remove_role = $_POST['remove_role'];
			if($remove_role == "0"){
				$result = 'error';
				$message = _("Please select role to delete.");
			}else if( get_role($remove_role) ){
				remove_role( $remove_role );
				$result = 'success';
				$message = _("Role removed.");
			}else{
				$result = 'error';
				$message = _("Role doesn't exist.");
			}
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}

		echo json_encode(array('result'=>$result, 'message'=>$message));
		wp_die();
	}

	add_action('wp_ajax_remove_role', 'wl_remove_role');
//add_action( 'wp_ajax_nopriv_remove_role', 'wl_remove_role' );
/* end AJAX function for remove role*/
	
/* AJAX function for add capability */
	function wl_add_capability($test) {

		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			$addCapability = trim($_POST['add_capability']);
			if(preg_match("/^[a-zA-Z0-9_]+$/",$addCapability)){
				$roles = get_editable_roles();
				$role = get_role('administrator');
				$role->add_cap($addCapability);			
				$result = 'success';
				$message = _("Capability added.");
			}else{
				
				$result = 'error';
				$message = _("You can use only string and underscore");
				
			}
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}

		echo json_encode(array('result'=>$result, 'message'=>$message));
		wp_die();
	}

	add_action('wp_ajax_add_capability', 'wl_add_capability');
//add_action( 'wp_ajax_nopriv_add_capability', 'wl_add_capability' );
/* end AJAX function for add capability */
	
/* AJAX function for remove capability */
	function wl_remove_capability($test) {
		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			$removeCapability = trim($_POST['remove_capability']);
				$role = get_role('administrator');
				if($role->has_cap($removeCapability)){
					$role->remove_cap($removeCapability);
				$result = 'success';
				$message = _("Capability removed.");
				}else{
					$result = 'error';
					$message = _("Capability doesn't exist.");
				}
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}

		echo json_encode(array('result'=>$result, 'message'=>$message));
		wp_die();
	}

	add_action('wp_ajax_remove_capability', 'wl_remove_capability');
//add_action( 'wp_ajax_nopriv_remove_capability', 'wl_remove_capability' );
/* end AJAX function for remove capability */
	
/* AJAX function for get editable roles */
	function wl_get_editable_roles($test) {

		$wp_capabilities = array();
		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			
			$wp_capabilities = wl_getEditableRoleNames();
			
			$result = 'success';
			$message = _("Roles loaded.");
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}
		
		echo json_encode(array('result'=>$result, 'data'=>$wp_capabilities, 'message' => $message));
		wp_die();
	}

	add_action('wp_ajax_get_editable_roles', 'wl_get_editable_roles');
//add_action( 'wp_ajax_nopriv_get_editable_roles', 'wl_get_editable_roles' );
/* end AJAX function for get editable roles */
	
/* AJAX function for get roles */
	function wl_get_roles($test) {
		$wp_roles = array();
		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			$roles = get_editable_roles();
			$capabilities_exclude = array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10');
			foreach ($roles as $key => $value) {
				$wp_capabilities = $roles[$key]['capabilities'];
				$wp_capabilities = array_diff_key($wp_capabilities, array_flip($capabilities_exclude));
				$wp_roles[$key]['capabilities'] = $wp_capabilities;
				$wp_roles[$key]['name'] = $value['name'];
			}
			$result = 'success';
			$message = _("Roles loaded.");
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}
		
		echo json_encode(array('result'=>$result, 'data'=>$wp_roles, 'message' => $message));
		wp_die();
	}

	add_action('wp_ajax_get_roles', 'wl_get_roles');
//add_action( 'wp_ajax_nopriv_get_roles', 'wl_get_roles' );
/* end AJAX function for get roles */
	
/* AJAX function for get capabilities */
	function wl_get_capabilities($test) {

		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			
			$wp_capabilities = wl_getAllCapabilities();
			
			$result = 'success';
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}
		
		echo json_encode(array('result'=>$result, 'data'=>$wp_capabilities));
		wp_die();
	}

	add_action('wp_ajax_get_capabilities', 'wl_get_capabilities');
//add_action( 'wp_ajax_nopriv_get_capabilities', 'wl_get_capabilities' );
/* end AJAX function for get capability */
	
/* AJAX function for get capabilities */
	function wl_save_capability($test) {
		$data = array();
		if (current_user_can('administrator') || current_user_can('manage_roles')) {
			$data = $_POST['data'];
			$i=0;$j=0;
			foreach ($data as $role => $capabilities) {
				foreach ($capabilities as $key => $value) {
					$role_obj = get_role($role);
					if($value){
						$role_obj->add_cap($key);
					}else{
						$role_obj->remove_cap($key);
					}
					$j++;
				}
				$i++;
			}
			$result = 'success';
			$message = _("Capabilities saved.");
		}else{
			$result = 'error';
			$message = _("You don't have rights to do that");
		}
		echo json_encode(array('result'=>$result, 'message'=>$message));
		wp_die();
	}

	add_action('wp_ajax_save_capability', 'wl_save_capability');
//add_action( 'wp_ajax_nopriv_save_capability', 'wl_save_capability' );
/* end AJAX function for get capability */