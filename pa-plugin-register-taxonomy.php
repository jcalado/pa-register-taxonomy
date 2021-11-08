<?php
/**
* Plugin Name: Register Taxonomy - Portal Adventista
* Plugin URI: https://www.adventistas.org
* Description: Plugin que regista todas as taxonomias necessário para o funcionamento do portal.
* Version: 1.0
* Author: IASD
* Author URI: https://www.adventistas.org"
**/


class PARegisterTax
{
	public function __construct() {

		
		load_theme_textdomain('iasd', plugin_dir_path(__FILE__) . 'language/');
		add_action('after_setup_theme', array($this, 'installRoutines'));

	}

	function installRoutines() {

		$termos = array(
			'xtt-pa-colecoes' 		=> [__('Collections', 'iasd'), 				__('Collection', 'iasd')],
			'xtt-pa-editorias' 		=> [__('Editorials', 'iasd'), 				__('Editorial', 'iasd')],
			'xtt-pa-departamentos' 	=> [__('Ministries', 'iasd'), 				__('Ministry', 'iasd')],
			'xtt-pa-projetos' 		=> [__('Projects', 'iasd'), 				__('Project', 'iasd')],
			'xtt-pa-regiao' 		=> [__('Region', 'iasd'), 					__('Regions', 'iasd')],
			'xtt-pa-sedes' 			=> [__('Regional Headquarters', 'iasd'), 	__('Regional Headquarters', 'iasd')],
			'xtt-pa-owner' 			=> [__('Owner Headquarter' , 'iasd'), 		__('Owner Headquarter', 'iasd')]
		);

		foreach ($termos as $key => $value) {
			
			$labels = array(
				'name'              => $value[0],
				'singular_name'     => $value[1],
				'search_items'      => __('Search', 'iasd'),
				'all_items'         => __('All itens', 'iasd'),
				'parent_item'       => $value[1] . ', father',
				'parent_item_colon' => $value[1] . ', father',
				'edit_item'         => __('Edit', 'iasd'),
				'update_item'       => __('Update', 'iasd'),
				'add_new_item'      => __('Add new', 'iasd'),
				'new_item_name'     => __('New', 'iasd'),
				'menu_name'         => $value[1],
			);
		
			$args   = array(
				'hierarchical'      	=> true, // make it hierarchical (like categories)
				'labels'            	=> $labels,
				'show_ui'           	=> true,
				'show_admin_column' 	=> true,
				'query_var'         	=> true,
				'rewrite'           	=> array( 'slug' => strtolower($value[1])),
				'public'              	=> true,
				'show_in_rest'      	=> true, // add support for Gutenberg editor
				'capabilities' 			=> array(
					'edit_terms' 	  	=> false,
					'delete_terms'    	=> false,
				)
			);
			
			register_taxonomy($key, ['post'], $args);
		}
	}
}

$PARegisterTax = new PARegisterTax();
