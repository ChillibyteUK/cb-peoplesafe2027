<?php
/**
 * WP-CLI: fill the Navigation post type with the navigation of the 2024 theme.
 *
 *   wp cb-nav seed --images-from=/path/to/cb-peoplesafe2024/img
 *
 * Creates one section per item, in the default region. Links are paths, so they
 * work in every region; switch a link to a page in the editor once that page
 * exists. Images are imported into the media library from the folder given
 * (names below are relative to it); an image that is not found is left out,
 * and can be chosen in the editor. A section that already exists (same slug) is
 * skipped unless --replace is given, which overwrites its settings.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The navigation as it was in the 2024 theme's header.php.
 *
 * @return array[]
 */
function cb_peoplesafe2027_nav_seed_data() {
	$item  = static function ( $label, $url, $desc = '', $img = '' ) {
		return compact( 'label', 'url', 'desc', 'img' );
	};
	$more  = static function ( $text, $url ) {
		return array(
			'text' => $text,
			'url'  => $url,
		);
	};
	$all   = $more( 'View all products', '/products/' );
	$p     = '2024/products/';
	$sects = array(
		'nav-products'  => array(
			'title'  => 'Products',
			'layout' => 'panels',
			'url'    => '/products/',
			'panels' => array(
				array(
					'title'   => 'Safety Devices',
					'heading' => 'Safety Devices',
					'intro'   => 'We provide a range of safety products including devices and apps to ensure the most appropriate solution for your organisation.',
					'display' => 'products',
					'more'    => $all,
					'items'   => array(
						$item( 'MySOS', '/products/mysos/', 'Compact personal safety alarm', $p . 'MySOS.png' ),
						$item( 'MySOS ID Badge 4G', '/products/mysos-id-badge-4g/', 'Discreet personal safety ID badge', $p . 'MySOS-ID-Badge.png' ),
						$item( 'SPOT Gen4', '/products/spot-gen4/', 'Pocket-sized satellite SOS alarm', $p . 'SpotGen4.png' ),
						$item( 'SPOT X', '/products/spot-x/', 'Robust compact satellite SOS alarm', $p . 'SpotX.png' ),
						$item( 'TWIG Ex (ATEX)', '/products/twig-ex-atex/', 'ATEX/IECEX approved SOS alarm', $p . 'TwigEx.png' ),
						$item( 'Standard Mobile', '/products/standard-mobile/', 'Entry level solution for low-risk workers', $p . 'StandardMobile.png' ),
					),
				),
				array(
					'title'   => 'Safety Apps',
					'heading' => 'Safety Apps',
					'intro'   => 'We provide a range of safety apps to ensure the most appropriate solution for your organisation.',
					'display' => 'products',
					'more'    => $all,
					'items'   => array(
						$item( 'Lone Worker App', '/products/lone-worker-app/', 'Accredited app for lone workers', 'Lone-Worker-App.png' ),
						$item( 'Personal Safety App', '/products/sos-app/', 'Safety app for all employees', 'Personal-Safety-App.png' ),
						$item( 'Travelsafe', '/products/travelsafe/', 'Get safely from A to B', 'Travelsafe.png' ),
						$item( 'Apple Watch App', '/products/apple-watch/', 'Response direct from your Apple Watch', 'Apple-Watch-Personal-App.png' ),
						$item( 'International Traveller', '/products/international-traveller/', 'Keep your international travellers safe', 'International-Traveller-1.png' ),
						$item( 'Roamsafe', '/products/roamsafe/', 'Get a signal anywhere to call for help', 'roamsafe-menu.png' ),
						$item( 'OK Alone', 'https://www.okaloneworker.com/', 'Monitor and protect your people', 'okalone.png' ),
					),
				),
				array(
					'title'   => 'API',
					'heading' => 'API',
					'intro'   => 'Streamline operations and reduce admin time',
					'display' => 'products',
					'items'   => array(
						$item( 'Service API', '/products/services-api/', 'For workforce management systems', 'Nexus-API-hero.png' ),
						$item( 'User Management API', '/products/user-management-api/', 'For HR systems', 'Nexus-API-hero.png' ),
					),
				),
				array(
					'title'   => 'Emergency Notification System',
					'heading' => 'Emergency Notification System',
					'intro'   => 'Simple and dedicated emergency communication platform.',
					'display' => 'products',
					'more'    => $all,
					'items'   => array(
						$item( 'Emergency Alert System', '/products/peoplesafe-alert/', 'Send multichannel notifications to keep your people safe and connected', 'Peoplesafe-Alert.png' ),
						$item( 'Team Broadcast', '/products/team-broadcast/', 'In-app mass notifications to keep your team safe', 'team-broadcast-sm.png' ),
					),
				),
				array(
					'title'   => 'Safety Cameras',
					'heading' => 'Safety Cameras',
					'intro'   => 'Overt protection for frontline workers.',
					'display' => 'products',
					'more'    => $all,
					'items'   => array(
						$item( 'Body Worn Camera', '/products/body-worn-camera/', 'Deterrent for verbal and physical abuse', 'Body-Worn-Camera-menu-image.png' ),
						$item( 'SureCam', '/products/surecam/', 'Providing drivers inside and outside the vehicle', 'Surecam.png' ),
					),
				),
			),
		),
		'nav-solutions' => array(
			'title'  => 'Solutions',
			'layout' => 'panels',
			'url'    => '/solutions/',
			'width'  => 850,
			'panels' => array(
				array(
					'title'   => 'By Role',
					'display' => 'cards',
					'items'   => array(
						$item( 'Lone Workers', '/lone-workers/', 'Keep your lone workers safe 24/7, wherever they are, with the Peoplesafe Lone Worker Alarms.', '2024/lone-workers.png' ),
						$item( 'Home & Hybrid Workers', '/home-workers/', 'Our market-leading technology empowers home and hybrid workers to stay safe.', '2024/home-hybrid.png' ),
						$item( 'All Employees', '/personal-safety/', 'Protect every employee with our dedicated solutions.', '2024/all-employees.png' ),
						$item( 'International Workers', '/international-workers/', 'Ensure the safety of your staff even when they are not in the UK.', '2024/international-workers.png' ),
					),
				),
				array(
					'title'   => 'By Sector',
					'heading' => 'By Sector',
					'intro'   => 'Whatever the risks your staff face at work, our fully accredited service will support them in an emergency.',
					'display' => 'cols',
					'items'   => array(
						$item( 'NHS', '/sectors/nhs/' ),
						$item( 'Housing', '/sectors/housing/' ),
						$item( 'Retail', '/sectors/retail/' ),
						$item( 'Transport', '/sectors/transport/' ),
						$item( 'Education', '/sectors/education/' ),
						$item( 'Estate Agents', '/sectors/estate-agents/' ),
						$item( 'Manufacturing', '/sectors/manufacturing/' ),
						$item( 'Professional Services & Government', '/sectors/professional-services-government/' ),
						$item( 'Councils', '/sectors/councils/' ),
						$item( 'Utilities', '/sectors/utilities-telecoms/' ),
						$item( 'Haulage & Distribution', '/sectors/haulage-distribution/' ),
						$item( 'Charity', '/sectors/charity/' ),
						$item( 'Hospitality', '/sectors/hospitality/' ),
						$item( 'Construction', '/sectors/construction/' ),
						$item( 'Police', '/sectors/police/' ),
						$item( 'All Sectors', '/sectors/' ),
					),
				),
			),
		),
		'nav-pricing'   => array(
			'title'  => 'Pricing',
			'layout' => 'link',
			'url'    => '/pricing/',
		),
		'nav-about'     => array(
			'title'  => 'About',
			'layout' => 'list',
			'url'    => '/about-menu/',
			'items'  => array(
				$item( 'About Us', '/about/' ),
				$item( 'Our Team', '/about/senior-management/' ),
				$item( 'Our History', '/about/our-history/' ),
				$item( 'Nexus', '/nexus/' ),
				$item( 'ARC', '/alarm-receiving-centre/' ),
				$item( 'Accreditations', '/accreditations/' ),
				$item( 'Contact us', '/contact-us/' ),
			),
		),
		'nav-knowledge' => array(
			'title'  => 'Knowledge Hub',
			'layout' => 'tiles',
			'url'    => '/resources/',
			'more'   => $more( 'View all Knowledge Hub', '/resources/' ),
			'items'  => array(
				$item( 'Blogs', '/blogs/', 'Tips & content for safety at work', '2024/icons/blog.png' ),
				$item( 'News', '/news/', 'The very latest developments', '2024/icons/news.png' ),
				$item( 'Guides', '/guides/', 'Help & guidance for employee safety', '2024/icons/guides.png' ),
				$item( 'Whitepapers', '/whitepapers/', 'Thought leadership research', '2024/icons/whitepapers.png' ),
				$item( 'Video', '/video/', 'Watch our latest content', '2024/icons/videos.png' ),
				$item( 'Brochures', '/brochures/', 'Peoplesafe product offerings', '2024/icons/brochures.png' ),
				$item( 'Real Life Stories', '/stories/', 'How we helped each person', '2024/icons/stories.png' ),
				$item( 'Case Studies', '/case-studies/', 'Hear direct from our customers', '2024/icons/case-studies.png' ),
			),
		),
		'nav-support'   => array(
			'title'    => 'Support',
			'layout'   => 'list',
			'url'      => '/support/',
			'items'    => array(
				$item( 'Helpcentre', 'https://nexussupport.peoplesafe.co.uk/hc/en-gb', '', '' ),
				$item( 'Service Status', '/service-status/' ),
				$item( 'Contact Us', '/contact-us/' ),
				$item( 'Portal Login', '/portal-login/' ),
			),
		),
		'nav-login'     => array(
			'title'    => 'Login',
			'layout'   => 'link',
			'position' => 'extras',
			'icon'     => 'lock',
			'url'      => '/portal-login/',
		),
		'nav-demo'      => array(
			'title'    => 'Book a Demo',
			'layout'   => 'button',
			'url'      => '/contact-us/',
		),
	);

	return $sects;
}

