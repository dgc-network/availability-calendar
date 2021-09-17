<?php
class OWAC_calendar_front {
	
    public function __construct()
    {
		global $wpdb;
		add_shortcode('availabilitycalendar', array($this, 'OWAC_calendar_front_shortcode'));
		$contactus_table = $wpdb->prefix."OWAC_event";
		$this->event_data = $wpdb->get_results("SELECT * FROM $contactus_table WHERE 1 AND `flag`='0'");
		$ec_category_table = $wpdb->prefix."OWAC_category";
		$this->category_data = $wpdb->get_results("SELECT * FROM $ec_category_table where 1 AND `flag`='0' ORDER BY `cat_ord_num` ASC");
    }
	
	private function OWAC_check_date($pv_r,$k,$j,$sat_sun,$m,$category_short,$old_date_fade_out_color){
		
		$return_value = "";
		global $wpdb;
		if(empty($this->event_data)){
			$this->event_data = array();
		}
		if(empty($this->category_data)){
			$this->category_data = array();
		}
		if(!empty($category_short)){
			foreach($this->event_data as $key => $val){
				$category_short_new = explode(",",$category_short);
				if(in_array($val->cat_id,$category_short_new)){
					$total_pages_sql[] = $val;
				}
			}
		}else{
			$total_pages_sql = $this->event_data;
		}
		
		$cat_id_list = array();
		foreach($this->category_data as $key => $val){
			$cat_id_list[] = $val->cat_id;
		}
		
		for($i=0;$i<count($total_pages_sql);$i++){
			$ec_category_sql = array();
			foreach($cat_id_list as $val){
				$cat_id = '';
				if($total_pages_sql[$i]->cat_id == $val){
					$cat_id = $val;
				}	
			}

			foreach($this->category_data as $key => $val){
				if($total_pages_sql[$i]->cat_id == $val->cat_id){
					$ec_category_sql = $val;
				}
			}
			
			$from_date = $total_pages_sql[$i]->from_date;
			$to_date = $total_pages_sql[$i]->to_date;
			
			$cat_color = $ec_category_sql->cat_color;
			$cat_name = $ec_category_sql->cat_name;
			
			$cat_color_style = "";
			if($j==$sat_sun['sat']){$sday = "holiday";}elseif($j==$sat_sun['sun']){$sday="holiday";}else{$sday="";}
			if(!empty($old_date_fade_out_color)){
				$cat_color_style = $old_date_fade_out_color;
			}else if(!empty($cat_color)){
				$cat_color_style = "style='background-color:".sanitize_hex_color($cat_color)."'";
			}else{
				$cat_color_style = "";
				$sday .= " disable";
			}
			
			if($from_date <= $pv_r && $to_date >= $pv_r) {
				$return_value = "<td class='".esc_attr($sday)."'><span class='owaccatdec' ".$cat_color_style.">$k</span></td>";
				$cat_color_new = $cat_color;
			}	
		}
		
		return $return_value;
	}
	
