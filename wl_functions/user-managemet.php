<?php


	if (is_admin()) : // Load only if we are viewing an admin page

		function wl_register_settings() {
			// Register settings and call sanitation functions
			register_setting('wl_theme_options', 'wl_options', 'wl_validate_options');
		}

		add_action('admin_init', 'wl_register_settings');
		
		function wl_getAllCapabilities() {
			$role_exclude = array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10');
			$wp_capabilities = get_role('administrator')->capabilities;
			$wp_capabilities = array_diff_key($wp_capabilities, array_flip($role_exclude));
			return $wp_capabilities;
		}
		
		function wl_getEditableRoleNames() {
			$roles = get_editable_roles();
			$role_arr = array();
			foreach ($roles as $key => $value) {
				if($key != 'administrator'){
					$role_arr[$key] = $value['name'];
				}
			}
			return $role_arr;
		}


		function wl_theme_options() {
			// Add user management page to the addmin user menu
			add_users_page( __('User managemet'),  __('User management'), 'edit_users', 'user-management', 'wl_user_management');	
		}

		add_action('admin_menu', 'wl_theme_options');

		// Function to generate user management page page
		function wl_user_management() {
			$role_exclude = array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10');
			if (!isset($_REQUEST['settings-updated']))
				$_REQUEST['settings-updated'] = false; // This checks whether the form has just been submitted. 
			?>
			<style>
				.right{
					float: right
				}
				.inb{display: inline-block}

				#remove-capability, #remove-role{
					min-width: 150px
				}
				#remove-capability-name, #remove-role-name{
					min-width: 240px
				}
				#add-capability, #add-role{
					min-width: 125px
				}

				#tab-wrapper .tab>.inb{
					margin:10px 0;
					min-width: 270px
				}
				#tab-wrapper .tab>.inb label{
					margin-right: 10px
				}

				/*Toggle button*/
				.switch{
					position: relative;
					display: inline-block;
					width: 60px;
					height: 32px;
					vertical-align: middle;
					margin-left: 5px
				}
				.switch input{display: none}
				.slider{
					position: absolute;
					cursor: pointer;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					background-color: #ccc;
					-webkit-transition:.4s;
					transition: .4s
				}
				.slider:before{
					position: absolute;
					content: "";
					height: 24px;
					width: 24px;
					left: 4px;
					bottom: 4px;
					background-color: #fff;
					-webkit-transition:.4s;
					transition: .4s
				}
				input:checked+.slider{
					background-color: yellowgreen;

				}
				input:checked+.slider:before{
					-webkit-transform:translatex(28px);
					-ms-transform:translatex(28px);
					transform:translatex(28px);
				}
				.slider.round{
					border-radius: 34px;
				}
				.slider.round:before{
					border-radius: 50%
				}
			</style>
			
			<div class="wrap">
				<form id="capabilities" method="post" action="">
					<button class="right" id="remove-role"><?php _e('Remove role'); ?></button>
					<select class="right" id="remove-role-name">
						
					</select>
					<button class="right" id="add-role"><?php _e('Add role'); ?></button>
					<input class="right" type="text" id="add-role-name" /><br /><br />
					<button class="right" id="remove-capability"><?php _e('Remove capability'); ?></button>
					<select class="right" id="remove-capability-name">
						
					</select>
					<button class="right" id="add-capability"><?php _e('Add capability'); ?></button>
					<input class="right" type="text" id="add-capability-name" />
					
					<script type="text/javascript">
							function showTab(data, id) {
								jQuery(data).siblings("a").removeClass("nav-tab-active");
								jQuery(data).addClass("nav-tab-active");
								jQuery(".tab").addClass("hidden");
								jQuery(".tab" + id).removeClass("hidden");
							}
					</script>

						<?php
							echo "<h2>" . wp_get_theme() . __(' User managemet') . "</h2>";
						?>
						<h2 class="nav-tab-wrapper" id="nav-tab-wrapper">
							<a href="javascript: void(0)" onclick="showTab(this, 1)" class="nav-tab nav-tab-active">Loading...</a>
						</h2>


					<?php if (false !== $_REQUEST['settings-updated']) : ?>
							<div class="updated fade"><p><strong><?php _e('Options saved'); ?></strong></p></div>
					<?php endif; // If the form has just been submitted, this shows the notification  ?>

				
					<div id="tab-wrapper">
						
					</div>

					<p class="submit"><input id="submit" type="submit" class="button-primary" value="Save capabilities" /></p>

				</form>
				<script>
					jQuery(document).ready(function ($) {
						var changes = {};
						$('body').on('click', '#add-role', function (e) {
							e.preventDefault();
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							var val = $('#add-role-name').val();
							$.post(end_point, {'action': 'add_role', 'new_role': val}, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									alert(response.message);
									laod_data();
								} else {
									alert(response.message);
								}
								
							}).fail(function (response) {
								alert("<?php _e("Error! Please refresh the page and try again1."); ?>");
							});
						});
						$('body').on('click', '#remove-role', function (e) {
							e.preventDefault();
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							var val = $('#remove-role-name').val();
							$.post(end_point, {'action': 'remove_role', 'remove_role': val}, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									alert(response.message);
									laod_data();
								} else {
									alert(response.message);
								}

							}).fail(function (response) {
								alert("<?php _e("Error! Please refresh the page and try again2."); ?>");
							});
						});
						$('body').on('click', '#add-capability', function (e) {
							e.preventDefault();
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							var val = $('#add-capability-name').val();
							$.post(end_point, {'action': 'add_capability', 'add_capability': val}, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									alert(response.message);
									laod_data();
								} else {
									alert(response.message);
								}
								
							}).fail(function (response) {
								alert("<?php _e("Error! Please refresh the page and try again3."); ?>");
							});
						});
						$('body').on('click', '#remove-capability', function (e) {
							e.preventDefault();
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							var val = $('#remove-capability-name').val();
							$.post(end_point, {'action': 'remove_capability', 'remove_capability': val}, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									alert(response.message);
									laod_data();
								} else {
									alert(response.message);
								}

							}).fail(function (response) {
				//				console.log(response);
								alert("<?php _e("Error! Please refresh the page and try again4."); ?>");
							});
						});
						$('body').on('click', '#submit', function (e) {
							e.preventDefault();
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							var val = JSON.stringify(changes);
							console.log(val); 
							input = {'action': 'save_capability', 'data': changes};
							console.log(input);
							$.post(end_point, input, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									console.log(response.message);
									alert(response.message);
								} else {
									alert(response.message);
								}

							}).fail(function (response) {
								console.log(response);
								alert("<?php _e("Error! Please refresh the page and try again5."); ?>");
							});
						});
						$('body').on('change', 'input[type="checkbox"]', function (e) {
							role = $(this).attr('data-role');
							capability = $(this).attr('data-capability');
							if (!(role in changes)) {
								changes[role] = {};
							}
							if(capability in changes[role]){
								delete changes[role][capability];
							}else{
								if($(this).is(':checked')){

									changes[role][capability] = 1;
								}else{
									changes[role][capability] = 0;

								}
							}
							if(Object.keys(changes[role]).length == 0){
								delete changes[role];
							}
							console.log(JSON.stringify(changes)); 
						});
						function load_combo_roles(){
							comboRoles = '<option value="0"> -------- </option>';
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							$.post(end_point, {'action': 'get_editable_roles', }, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									roles = response.data;
									for (var key in roles) {
										comboRoles = comboRoles + '<option value="' + key + '">' + roles[key] + '</option>';
									}
									$('#remove-role-name').html(comboRoles);
								} else {
									alert(response.message);
								}

							}).fail(function (response) {
								alert("<?php _e("Error! Please refresh the page and try again6."); ?>");
							});
						}
						function load_combo_capabilities(){
							comboCapabilities = '<option value="0"> -------- </option>';
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							$.post(end_point, {'action': 'get_capabilities', }, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									capabilities = response.data;
									for (var key in capabilities) {
										comboCapabilities = comboCapabilities + '<option value="' + key + '">' + key + '</option>';
									}
									$('#remove-capability-name').html(comboCapabilities);
								} else {
									alert(response.message);
								}

							}).fail(function (response) {
								alert("<?php _e("Error! Please refresh the page and try again7."); ?>");
							});
						}
						function load_role_tabs(){
//							tab-wrapper
//							nav-tab-wrapper
							navContent = '';
							tabContent = '';
							var end_point = "<?php echo admin_url('admin-ajax.php'); ?>";
							$.post(end_point, {'action': 'get_roles', }, function (response) {

								var response = JSON.parse(response);
								if (response.result == 'success') {
									allCap = response.data.administrator.capabilities;
									delete response.data.administrator;
									roles = response.data;
									capability = '';
									i = 1;
									for (var role in roles) {
										hidden = (i==1)?"":"hidden";
										active = (i==1)?"nav-tab-active":"";
										navContent += '<a href="javascript: void(0)" onclick="showTab(this, '+i+')" class="nav-tab '+active+'">'+roles[role].name+'</a>';
										tabContent += '<div class="tab tab'+i+' '+hidden+'">';
										
										for (var key in allCap) {
											checked = roles[role].capabilities[key]?'checked="checked"':'';
											capability = '<div class="inb">' +
													'<label for="'+role+'-'+key+'" class="switch">' +
														'<input id="'+role+'-'+key+'" type="checkbox" name="'+role+'-'+key+'" '+checked+' data-role="'+role+'" data-capability="'+key+'" '+' />'+
														'<span class="slider round"></span>'+
													'</label>' + 
													'<span class="inb">'+key+'</span>'+
												'</div>';
											tabContent +=  capability;
										}									
										tabContent += '</div>';
										i++;
									}
									$('#tab-wrapper').html(tabContent);
									$('#nav-tab-wrapper').html(navContent);
								} else {
									alert(response.message);
								}

							}).fail(function (response) {
								alert("<?php _e("Error! Please refresh the page and try again8."); ?>");
							});
						}
						
						function laod_data() {
							load_combo_roles();
							load_combo_capabilities();
							load_role_tabs();
						}
						laod_data();
					})
				</script>
			</div>

			<?php
		}





endif;  // EndIf is_admin()