/**
 * Turn a seed row into the item shape the block stores.
 *
 * @param array  $row    Seed row.
 * @param string $dir    Folder to look for images in, or an empty string.
 * @return array
 */
function cb_peoplesafe2027_nav_seed_item( $row, $dir ) {
	$item = array(
		'label'    => $row['label'],
		'desc'     => $row['desc'],
		'image'    => 0,
		'imageUrl' => '',
		'link'     => array(
			'post'      => 0,
			'postTitle' => '',
			'url'       => $row['url'],
			'target'    => 0 === strpos( $row['url'], 'http' ),
		),
	);

	$id = $row['img'] ? cb_peoplesafe2027_nav_seed_image( $row['img'], $dir ) : 0;

	if ( $id ) {
		$item['image']    = $id;
		$item['imageUrl'] = wp_get_attachment_url( $id );
	}

	return $item;
}

/**
 * Where a menu image can be downloaded from on the live 2024 site.
 *
 * @param string $img Image name from the seed data.
 * @return string URL, or an empty string when it is not known.
 */
function cb_peoplesafe2027_nav_seed_remote_url( $img ) {
	$uploads = array(
		'Lone-Worker-App.png'                => '2024/07/Lone-Worker-App.png',
		'Personal-Safety-App.png'            => '2024/07/Personal-Safety-App.png',
		'Travelsafe.png'                     => '2024/07/Travelsafe.png',
		'Apple-Watch-Personal-App.png'       => '2024/07/Apple-Watch-Personal-App.png',
		'International-Traveller-1.png'      => '2024/07/International-Traveller-1.png',
		'roamsafe-menu.png'                  => '2024/12/roamsafe-menu.png',
		'okalone.png'                        => '2025/04/okalone.png',
		'Nexus-API-hero.png'                 => '2025/01/Nexus-API-hero.png',
		'Peoplesafe-Alert.png'               => '2024/07/Peoplesafe-Alert.png',
		'team-broadcast-sm.png'              => '2025/04/team-broadcast-sm.png',
		'Body-Worn-Camera-menu-image.png'    => '2025/10/Body-Worn-Camera-menu-image.png',
		'Surecam.png'                        => '2024/07/Surecam.png',
	);

	if ( 0 === strpos( $img, '2024/' ) ) {
		return 'https://peoplesafe.co.uk/wp-content/themes/cb-peoplesafe2024/img/' . $img;
	}

	return isset( $uploads[ $img ] ) ? 'https://peoplesafe.co.uk/wp-content/uploads/' . $uploads[ $img ] : '';
}

