<?php
/**
 * @package MW_FB_Events_Widget
 * @version 1.0
 */
/*
Plugin Name: Wordpress Facebook Events Widget
Description: Widget to display timer for Facebook events in Wordpress
Author: Mateusz Wojtuła
Version: 1.0
Author URI: http://www.matw.pl
*/
require_once 'src/facebook.php';

/**
 * Widget for displaying Facebook events
 *
 * @link http://www.matw.pl
 * @author Mateusz Wojtuła
 * @version 1.0
 */
class MW_FB_Events_Widget extends WP_Widget {
	
	private $app_id = '373259876146261';
	private $app_secret = 'd1ae396d4cc960ea83464c8e282a002c';
	
	private $fb;
	private $errors;
	
	/**
	 * Constructor
	 *
	 * @return MW_FB_Events_Widget
	 */
	public function __construct() {
		parent::__construct( 'widget_mw_fb_events', __( 'Facebook Events'), array(
			'classname'   => 'widget_mw_fb_events',
			'description' => __( 'Use this widget to display timer for Facebook events on Your page.'),
		));
		$this->fb_init();
	}
	
	/**
	 * Initialize Facebook library
	 * 
	 * @return Facebook
	 */
	private function fb_init() {
		$config = array(
			'appId' => $this->app_id,
			'secret' => $this->app_secret,
			'fileUpload' => false,
			'allowSignedRequest' => false,
		);
		
		return $this->fb = new Facebook($config);
	}
	
	/**
	 * Retrieves events data
	 * 
	 * @param string $event_id
	 * @return multitype:NULL
	 */
	public function get_event_data($event_id = '') {
		$data = array();
		try {
			$data = $this->fb->api('/'.$event_id,'GET');
		} catch(FacebookApiException $e) {
			$data['error'] = array(
				'type' 		=> $e->getType(),
				'message'	=> $e->getMessage(),
			);
		}
		return $data;
	}
	
