<?php

class AD_Manager_Widget extends WP_Widget
{

	private $ad_table = 'tar_ads_manager_ads';

	public function __construct()
	{
		parent::__construct(
			'ad_manager_widget',
			__('Ad Manager', 'tar-manager'),
			['customize_selective_refresh' => true]
		);
	}

	public function form($instance)
	{

		$items = ADDB()->get_data_by_field($this->ad_table, '*', 'ORDER BY ID DESC', null, null);

		$options = [];
		$options[''] = __('-- select ad --', 'tar-manager');
		foreach ($items as $item) {
			if ($item->active === '1') {
				$options[$item->ID] = $item->group .' - '. $item->name  ;
			}
		}

		$sections = get_terms([
			'taxonomy' => 'ta_article_section',
		]);

		$show_options = [];
		$show_options['all'] = 'all';
		foreach($sections as $section) {
			$show_options[$section->term_id] = $section->name;
		}


		$defaults = [
			'select' => '',
			'show_in' => ''
		];

		extract(wp_parse_args((array) $instance, $defaults));

		echo '<p>';
		echo '<lable for="' . $this->get_field_name('select') . '">' . __('Select AD', 'tar-manager') . '</label>';
		echo '<select name="' . $this->get_field_name('select') . '" id="' . $this->get_field_id('select') . '" class="widefat">';
		foreach ($options as $key => $name) {
			echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" ' . selected($select, $key, false) . '>' . $name . '</option>';
		}
		echo '</select>';
		echo '<p>';
		echo '<p>';
		echo '<lable for="' . $this->get_field_name('show_in') . '">' . __('Mostrar en', 'tar-manager') . '</label>';
		echo '<select name="' . $this->get_field_name('show_in') . '" id="' . $this->get_field_name('show_in') . '" class="widefat">';
		foreach($show_options as $key => $name) {
			echo '<option value="' . esc_attr($key) . '" id="' . esc_attr($key) . '" '.selected( $show_in, $key, false ).'>'.$name.'</option>';
		}
		echo '</select>';
		echo '</p>';
	}
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['select']   = isset($new_instance['select']) ? wp_strip_all_tags($new_instance['select']) : '';
		$instance['show_in']  = isset($new_instance['show_in']) ? wp_strip_all_tags($new_instance['show_in']) : '';
		return $instance;
	}

	public function widget($args, $instance)
	{
		extract($args);
		$select   = isset($instance['select']) ? $instance['select'] : '';
		$show_in  = isset($instance['show_in']) ? $instance['show_in']:'';

		if($show_in !== 'all' && $show_in == $this->term_id()) {
			if(!is_user_logged_in() 
			|| $this->user_status() !== 'active' 
			|| !$this->user_role()) {
				$this->show_widget($select);
			} 
		}

		if($show_in === 'all' && $this->term_id() === null)  {
			if(!is_user_logged_in() || !$this->user_role() || $this->user_status() != 'active') { 
					$this->show_widget($select);
			} 
		}

		
	
	}

	public function show_widget($select)
	{
		echo $before_widget;
		echo '<div class="widget-text wp_widget_plugin_box">';
		if ($select) {
			ADF()->show_ad($select);
		}
		echo '</div>';
		echo $after_widget;
	}

	public function user_status()
	{
		if(is_user_logged_in()) {
			return get_user_meta(get_current_user_id(),'_user_status',true);
		}
	}

	public function user_role()
	{
		if(is_user_logged_in()) {
			if(in_array(get_option('subscription_digital_role'),get_userdata(get_current_user_id())->roles)){
				return true;
			}
		}

		return false;
	}

	public function term_id()
	{
		if(get_queried_object()->term_id) {
			return get_queried_object()->term_id;
		}
	}
}

function ad_manager__widget()
{
	register_widget('AD_Manager_Widget');
}
add_action('widgets_init', 'ad_manager__widget');