/**
 * Add a menu image to the media library, once: a second run reuses the first
 * copy. The file comes from a local folder when found there, otherwise it is
 * downloaded from the live 2024 site.
 *
 * @param string $img Image name from the seed data, for example 2024/products/MySOS.png.
 * @param string $dir Comma-separated local folders to look in (may be empty).
 * @return int Attachment ID, or 0.
 */
function cb_peoplesafe2027_nav_seed_image( $img, $dir = '' ) {
	$existing = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_cb_nav_seed',
			'meta_value'  => basename( $img ),
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = '';

	foreach ( array_filter( array_map( 'trim', explode( ',', (string) $dir ) ) ) as $folder ) {
		if ( file_exists( trailingslashit( $folder ) . $img ) ) {
			$tmp = wp_tempnam( basename( $img ) );
			copy( trailingslashit( $folder ) . $img, $tmp );
			break;
		}
	}

	if ( '' === $tmp ) {
		$url = cb_peoplesafe2027_nav_seed_remote_url( $img );
		$tmp = $url ? download_url( $url ) : '';

		if ( is_wp_error( $tmp ) || '' === $tmp ) {
			cb_peoplesafe2027_seed_log( 'No image for ' . $img );
			return 0;
		}
	}

	$id = media_handle_sideload(
		array(
			'name'     => basename( $img ),
			'tmp_name' => $tmp,
		),
		0
	);

	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}

	update_post_meta( $id, '_cb_nav_seed', basename( $img ) );

	return (int) $id;
}

