<?php

	unregister_setting( 'tooltip_options','only_external', 'intval' );
	unregister_setting( 'tooltip_options','tooltip_w', 'intval' );
	unregister_setting( 'tooltip_options','tooltip_h', 'tooltip_set_h' );
	unregister_setting( 'tooltip_options','position_x', 'intval' );
	unregister_setting( 'tooltip_options','position_y', 'intval' );
	unregister_setting( 'tooltip_options','style', 'wp_filter_nohtml_kses' );
	
?>