	/**
	 * Output the HTML for this widget.
	 *
	 * @access public
	 *
	 * @param array $args     An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance.
	 * @return void Echoes its output.
	 */
	public function widget( $args, $instance ) {
		
		$event = $this->get_event_data(!empty($instance['event_id'])?$instance['event_id']:0);

		if (isset($event['error'])) {
			echo $event['error']['message'];			
		} else {
			$title  = apply_filters( 'widget_title', isset($event['name'])?$event['name']:'', $instance, $this->id_base );
				
			$href = 'https://www.facebook.com/events/'.$event['id'];
			$href_graph = 'https://graph.facebook.com/'.$event['id'];
			
			$timer_count = 0;
			if ($instance['days']) $timer_count++;
			if ($instance['hours']) $timer_count++;
			if ($instance['minutes']) $timer_count++;
			if ($instance['seconds']) $timer_count++;
			if ($instance['hundredth_second']) $timer_count++;
?>
<style type="text/css">
	.mw_widget p {
		text-align: justify;
	}
	.mw_widget h3 span {
		cursor: pointer;
	}
	.mw_time_box {
		width: <?php echo $timer_count>0?100/$timer_count:0?>%;
		height: 60px;
		text-align: center;
		background: #fff;
		float: left;
	}
	.mw_time_box .mw_timer {
		font-size: 20px;
		width: 100%;
		font-weight: bold;
	}
	.mw_time_box .mw_caption {
		font-size: 11px;
		display: block;
	}
	.mw_clear {
		clear: both;
	}
	#mw_event_picture {
		text-align: center;
		padding: 0px 10px 18px 10px;
	}
</style>
		<aside id="<?php echo $args['widget_id']?>" class="widget mw_widget">
			<h1 class="widget-title">
				<a href="<?php echo $href?>" title="<?php echo $title;?>" target="_blank">
					<?php echo $title;?>
				</a>
			</h1>
			<?php if(!empty($instance['picture'])):?>
			<div id="mw_event_picture">
				<a href="<?php echo $href?>" title="<?php echo $title;?>" target="_blank">
					<img src="<?php echo $href_graph?>/picture?type=large">
				</a>
			</div>
			<?php endif;?>
			<?php if(!empty($instance['days'])):?>
			<div class="mw_time_box">
				<div id="mw_days" class="mw_timer">0</div>
				<div class="mw_caption"><?php _e('Days');?></div>
			</div>
			<?php endif;?>
			<?php if(!empty($instance['hours'])):?>
			<div class="mw_time_box">
				<div id="mw_hours" class="mw_timer">0</div>
				<div class="mw_caption"><?php _e('Hours');?></div>
			</div>
			<?php endif;?>
			<?php if(!empty($instance['minutes'])):?>
			<div class="mw_time_box">
				<div id="mw_minutes" class="mw_timer">0</div>
				<div class="mw_caption"><?php _e('Minutes');?></div>
			</div>
			<?php endif;?>
			<?php if(!empty($instance['seconds'])):?>
			<div class="mw_time_box">
				<div id="mw_seconds" class="mw_timer">0</div>
				<div class="mw_caption"><?php _e('Seconds');?></div>
			</div>
			<?php endif;?>
			<?php if(!empty($instance['hundredth_second'])):?>
			<div class="mw_time_box">
				<div id="mw_hundredth_second" class="mw_timer">0</div>
				<div class="mw_caption"><?php _e('Hundredths of seconds');?></div>
			</div>
			<?php endif;?>
			<div class="mw_clear"></div>			
		</aside>
<script type="text/javascript">
<!--
function mw_timer()
{
	var start = Date.parse('<?php echo $event['start_time']?>');
	var c = new Date();
	var current = c.getTime();

	var hundredth_second = 10;
	var seconds = 1000;
	var minutes = 60000;
	var hours = 3600000;
	var days = 86400000;
		
	var miliseconds = start - current;
	
	var left = miliseconds;
	console.log(miliseconds);
	if (left > 0) {
		<?php if(!empty($instance['days'])):?>
		var timer_d = Math.floor(left/days);
		document.getElementById("mw_days").innerHTML = timer_d;
		left -= timer_d*days;
		<?php endif;?>
		<?php if(!empty($instance['hours'])):?>
		var timer_h = Math.floor(left/hours);
		document.getElementById("mw_hours").innerHTML = timer_h;
		left -= timer_h*hours;
		<?php endif;?>
		<?php if(!empty($instance['minutes'])):?>
		var timer_m = Math.floor(left/minutes);
		document.getElementById("mw_minutes").innerHTML = timer_m;
		left -= timer_m*minutes;
		<?php endif;?>
		<?php if(!empty($instance['seconds'])):?>
		var timer_s = Math.floor(left/seconds);
		document.getElementById("mw_seconds").innerHTML = timer_s;
		left -= timer_s*seconds;
		<?php endif;?>
		<?php if(!empty($instance['hundredth_second'])):?>
		var timer_ms = Math.floor(left/hundredth_second);
		document.getElementById("mw_hundredth_second").innerHTML = timer_ms;
		left -= timer_ms*hundredth_second;
		<?php endif;?>
	} else {
		document.getElementById("mw_days").innerHTML = 0;
		document.getElementById("mw_hours").innerHTML = 0;
		document.getElementById("mw_minutes").innerHTML = 0;
		document.getElementById("mw_seconds").innerHTML = 0;
		document.getElementById("mw_hundredth_second").innerHTML = 0;
	}
}
mw_timer();
<?php 
	if (!empty($instance['hundredth_second'])) {
		echo 'var mw_interval = setInterval(function(){mw_timer()},10);';
	} elseif(!empty($instance['seconds'])) {
		echo 'var mw_interval = setInterval(function(){mw_timer()},1000);';
	} else {
		echo 'var mw_interval = setInterval(function(){mw_timer()},60000);';
	}
?>
//-->
</script>

<?php
		}
	}

	/**
	 * Validation and saving data
	 *
	 * @param array $new_instance New widget instance.
	 * @param array $instance     Original widget instance.
	 * @return array Updated widget instance.
	 */
	function update( $new_instance, $instance ) {
		$event_id  = strip_tags( $new_instance['event_id'] );
		$event_data = $this->get_event_data($event_id);
		
		if (empty($event_data['error'])) {
			$instance['event_id'] = $event_id;
		} else {
			switch ($event_data['error']['message']) {
				case 'Unsupported get request.':
					$error_msg = 'Invalid event ID "'.$new_instance['event_id'].'"';
					break;
				
				default:
					$error_msg = $event_data['error']['message'];
					break;
			}
			$this->errors = new WP_Error('event_id', __($error_msg));
		}
	
		$instance['picture']  = empty( $new_instance['picture'] ) ? 0 : 1;
		$instance['days']  = empty( $new_instance['days'] ) ? 0 : 1;
		$instance['hours']  = empty( $new_instance['hours'] ) ? 0 : 1;
		$instance['minutes']  = empty( $new_instance['minutes'] ) ? 0 : 1;
		$instance['seconds']  = empty( $new_instance['seconds'] ) ? 0 : 1;
		$instance['hundredth_second']  = empty( $new_instance['hundredth_second'] ) ? 0 : 1;

		return $instance;
	}

	/**
	 * Display the form for this widget on the Widgets page of the Admin area.
	 *
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		$title  = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$event_id  = empty( $instance['event_id'] ) ? '' : esc_attr( $instance['event_id'] );
		$picture = empty( $instance['picture'] ) ? 0 : 1;
		$days = empty( $instance['days'] ) ? 0 : 1;
		$hours = empty( $instance['hours'] ) ? 0 : 1;
		$minutes = empty( $instance['minutes'] ) ? 0 : 1;
		$seconds = empty( $instance['seconds'] ) ? 0 : 1;
		$hundredth_second = empty( $instance['hundredth_second'] ) ? 0 : 1;
		
		if (!empty($this->errors)) {
			echo '<div id="message" class="error">'.$this->errors->get_error_message().'</div>';
		}
		echo $this->field('event_id','Event ID: ',$event_id);
		echo $this->field('picture','Picture',$picture,'checkbox');
		echo $this->field('days','Days',$days,'checkbox');
		echo $this->field('hours','Hours',$hours,'checkbox');
		echo $this->field('minutes','Minutes',$minutes,'checkbox');
		echo $this->field('seconds','Seconds',$seconds,'checkbox');
		echo $this->field('hundredth_second','Hundredths of seconds',$hundredth_second,'checkbox');
	}
	
	/**
	 * Create form fields
	 * 
	 * @param string $name
	 * @param string $title
	 * @param string $value
	 * @param string $type
	 * @return string
	 */
	private function field($name = '', $title = '', $value = '', $type = 'text') {

		switch ($type) {
			case 'checkbox':
				$str = '<p>
	 				<input id="'.esc_attr($this->get_field_id($name)).'" class="widefat" name="'.esc_attr($this->get_field_name($name)).'" type="checkbox" value="1" '.($value==1?'checked=checked':'').'>
					<label for="'.esc_attr($this->get_field_id($name)).'">'.translate($title).' </label>
				</p>';
				break;
			default:
				$str = '<p>
		 			<label for="'.esc_attr($this->get_field_id($name)).'">'.translate($title).' </label>
					<input id="'.esc_attr($this->get_field_id($name)).'" class="widefat" name="'.esc_attr($this->get_field_name($name)).'" type="'.$type.'" value="'.esc_attr($value).'">
				</p>';
				break;
		}
		
		return $str;
	}
	
	/**
	 * Register widget
	 */
	public function register() {
		return register_widget( 'MW_FB_Events_Widget' );
	}
}

add_action( 'widgets_init', array( 'MW_FB_Events_Widget', 'register' ) );