/**
 * The block markup for a seed section.
 *
 * @param array  $section Seed section.
 * @param string $dir     Image folder.
 * @return string
 */
function cb_peoplesafe2027_nav_seed_content( $section, $dir ) {
	$link = static function ( $url ) {
		return array(
			'post'      => 0,
			'postTitle' => '',
			'url'       => $url,
			'target'    => false,
		);
	};
	$more = static function ( $row ) use ( $link ) {
		return $row ? array_merge( $link( $row['url'] ), array( 'text' => $row['text'] ) ) : array();
	};
	$rows = static function ( $list ) use ( $dir ) {
		return array_map(
			static function ( $row ) use ( $dir ) {
				return cb_peoplesafe2027_nav_seed_item( $row, $dir );
			},
			(array) $list
		);
	};

	$attrs = array(
		'layout'   => $section['layout'],
		'position' => isset( $section['position'] ) ? $section['position'] : 'main',
		'icon'     => isset( $section['icon'] ) ? $section['icon'] : '',
		'width'    => isset( $section['width'] ) ? $section['width'] : 0,
		'link'     => $link( $section['url'] ),
		'items'    => $rows( isset( $section['items'] ) ? $section['items'] : array() ),
		'more'     => $more( isset( $section['more'] ) ? $section['more'] : array() ),
		'panels'   => array(),
	);

	foreach ( isset( $section['panels'] ) ? $section['panels'] : array() as $panel ) {
		$attrs['panels'][] = array(
			'title'   => $panel['title'],
			'heading' => isset( $panel['heading'] ) ? $panel['heading'] : '',
			'intro'   => isset( $panel['intro'] ) ? $panel['intro'] : '',
			'display' => $panel['display'],
			'items'   => $rows( $panel['items'] ),
			'more'    => $more( isset( $panel['more'] ) ? $panel['more'] : array() ),
		);
	}

	return serialize_block(
		array(
			'blockName'    => 'cb-peoplesafe2027/nav-section',
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	);
}

/**
 * Say what the setup did: to the terminal under WP-CLI, otherwise into a list
 * the Tools page shows.
 *
 * @param string $message Message.
 * @return void
 */
function cb_peoplesafe2027_seed_log( $message ) {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::log( $message );
		return;
	}

	$GLOBALS['cb_peoplesafe2027_seed_log'][] = $message;
}

/**
 * Create the navigation sections of the 2024 site.
 *
 * @param string $dir     Comma-separated folders to find menu images in (may be empty).
 * @param bool   $replace Overwrite sections that already exist.
 * @return void
 */