    public function OWAC_calendar_front_shortcode($atts = array())
    {	
		$atts = shortcode_atts(array('category' => ''), $atts);
		
		if(!empty($atts['category'])){
			$category_short = array();
			$category_atts_array = explode(",",$atts['category']);
			foreach($category_atts_array as $val){
				if(is_numeric($val)){
					$category_short[] = $val;
				}
			}
			$category_short = implode(",",$category_short);	
		}
		
		if(empty($this->event_data)){
			$this->event_data = array();
		}
		if(empty($this->category_data)){
			$this->category_data = array();
		}
		
	   /**
		* Get Event and Category value
		*/
		$total_pages_sql = $this->event_data;
		
		if(!empty($category_short)){
			foreach($this->category_data as $key => $val){
				$category_short_new = explode(",",$category_short);
				if(in_array($val->cat_id,$category_short_new)){
					$ec_category_sql[] = $val;
				}
			}
		}else{
			$ec_category_sql = $this->category_data;
		}
		
		$settings_options = get_option( 'OWAC_settings_option' );
		$display_calendar_month = preg_replace("/[^0-9\.]/", '', $settings_options['display_calendar_month']);
		$language_display = $settings_options['language_display'];
		$language = owac_language();
		foreach($language as $key => $va){
			if($language_display == $key){
				setlocale(LC_TIME, array(''.$va['code'].'.UTF-8',''.$va['code'].'@euro',''.$va['code'].'',$key));
			}
		}
		
		/**
		* Set year
		*/
		$year=date('Y'); 
		if(strlen($year)!= 4){
			$year=date('Y'); 
		}
		$start_year=date('Y');
		//$end_year=$start_year + 1;
		/**
		* Check Switch Case Set Display Calendar Year
		*/
		switch ($settings_options['display_calendar_month']) {
			
			case "1y":
				$end_year=$start_year + $display_calendar_month;
				$row = 12 * $display_calendar_month + 1;
				break;
				
			case "2y":
				$end_year=$start_year + $display_calendar_month;
				$row = 12 * $display_calendar_month + 1;
				break;
			
			case "3y":
				$end_year=$start_year + $display_calendar_month;
				$row = 12 * $display_calendar_month + 1;
				break;
				
			case "4y":
				$end_year=$start_year + $display_calendar_month;
				$row = 12 * $display_calendar_month + 1;
				break;

			case "5y":
				$end_year=$start_year + $display_calendar_month;
				$row = 12 * $display_calendar_month + 1;
				break;
			
			default:
				$end_year = $start_year + 1;
				$row=12;
				break;
		}
		
		/**
		* No Edit Below
		*/
		$row_no=0; 
		$total_month = "12";
		$data = "";
		$data .= "<div class='owac-calendar-container' style='background-color: #".sanitize_hex_color_no_hash($settings_options['background_color'])." !important'>";
		/**
		* Set Header And Category Display
		*/	
		if($settings_options['header_display'] == 'yes' || $settings_options['category_display'] == 'yes'){
			$data .= "<div class='header'>";
					if($settings_options['header_display'] == 'yes'){
						$data .= "<h1 class='title'>".intval($year)."</h1>
								<p>".nl2br($settings_options['header_add_text'])."</p>
						";
					}
					if($settings_options['category_display'] == 'yes'){	
						$data .= "<div class='event-calendar'>
									<ul>";
								if($category_short == ''){
									for($i=0;$i<count($ec_category_sql);$i++){
										$cat_color = $ec_category_sql[$i]->cat_color;
										$cat_name = $ec_category_sql[$i]->cat_name;
										$data .= "<li>";
											$data .= "<span class='cat_color' style='background-color:".sanitize_hex_color($cat_color)." !important'></span>";
											$data .= "<span class='event-name'>".esc_attr($cat_name)."</span>";
										$data .= "</li>";
									}	
								}else{
									for($i=0;$i<count($ec_category_sql);$i++){
										$cat_color = $ec_category_sql[$i]->cat_color;
										$cat_name = $ec_category_sql[$i]->cat_name;
										$data .= "<li>";
											$data .= "<span class='cat_color' style='background-color:".sanitize_hex_color($cat_color)." !important'></span>";
											$data .= "<span class='event-name'>".esc_attr($cat_name)."</span>";
										$data .= "</li>";
									}
								}
								
						$data .= "</ul>
								</div>";
					}
			$data .= "</div>";
		}
		/**
		* Add JavaScript
		*/
		if($settings_options['display_calendar_month'] == '1m'){
			$settings_options['desktop_column'] = 1;
		}elseif($settings_options['display_calendar_month'] == '2m'){
			$settings_options['desktop_column'] = 2;
		}

		$data .= "	
			<script type='text/javascript'>
				jQuery(document).on('ready', function() {
					jQuery('.owacregular').not('.owac-initialized').owacslider({
						dots: false,
						infinite: false,
						slidesToShow: ".intval($settings_options['desktop_column']).",
						slidesToScroll: ".intval($settings_options['slides_to_scroll']).",
						responsive: [{
							breakpoint: 800,
							settings: {
								slidesToShow: ".intval($settings_options['tablet_column']).",
								slidesToScroll: ".intval($settings_options['slides_to_scroll'])."
							}
						},{
							breakpoint: 580,
							settings: {
								slidesToShow: ".intval($settings_options['mobile_column']).",
								slidesToScroll: ".intval($settings_options['slides_to_scroll'])."
							}
						}]					
					})				  			  				 
					jQuery('.owac-slider').on('setPosition', function () {
						jbResizeSlider();
					});
				});
				function jbResizeSlider(){
					var owacSlider = jQuery('.owac-slider');
					owacSlider.find('.owac-slide').height('auto');
					var owacTrack = owacSlider.find('.owac-track');
					var owacTrackHeight = jQuery(owacTrack).height();
					owacSlider.find('.owac-slide').css('height', owacTrackHeight + 'px');
					owacSlider.find('table.main').css('background-color', '#".sanitize_hex_color_no_hash($settings_options['calendar_background_color'])."');
				}
				jQuery(window).on('resize', function(e) {
					jbResizeSlider(); 
				});
			</script>";	
		
		/**
		* Display Calendar
		*/
		$data .= "<div class='main regularslider owac'>"; 
		/**
		* Starting of for loop
		* Creating calendars for Set year and Month 
		* Creating calendars for each month by looping 12 times
		*/
		for($r=$start_year;$r<=$end_year;$r++){
			$year = $r;
			$month_cur = intval(date("m"));
			$month_cur_u = intval(date("m"));
		/**
		* Check If Condition set Current Month and End Month To Year Wise 
		*/
			if($r==$start_year || $start_year==$end_year){
				$month_cur =intval(date("m"));
				$endmonth_cur = 12;
			}elseif($r < $end_year){
				$month_cur = 1;
				$endmonth_cur = 12;
			}else{
				$month_cur = 1;
				$endmonth_cur = $month_cur_u;
			}
			
		/**
		* Check Switch Case Set Display Calendar Month
		*/	
			switch ($settings_options['display_calendar_month']) {
			
				case "1m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+1 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "2m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+2 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
				
				case "3m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+3 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "4m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+4 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
				
				case "5m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+5 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "6m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+6 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "7m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+7 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "8m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+8 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "9m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+9 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "10m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+10 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
				
				case "11m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+11 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
					
				case "12m":
					$endmonth_cur = $month_cur_u + $display_calendar_month - 1;
					$check_cur_y = date('d-m-Y', strtotime('+12 months'));
					$end_year = date('Y',strtotime($check_cur_y));
					break;
			}
			
			if($r==$start_year || $start_year==$end_year){
				if($endmonth_cur >= 12){
					$endmonth_cur = 12;
				}
			}elseif($r < $end_year){
				if($endmonth_cur > 12){
					$endmonth_cur = $endmonth_cur - 12;
				}
			}else{
				if($endmonth_cur >= 12){
					$endmonth_cur = $endmonth_cur - 12;
				}
			}
	
		/**
		* Starting of for loop
		* And Creating Month and Day
		*/
			for($m=$month_cur;$m<=$endmonth_cur;$m++){
				
				$month =date("m");  // Month 
				$month_cur =intval(date("m")); // Month set
				$dateObject = DateTime::createFromFormat('!m', $m);
				//$monthName = utf8_encode(strftime("%B", mktime(0,0,0,$m+1,0,0)));
				$mName=strftime("%B", mktime(0,0,0,$m+1,0,0));
				$encoding = mb_detect_encoding($mName, "auto" );
				$monthName =mb_convert_encoding($mName, 'UTF-8','Windows-1252');
		
				$month = $dateObject->format('m');
				$d= 2; // To Finds today's date
				$no_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);//calculate number of days in a month
				$j = date('w',mktime(0,0,0,$month,1,$year)); // This will calculate the week day of the first day of the month
		/**
		* if starting day of the week is Monday then add following two lines
		*/	
				$j=$j-1;  
				if($j<0){$j=6;}  // if it is Sunday //
		/**
		* Starting of top line showing year and month to select
		*/	
				if(($row_no % $row)== 0){
					$data .= "<div class='owacregular slider calendar'>";
				}			
		/**
		* Set Month Name and Year
		*/
				$data .= "<table class='main owac' style='background-color: #".sanitize_hex_color_no_hash($settings_options['calendar_background_color'])." !important;'><tr class='month_title'><td colspan=7 align=center><h4 style='background-color: #".sanitize_hex_color_no_hash($settings_options['month_background_color'])." !important;; color: #".sanitize_hex_color_no_hash($settings_options['month_title_font_color'])." !important;;'> ".esc_attr($monthName)." ".intval($year)."</h4></td></tr>";
		/**
		*  Showing name of the days of the week
		*/
				if(!empty($settings_options['display_start_day'])){
					$start_day = $settings_options['display_start_day'];
				}else{
					$start_day = 1;
				}
				$num_of_days = cal_days_in_month(CAL_GREGORIAN, $m, $year);
				for( $i=1; $i<= $num_of_days; $i++){
					$dates[]= str_pad($i,2,'0', STR_PAD_LEFT);
					$d = $year."-".$m."-".$i;
					$day = date('D', strtotime($d));
					$day_no = date('N', strtotime($d));
					$dName=strftime("%a", mktime(0,0,0,$m,$i,$year));
					$encoding = mb_detect_encoding($dName, "auto" );
					$dayName =mb_convert_encoding($dName, 'UTF-8','Windows-1252');
					$days[$day_no] = strtoupper($dayName);
				}
				ksort($days);
				$onearr=array();
				foreach ( $days as $key => $val ){
					$onearr[ $key-1 ] = $val;
				}
				$start_week = $start_day - 1;
				$first_day = getdate(mktime(0, 0, 0, $month, 1, $year));
				$sat_sun=array();
				$sat = $days[6];
				$sun = $days[7];
				$total_days = date('t', mktime(0, 0, 0, $month, 1, $year));
				$data .= "<tr class='day_title'>";
				for ($dy = $start_week; $dy < ($start_week + 7); $dy++) {
					$index = ($dy > 6 ? $dy - 7 : $dy);
					$data .= '<th><span>' . esc_attr($onearr[$index][0]) . '</span></th>';
				}
				$sat_sun_num = 1;
				for ($dy = $start_week; $dy < ($start_week + 7); $dy++) {
					$index = ($dy > 6 ? $dy - 7 : $dy);
					if($sat == $onearr[$index]){
						$sat_sun['sat'] = $sat_sun_num;
					}
					if($sun == $onearr[$index]){
						$sat_sun['sun'] = $sat_sun_num;
					}
					$sat_sun_num++;
				}
				$data .= "</tr><tr class='day_row'>";
		/**
		* Starting of the Days
		*/	
				$days_num = array();
				$offset = $first_day['wday'] - $start_week;
				if ($offset < 1) {$offset += 7;}
				for ($i = 1; $i < $offset; $i++){$days_num[$i] = "";}
				for ($j = $i; $j < $total_days + $i; $j++){$days_num[$j] = $j - $i + 1;}
				for ($i = 1; $i <= 7; $i++){
					if (count($days_num) % 7 == 0) {break;}
					$days_num[] = "";
				}
				$holiday = 1;
				foreach ($days_num as $key => $day) {
					if(!empty($day)){
						$pv="'$month'".","."'$day'".","."'$year'";
						$pv_r="$day"."-"."$month"."-"."$year";
						$pv_r=strtotime($pv_r);
						
						if($holiday==$sat_sun['sat']){$sday = "holiday disable";}elseif($holiday==$sat_sun['sun']){$sday="holiday disable";}else{$sday="disable";}
						$old_date_fade_out_color = "";
						if($settings_options['old_date_fade_out'] == 'yes'){
							$old_date_fade_out = $settings_options['old_date_fade_out'];
							$current_Date = date('d-m-Y');
							$current_Date=strtotime($current_Date);
							if(!empty($settings_options['old_date_fade_out_color'])){
								$old_date_fade_out_color = sanitize_hex_color($settings_options['old_date_fade_out_color']);
							}
							if ($current_Date > $pv_r){
								$old_date_fade_out_color = 'style="background-color:#'.sanitize_hex_color_no_hash($old_date_fade_out_color).'"';
							}else{
								$old_date_fade_out_color = "";
							}
						}
						$set_event = $this->OWAC_check_date($pv_r,$day,$holiday,$sat_sun,$m,$category_short,$old_date_fade_out_color);
						
						if(!empty($set_event)){
							$data .= $set_event;
						}else{
							$data .= "<td class='".esc_attr($sday)."'><span ".$old_date_fade_out_color.">$day</span></td>";
						}

						if ($key % 7 == 0 && $key != count($days_num)) {
							$data .= '</tr><tr class="day_row">';
						}
					}else{
						$data .= "<td>&nbsp;</td>";
					}
					if($holiday==7){$holiday=0;}
					$holiday ++;
				}
				$data .= "</tr></table></td>";
				$row_no=$row_no+1;
			} // end of for loop for 12 months
		}
			$data .= "</div>";
		$data .= "</div>";
		
		
		$data .= "</div>";
		return $data;
    }
}

$OWAC_calendar_front = new OWAC_calendar_front();