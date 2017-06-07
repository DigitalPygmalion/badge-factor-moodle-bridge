<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://bitbucket.org/django_d/
 * @since      1.0.0
 *
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Badge_Factor_Moodle_Bridge
 * @subpackage Badge_Factor_Moodle_Bridge/public
 * @author     Django Doucet <doucet.django@uqam.ca>
 */
class Badge_Factor_Moodle_Bridge_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Badge_Factor_Moodle_Bridge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Badge_Factor_Moodle_Bridge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/badge-factor-moodle-bridge-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Badge_Factor_Moodle_Bridge_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Badge_Factor_Moodle_Bridge_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

//		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/badge-factor-moodle-bridge-public.js', array( 'jquery' ), $this->version, false );
//		wp_localize_script( $this->plugin_name, 'admin_url', array('ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
//		wp_localize_script('script', 'ajaxurl', admin_url( 'admin-ajax.php' ) );

	}
	
	/**
     * Badge Factor Moodle Bridge ajaxurl.
     *
     * @since    1.0.0
     */
/*	public function badge_factor_moodle_bridge_ajaxurl() {
		echo '<script type="text/javascript">
				var ajaxurl = "' . admin_url('admin-ajax.php') . '";
			  </script>';
	}*/
	
    /**
     * Process & validate data sent from Moodle, create BadgeFactor submission.
     *
     * @since    1.0.0
     */
    public function get_assertion() {

        /**
         * USE CASE
         * Recieves Moodle cURL
         * Validates Data
		 * 	api_key
         *  authenticate (user exists)
         *  user
         *  date/timestamp
         *  badge_id
		 * 	preuves	???
         *  
         * Generate Badge
         * [Features - TODO] 
		 *  Return Response to Moodle
         *   Status
         *   URL
         *
         */

		/* cURL processing */
		echo "ok"; //for CURL
		$fp = fopen('php://input', 'r'); //creates ressource
		$rawData = stream_get_contents($fp); //converts ressource to array
		$object = json_decode($rawData); //converts to array
		
		// Validate Shared encryption secret (Wordpress & Moodle)
		$wp_shared_key = get_option('wp2m_shared_secret');
		$moodle_shared_key = $object->api_key;

		if ( $moodle_shared_key !== $wp_shared_key ){
			echo json_encode(array('handshake'=>false, 'message'=>__('Fail: Shared encryption secret')));
			wp_die();
		}
		
/* del *//* Local config */
		setlocale(LC_TIME, "fr_FR");
		date_default_timezone_set('America/Montreal');
		
		/* User validation */
		$moodleuser = get_user_by('email', $object->record->user_email);
		
		if (!($moodleuser instanceof WP_User) ) {
//			error_log( 'Non-WP user: ' . $object->record->user_email );
			wp_die();
		}

		if ( ($moodleuser instanceof WP_User) ) {
//			error_log( 'WP user : ' . $object->record->user_email );
			$user_id = $moodleuser->ID;
						
			/* Course info */
			$course_idnumber = $object->record->idnumber;
			$course_moduleid = $object->record->coursemoduleid;
			$course_name = $object->record->coursename;
			$user_fullname = $object->record->fullname;
			
		   /**
			* Retrieve a post_id given its (moodle)$course_idnumber.
			*
			* @param string $post_type ('badges')
			* @return string $post_id
			*/
			if ( !isset($object->record->idnumber) ) {
				error_log( 'Moodle Course ID Number not set : ' . $object->record->idnumber );
				wp_die();
			}
			if ( isset($object->record->idnumber) ) {
				$moodle_idnumber = $object->record->idnumber;
					function get_moodle_idnumber( $moodle_idnumber ){
						wp_reset_postdata();
						$myargs = array (
							'post_type' => 'badges',
							'meta_value' => $moodle_idnumber
						);
						$myquery = new WP_Query($myargs);
						if($myquery->have_posts() ) :
							while($myquery->have_posts() ) : $myquery->the_post(); 
								return get_the_ID();
							endwhile;
						endif;
						wp_reset_postdata();
					}
					
				$achievement_id = get_moodle_idnumber( $moodle_idnumber );
			}
			
//			error_log( '$achievement_id: ' . $achievement_id . ', $user_id: ' . $user_id . ', $title: ' . $title);
			
/* NEEDED? : Badge META Content */
	
			/* Basic Output: Badge title + content	*/
			$mdl_post_content = $object->record->fullname . ', a complété l\'activité ' . $object->record->coursemoduleid . ' du cours ' . $object->record->coursename . ' le ' . strftime('%c');
			
			$title = $object->record->coursename;	//$mdl_post_title
			$content = $mdl_post_content;
/* NEEDED? */
			
			
			/* achievement validation */
			if ( !isset($achievement_id) ){	
				error_log( 'No Badge found with that Moodle Course ID!' );
				wp_die();
			}
			
			if ( isset($achievement_id) ){	
				$GLOBALS['badgefactor']->create_submission( $achievement_id, $title, $content, $user_id);
			}
	
		}

	wp_die();
		
    }
	
	/**
     * Get list of Badges (Title, courseIDnumber)
     *
     * @since    1.0.0
     */
    public function get_badge_list_wp2moodle() {

        /**
         * USE CASE
		 * TinyMCE Modal popup displays list of badges, select one and prepopulate shortcode for use with wp2Moodle. 
         * 		Return list of badges 
		 * 		Fields : Title, courseIDnumber
         */

// Is this even a POST?
//		if ($_POST) { //Recieves Moodle _POST 
		
// Is validation even a necessary?
			// Validate Shared encryption secret (Wordpress & Moodle)
/*			$wp_shared_key = get_option('wp2m_shared_secret');
			$api_key = $_REQUEST['api_key'];
			
			if ( $api_key !== $wp_shared_key ){	
				echo json_encode(array('handshake'=>false, 'message'=>__('Wrong API Key.')));
				wp_die();
			}*/

			/**
			* Retrieve a list of posts from $post_type('badges') for TinyMCE insertion (wp2Moodle).
			*
			* @return mixed ($post_title Page title) + ('meta_value' => $course_idnumber)
			* group_591c4e2ccec4c
			* field_591c4e41b604b
			*/			

/*			$course_idnumber = $_REQUEST['course_idnumber']; //$course_idnumber
			$page_title = $_REQUEST['title'];//'La scénarisation pédagogique';// */
					
			wp_reset_postdata();
			
			$myargs = array (
				'post_type' => 'badges',
			);
			$badges = array();
			$myquery = new WP_Query($myargs);
			if($myquery->have_posts() ) :
				while($myquery->have_posts() ) : $myquery->the_post();
					$meta = get_field_object( 'field_591c4e41b604b', get_the_ID() );
//						echo serialize($meta) . "\r\n";
//						echo $meta['label'] . ': ' . $meta['value'];
//					echo 'course_title:' . get_the_title() . ', course_id:' . $meta['value']; 
					
					$badges[] = array(
						'course_title' => html_entity_decode(get_the_title()),
						'course_id' => $meta['value']
					);
				endwhile;
			endif;
			wp_reset_postdata();
			// output
//			echo json_encode( $badges );
			$output = json_encode( $badges );
			print $output;
//			return true;
			wp_die();
    }
	
	
	/**
     * Get text options for wp2m shortcode insert.
     *
     * @since    1.0.0
     */
	public function get_wp2moodle_options(){
			$bridge_options = get_option( 'badge_factor_moodle_bridge_settings' );
			$unauth_moodle_text = $bridge_options['badge_factor_moodle_bridge_unauth_moodle_text'];
			$auth_moodle_text = $bridge_options['badge_factor_moodle_bridge_auth_moodle_text'];
			//echo json_encode( array($unauth_moodle_text . ',' .  $auth_moodle_text) );
			print json_encode( $bridge_options );
			wp_die();
			//return array($unauth_moodle_text . ',' .  $auth_moodle_text);
			//error_log( array($unauth_moodle_text . ',' .  $auth_moodle_text));
	}
	/**
     * Moodle tells WP to take the badge at this URL.
     *
     * @since    1.0.0
     */
    public function get_assertion_backup() {

        /**
         * USE CASE
         * Recieves Moodle _POST
         * Validates Data
		 * 	api_key
         *  authenticate (user exists)
         *  user
         *  date/timestamp
         *  badge_id
		 * 	preuves	???
         *  
         * Generate Badge
         * Return Response to Moodle
         *  Status
         *  URL
         *
         */

/*	TEST	*/

			echo "ok"; //for CURL
			$fp = fopen('php://input', 'r'); //creates ressource
			$rawData = stream_get_contents($fp); //converts ressource to array
			$object = json_decode($rawData); //converts to array

//			error_log( 'all: ' . print_r($rawData, true)); 		
/*			$objectA = json_decode($rawData, true);
			$objectB = json_decode($rawData);
*/
/*			ob_start();
			var_dump($object);
			error_log(ob_get_clean());*/

/*			error_log( var_dump($object) );  
			error_log( 'WP[coursename]: ' . $object->record->coursename );			
			error_log( 'WP[coursemoduleid]: ' . $object->record->coursemoduleid );
			error_log( 'WP[userid]: ' . $object->record->userid );
			error_log( 'WP[user_email]: ' . $object->record->user_email );
			error_log( 'WP[timemodified]: ' . $object->record->timemodified );
			error_log( 'WP[idnumber]: ' . $object->record->idnumber );																	
*/

			setlocale(LC_TIME, "fr_FR");
			date_default_timezone_set('America/Montreal');
			
			/* refactor security	*/
//			error_log('wp_api_key: ' .  $object->api_key);
			$wp_shared_key = get_option('wp2m_shared_secret');
			$moodle_shared_key = $object->api_key;

			// Validates Shared encryption secret (Wordpress & Moodle)
			if ( $moodle_shared_key !== $wp_shared_key ){
				echo json_encode(array('handshake'=>false, 'message'=>__('Fail: Shared encryption secret')));
				wp_die();
			}
			
			$moodleuser = get_user_by('email', $object->record->user_email);
			$course_idnumber = $object->record->idnumber;
			
			/* Output Badge title + content	*/
			$mdl_post_title = 'Activité ' . $object->record->coursemoduleid . ' ' . $object->record->timemodified; 
			$mdl_post_content = $object->record->fullname . ', a complété l\'activité ' . $object->record->coursemoduleid . ' du cours ' . $object->record->coursename . ' le ' . strftime('%c');
			
			/* are we really using title + content here? */
			$title = $object->record->coursename;	//$mdl_post_title
			$content = $mdl_post_content;
			
			
			if (!($moodleuser instanceof WP_User) ) {
//				error_log( 'Moodle - fulano de tal : ' . $object->record->user_email . ' + ' . $object->record->coursemoduleid );
			}

				if (($moodleuser instanceof WP_User) ) {
//					error_log( 'Moodle - aqui toy : ' . $object->record->user_email . ' + ' . $object->record->coursemoduleid );
					
					$user_id = $moodleuser->ID;

					$mdl_post_title = 'Activité ' . $object->record->coursemoduleid . ' ' . $object->record->timemodified; 
					$mdl_post_content = $object->record->fullname . ', a complété l\'activité ' . $object->record->coursemoduleid . ' du cours ' . $object->record->coursename . ' le ' . strftime('%c');
					
					$moodleuser = get_user_by('email', $object->record->user_email);
					$course_idnumber = $object->record->idnumber;
					
					$title = $object->record->coursename;	//$mdl_post_title
					$content = $mdl_post_content;
					
				   /**
					* Retrieve a post_id given its (moodle)$course_idnumber.
					*
					* @param string $post_type ('badges')
					* @return string $post_id
					*/
					if ( isset($object->record->idnumber) ) {
						$moodle_idnumber = $object->record->idnumber;
							function get_moodle_idnumber( $moodle_idnumber ){
								wp_reset_postdata();
								$myargs = array (
									'post_type' => 'badges',
									'meta_value' => $moodle_idnumber
								);
								$myquery = new WP_Query($myargs);
								if($myquery->have_posts() ) :
									while($myquery->have_posts() ) : $myquery->the_post(); 
										return get_the_ID();
									endwhile;
								endif;
								wp_reset_postdata();
							}
							
						$achievement_id = get_moodle_idnumber( $moodle_idnumber );
					}
					
//					$achievement_id = get_moodle_idnumber( $moodle_idnumber );
					
					error_log( '$achievement_id: ' . $achievement_id . ', $user_id: ' . $user_id . ', $title: ' . $title);
					
					$GLOBALS['badgefactor']->create_submission( $achievement_id, $title, $content, $user_id);
					
					if (isset($mdl_post_title)){
/*						
						//{valid:true, email:'doucet.django@uqam.ca', first_name:'Django', last_name:'Doucet', badge_id:1000, preuves:'TBD'}
						// todo_badgefactor_function(json_params);
						// TEMP Create post object
						$my_post = array(
						  'post_title'    => $mdl_post_title,
						  'post_content'  => $mdl_post_content,
						  'post_status'   => 'publish',
						  'post_author'   => 1,
						  'post_category' => '?assertion?'
						);
						
						// Insert the (assertion) post into the database
						$post_id = wp_insert_post( $my_post );	
						$permalink = get_permalink( $post_id );*/
						//echo json_encode(array('badge_created'=>true, 'message'=>__('Votre badge a été créé!'), 'badge_url'=> urlencode($permalink)));	
						
					}
			
				}

			wp_die();
			
/*	A DELETER - TEST, ancienne methode d'authentification	*/
			
		if ($_POST) { //Recieves Moodle _POST 
			// Get shared secret from WP
//			$wp2m_shared_secret = get_option('wp2m_shared_secret')
			$bridge_options = get_option( 'badge_factor_moodle_bridge_settings' );
			$wp_shared_api_key = $bridge_options['badge_factor_moodle_bridge_shared_api_key'];

			// Get shared secret from Moodle
			$moodle_shared_api_key = $_REQUEST['shared_api_key'];
			
			// Validates API Keys
			if ( $moodle_shared_api_key !== $wp_shared_api_key ){
//			if ( $moodle_shared_api_key !== $wp2m_shared_secret ){	
				echo json_encode(array('handshake'=>false, 'message'=>__('Wrong API Key.')));
				wp_die();
			}

			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			
			$user = get_user_by('email', $username);
//			$user = get_user_by('login', $username);
			$creds = array($user, $username, $password);
	
			// Authenticate (user exists)
			$test_login = apply_filters('authenticate', $user, $username, $password);
			
			// Validates Userlogin Data
			if ( is_wp_error($test_login) ){
				echo json_encode(array('authenticate'=>false, 'message'=>__('Wrong username or password.')));
				
				//create user here with email and password
				$user_id = wp_create_user($username, $password);
				echo json_encode(array('user_created'=>true, 'message'=>__('Welcome to wordpress ' . $username)));
				wp_die();
			} else {
				// Authenticate Replace with api_key
				$test_signon = do_action('wp_signon', $creds);
				if ( is_wp_error($test_signon) ){
					echo json_encode(array('wp_signon'=>false, 'message'=>__('Error.')));	
				} else {
					
					//Ici on créera l'assertion
					/*if (isset($_POST['post_title'])){
						
						//{valid:true, email:'doucet.django@uqam.ca', first_name:'Django', last_name:'Doucet', badge_id:1000, preuves:'TBD'}
						// todo_badgefactor_function(json_params);
						// TEMP Create post object
						$my_post = array(
						  'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
						  'post_content'  => $_POST['post_content'],
						  'post_status'   => 'publish',
						  'post_author'   => 1,
						  'post_category' => '?assertion?'
						);
						
						// Insert the (assertion) post into the database
						$post_id = wp_insert_post( $my_post );	
						$permalink = get_permalink( $post_id );
						echo json_encode(array('badge_created'=>true, 'message'=>__('Votre badge a été créé!'), 'badge_url'=> urlencode($permalink)));	
						
					}*/
					
					//$moodle_url = $options['badge_factor_moodle_bridge_moodle_url'];
					//echo json_encode(array('moodle_url'=>$bridge_options['badge_factor_moodle_bridge_moodle_url'], 'wp_shared_api_key'=>$bridge_options['badge_factor_moodle_bridge_shared_api_key'], 'moodle_shared_api_key'=>$_REQUEST['shared_api_key']));
				}
				wp_die();
			}
		} 
    } 
	
	/**
     * Get list of Badges (Title, courseIDnumber)
     *
     * @since    1.0.0
     */
    public function get_badge_list_wp2moodle_backup() {

        /**
         * USE CASE
		 * TinyMCE Modal popup displays list of badges, select one and prepopulate shortcode for use with wp2Moodle. 
         * 		Return list of badges 
		 * 		Fields : Title, courseIDnumber
         */

		if ($_POST) { //Recieves Moodle _POST 
				$bridge_options = get_option( 'badge_factor_moodle_bridge_settings' );
				$wp_shared_api_key = $bridge_options['badge_factor_moodle_bridge_shared_api_key'];
				$moodle_shared_api_key = $_REQUEST['shared_api_key'];
				
				// Validates API Keys
				if ( $moodle_shared_api_key !== $wp_shared_api_key ){	
					echo json_encode(array('handshake'=>false, 'message'=>__('Wrong API Key.')));
					wp_die();
				}

				/**
				* Retrieve a list of posts from $post_type('badges') for TinyMCE insertion (wp2Moodle).
				*
				* @return mixed ($post_title Page title) + ('meta_value' => $course_idnumber)
				* group_591c4e2ccec4c
				* field_591c4e41b604b
				*/			
				$course_idnumber = $_REQUEST['course_idnumber']; //$course_idnumber
				$page_title = $_REQUEST['title'];//'La scénarisation pédagogique';//
				
				wp_reset_postdata();
				$myargs = array (
					'post_type' => 'badges',
				);
				$myquery = new WP_Query($myargs);
				if($myquery->have_posts() ) :
					while($myquery->have_posts() ) : $myquery->the_post();
						$meta = get_field_object( 'field_591c4e41b604b', get_the_ID() );
//						echo serialize($meta) . "\r\n";
//						echo $meta['label'] . ': ' . $meta['value'];
						echo get_the_title() . ' -> ' ; 
						echo $meta['value'] . "\r\n";					
					endwhile;
				endif;
				wp_reset_postdata();
				wp_die();
				
			   /**
				* Retrieve a post_id given its (moodle)$course_idnumber.
				*
				* @param string $post_type ('badges')
//				* @param string $post_title Page title
				* @return string $post_id
				*/
/*				if ( $_REQUEST['fodar_id'] ) {
//				if ( isset($object->record->idnumber) ) {
				
				$moodle_idnumber = $_REQUEST['fodar_id'];
//				$moodle_idnumber = $object->record->idnumber;
				
					function get_moodle_idnumber( $moodle_idnumber ){
						wp_reset_postdata();
						$myargs = array (
							'post_type' => 'badges',
		//					'title' => $page_title,
							'meta_value' => $moodle_idnumber
						);
						$myquery = new WP_Query($myargs);
						if($myquery->have_posts() ) :
							while($myquery->have_posts() ) : $myquery->the_post();
		//						echo the_title() . ' -> ' ; 
								return the_ID();// . "\r\n";	
							endwhile;
						endif;
						wp_reset_postdata();
					}
					$moodle_idnumber = $object->record->idnumber;
	//				$fodar_id = $_REQUEST['fodar_id']; //$course_idnumber
	//				$page_title = $_REQUEST['title'];//'La scénarisation pédagogique';//
					
					$achievement_id = get_moodle_idnumber( $moodle_idnumber );
					echo $achievement_id;
					wp_die();
				}*/
				
/*				if ( $_REQUEST['fodar_id'] ) {
					$fodar_id = $_REQUEST['fodar_id']; //$course_idnumber
	//				$page_title = $_REQUEST['title'];//'La scénarisation pédagogique';//
					
					wp_reset_postdata();
					$myargs = array (
						'post_type' => 'badges',
	//					'title' => $page_title,
						'meta_value' => $fodar_id
					);
					$myquery = new WP_Query($myargs);
					if($myquery->have_posts() ) :
						while($myquery->have_posts() ) : $myquery->the_post();
	//						echo the_title() . ' -> ' ; 
							echo the_ID();// . "\r\n";	
						endwhile;
					endif;
					wp_reset_postdata();
					wp_die();
				}*/
		}
		
    }
	
	/**
     * Authentication hook to sync Moodle users into WP.
     *
     * @since    1.0.0
     */
    public function authenticate($user, $username, $password) {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Badge_Factor_Moodle_Bridge_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Badge_Factor_Moodle_Bridge_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        /**
         * USE CASE
         * For user login to wp
         * For Moodle assertion login to wp
         */

		// If WP USER
//		if (!($user instanceof WP_User) ) {


        /**
         * if (isset(user & password))
         * wp_sign_on
         *  if success : yay
         * else : try
         * call moodle authentication CURL
         * define moodle URL & payload
         * if moodle user valid
         *  does wp user exist
         *  if not : wp_create_user
         *  if so : wp_set_password
         *
		 * test_user_1 : #IqZL6D@q84r4zKgJ4#9)8fR
         */

		/*$bridge_options = get_option( 'badge_factor_moodle_bridge_settings' );
		$moodle_url = $bridge_options['badge_factor_moodle_bridge_moodle_url'];
		$shared_api_key = $bridge_options['badge_factor_moodle_bridge_shared_api_key'];
		
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			// TODO Moodle API_URL 
			// ADD badge_id field
			// Define Proof : resutats, list Q/A, Nom
			$moodle_url .= "/...api_url...?shared_api_key={$shared_api_key}&username={$username}&password={$password}";
			curl_setopt($curl, CURLOPT_URL, $moodle_url);
			
			$json_result = curl_exec($curl);
			// must contain : first_name, last_name
			$results = json_decode($json_result);
			
			
			if ($results->valid) {
				//{valid:true, first_name:'Django', last_name:'Doucet'}
				//{valid:false}
				
				$creds = array('remember'=>true, 'user_login'=>$username, 'password'=>$password);
				
				$user_id = username_exists($username);
				
				if (false != $user_id) {
					wp_set_password($password, $user_id);
				} else {
					$user_id = wp_create_user($username, $password);
				}
				if (isset($results->first_name) && isset($results->last_name)){
					$user_id = wp_update_user(array(
						'ID' => $user_id, 
						'first_name' => $results->first_name, 
						'last_name' => $results->last_name, 
						'display_name' => $results->first_name . ' ' . $results->last_name ));
				}
				$user = wp_set_current_user($user_id);
			}
			
		}*/
//		return $user;
		
    }
}