function cb_peoplesafe2027_nav_seed_run( $dir = '', $replace = false ) {
	$order = 1;

	foreach ( cb_peoplesafe2027_nav_seed_data() as $slug => $section ) {
		$existing = get_posts(
			array(
				'name'        => $slug,
				'post_type'   => 'nav_section',
				'post_status' => 'any',
				'numberposts' => 1,
			)
		);
		$post     = array(
			'post_type'    => 'nav_section',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $section['title'],
			'menu_order'   => $order++,
			'post_content' => wp_slash( cb_peoplesafe2027_nav_seed_content( $section, $dir ) ),
		);

		if ( $existing && ! $replace ) {
			cb_peoplesafe2027_seed_log( 'Skipped ' . $slug . ' (exists)' );
			continue;
		}

		if ( $existing ) {
			$post['ID'] = $existing[0]->ID;
		}

		$id = wp_insert_post( $post, true );

		if ( is_wp_error( $id ) ) {
			cb_peoplesafe2027_seed_log( $slug . ': ' . $id->get_error_message() );
			continue;
		}

		if ( ! $existing && function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $id, pll_default_language() );
		}

		cb_peoplesafe2027_seed_log( ( $existing ? 'Updated ' : 'Created ' ) . $slug );
	}

}

/**
 * Create the footer menus of the 2024 site, assign them to their locations and
 * fill in the social links. A menu that already exists is left alone.
 *
 * @return void
 */
function cb_peoplesafe2027_footer_seed_run() {
	$home = function_exists( 'cb_peoplesafe2027_region_url' ) && function_exists( 'pll_default_language' )
		? cb_peoplesafe2027_region_url( pll_default_language() )
		: trailingslashit( home_url() );

	$menus = array(
		'footer_products'  => array(
			'name'  => 'Footer: Products',
			'items' => array(
				'MySOS'             => 'products/mysos/',
				'Twig Ex'           => 'products/twig-ex-atex/',
				'Standard Mobile'   => 'products/standard-mobile/',
				'SPOT X'            => 'products/spot-x/',
				'SPOT Gen4'         => 'products/spot-gen4/',
				'Personal Safety App' => 'products/sos-app/',
				'Smart Button'      => 'products/smart-button/',
				'MySOS ID Badge 4G' => 'products/mysos-id-badge-4g/',
				'Lone Worker App'   => 'products/lone-worker-app/',
				'Body Worn Camera'  => 'products/body-worn-camera/',
			),
		),
		'footer_about'     => array(
			'name'  => 'Footer: About us',
			'items' => array(
				'About Peoplesafe' => 'about/',
				'Contact Us'       => 'contact-us/',
				'Our Team'         => 'about/senior-management/',
				'Our History'      => 'about/our-history/',
				'Accreditations'   => 'accreditations/',
				'Careers'          => 'careers/',
			),
		),
		'footer_resources' => array(
			'name'  => 'Footer: Resources',
			'items' => array(
				'Blogs'                => 'blogs/',
				'News'                 => 'news/',
				'Real Life Stories'    => 'stories/',
				'Case Studies'         => 'case-studies/',
				'Guides'               => 'guides/',
				'Brochures'            => 'brochures/',
				'Whitepapers'          => 'whitepapers/',
				'Legislation'          => 'legislation/',
				'Partnerships'         => 'partnerships/',
				'Service Status'       => 'service-status/',
				'Privacy Policy'       => 'privacy-policy/',
				'Whistleblowing Policy' => 'peoplesafe-whistleblowing-policy/',
			),
		),
	);

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	foreach ( $menus as $location => $menu ) {
		$existing = wp_get_nav_menu_object( $menu['name'] );

		if ( $existing ) {
			cb_peoplesafe2027_seed_log( 'Skipped ' . $menu['name'] . ' (exists)' );
			$locations[ $location ] = $existing->term_id;
			continue;
		}

		$id = wp_create_nav_menu( $menu['name'] );

		if ( is_wp_error( $id ) ) {
			cb_peoplesafe2027_seed_log( $menu['name'] . ': ' . $id->get_error_message() );
			continue;
		}

		foreach ( $menu['items'] as $label => $path ) {
			wp_update_nav_menu_item(
				$id,
				0,
				array(
					'menu-item-title'  => $label,
					'menu-item-url'    => $home . $path,
					'menu-item-type'   => 'custom',
					'menu-item-status' => 'publish',
				)
			);
		}

		$locations[ $location ] = $id;
		cb_peoplesafe2027_seed_log( 'Created ' . $menu['name'] );
	}

	set_theme_mod( 'nav_menu_locations', $locations );

	$settings = get_option( CB_PEOPLESAFE2027_SETTINGS_OPTION, array() );
	$settings = is_array( $settings ) ? $settings : array();
	$social   = array(
		'linkedin_url' => 'https://www.linkedin.com/company/peoplesafe-ltd/',
		'youtube_url'  => 'https://www.youtube.com/channel/UCRT8D1BWOHpR9Gt8jZukZSQ',
		'x_url'        => 'https://twitter.com/peoplesafe',
	);

	foreach ( $social as $key => $url ) {
		if ( empty( $settings[ $key ] ) ) {
			$settings[ $key ] = $url;
		}
	}

	update_option( CB_PEOPLESAFE2027_SETTINGS_OPTION, $settings );

}

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	/**
	 * The wp cb-nav command.
	 */
	class CB_Peoplesafe_2027_Nav_CLI {

		/**
		 * Create the navigation sections of the 2024 site.
		 *
		 * ## OPTIONS
		 *
		 * [--images-from=<dirs>]
		 * : Folders to find the menu images in, comma separated. Images not found are fetched from the live site.
		 *
		 * [--replace]
		 * : Overwrite sections that already exist.
		 *
		 * @param array $args       Positional arguments (none).
		 * @param array $assoc_args Flags.
		 * @return void
		 */
		public function seed( $args, $assoc_args ) {
			cb_peoplesafe2027_nav_seed_run( isset( $assoc_args['images-from'] ) ? $assoc_args['images-from'] : '', ! empty( $assoc_args['replace'] ) );
			WP_CLI::success( 'Navigation seeded.' );
		}
	}

	/**
	 * The wp cb-footer command.
	 */
	class CB_Peoplesafe_2027_Footer_CLI {

		/**
		 * Create the footer menus of the 2024 site.
		 *
		 * @param array $args       Positional arguments (none).
		 * @param array $assoc_args Flags (none).
		 * @return void
		 */
		public function seed( $args, $assoc_args ) {
			cb_peoplesafe2027_footer_seed_run();
			WP_CLI::success( 'Footer seeded.' );
		}
	}

	WP_CLI::add_command( 'cb-nav', 'CB_Peoplesafe_2027_Nav_CLI' );
	WP_CLI::add_command( 'cb-footer', 'CB_Peoplesafe_2027_Footer_CLI' );
}

/**
 * Tools, Navigation setup: the same setup as the WP-CLI commands, for an
 * environment with no command line (Kinsta staging and live).
 *
 * @return void
 */
function cb_peoplesafe2027_seed_admin_menu() {
	add_management_page( 'Navigation setup', 'Navigation setup', 'manage_options', 'cb-nav-setup', 'cb_peoplesafe2027_seed_admin_page' );
}
add_action( 'admin_menu', 'cb_peoplesafe2027_seed_admin_menu' );

/**
 * The Navigation setup page.
 *
 * @return void
 */
function cb_peoplesafe2027_seed_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$GLOBALS['cb_peoplesafe2027_seed_log'] = array();

	if ( isset( $_POST['cb_nav_setup'] ) && check_admin_referer( 'cb_nav_setup' ) ) {
		set_time_limit( 300 );
		cb_peoplesafe2027_nav_seed_run( '', false );
		cb_peoplesafe2027_footer_seed_run();
	}
	?>
	<div class="wrap">
		<h1>Navigation setup</h1>
		<p>Creates the header navigation sections (Navigation) and the three footer menus, assigns the footer menus to their locations and fills in the social links, all from the 2024 site. Menu images are downloaded from the live site. Anything that already exists is left alone, so running it twice is safe.</p>
		<?php if ( ! empty( $GLOBALS['cb_peoplesafe2027_seed_log'] ) ) { ?>
			<div class="notice notice-success"><p><?php echo implode( '<br>', array_map( 'esc_html', $GLOBALS['cb_peoplesafe2027_seed_log'] ) ); ?></p></div>
		<?php } ?>
		<form method="post">
			<?php wp_nonce_field( 'cb_nav_setup' ); ?>
			<p><button type="submit" name="cb_nav_setup" value="1" class="button button-primary">Create navigation and footer menus</button></p>
		</form>
	</div>
	<?php
}
