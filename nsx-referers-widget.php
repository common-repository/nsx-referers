<?php

function get_referers()
{
global $wpdb;
	$out = '';
	srand((float) microtime() * 10000000);

	$siteurl = get_option('siteurl');
	$asiteurl = parse_url($siteurl);
	$siteurl = $asiteurl['scheme']."://".$asiteurl['host'];
  
	$maxc = (int) get_option('nsx_rw_count');
	if ($maxc < 1) $maxc = 10;
	$top_pages  = max((int) get_option('nsx_rw_top_pages'),1);
	$top_search = max((int) get_option('nsx_rw_top_search'),1);
	$min_queries = max((int) get_option('nsx_rw_min_queries'), 1);

	if ($top_pages > 0) $limit = "LIMIT 0, $top_pages"; else $limit = ''; 
	if ($top_search > 0) $limits = "LIMIT 0, $top_search"; else $limits = ''; 
	if ($min_queries > 1) $where = "AND hits >= {$min_queries}"; else $where = '';

	$a_top_pages = $wpdb->get_results( "
               SELECT url, sum(hits) as sum_hits
               FROM ".REFTABLE."
               WHERE not (search like '%??%')
               GROUP BY url
               ORDER BY sum_hits desc
               $limit", ARRAY_A );

	if (count($a_top_pages) > 0){
		$top_page_rows = $wpdb->num_rows;
		$a_top_pages_rand = array_rand($a_top_pages, min($top_page_rows, $maxc));

		if (is_array($a_top_pages_rand))
		foreach ($a_top_pages_rand as $page_ind){
			$a_top_page =  $a_top_pages[$page_ind];
			$search_words = $wpdb->get_results( "
				SELECT search, sum(hits) as sum_hits
				FROM ".REFTABLE."
				WHERE url = '{$a_top_page['url']}' and not (search like '%??%') {$where}
				GROUP BY search 
				ORDER BY sum_hits desc
				$limits", ARRAY_A );
			if (count($search_words) > 0){
				if ($top_search == 0)  $ind = mt_rand(0, $wpdb->num_rows - 1);
								else  $ind = mt_rand(0, min($top_search, $wpdb->num_rows) - 1);
				$out .= "<li><a href='{$siteurl}{$a_top_page['url']}' >{$search_words[$ind]['search']}</a></li>";
			}
		}
	} 

	return $out;
}

function nsx_referers_show($title=''){
	if (empty($title)) $title = "<h3>".get_option('nsx_rw_title')."</h3>"; 
	echo "$title\n"; 
	echo "<ul>\n";
	echo get_referers();
	echo "</ul>\n";
}

function nsx_referers_widget($args) {
	extract($args);

	echo $before_widget;
	echo $before_title;
	echo get_option('nsx_rw_title'); 
	echo $after_title;
	echo "<ul>";
	echo get_referers();
	echo "</ul>";
	echo $after_widget;
}

function nsx_referers_widget_control() {

    if (!empty($_REQUEST['nsx_rw_title'])) {
        update_option('nsx_rw_title', $_REQUEST['nsx_rw_title']);
    }
    if (!empty($_REQUEST['nsx_rw_count'])) {
        update_option('nsx_rw_count', (int) $_REQUEST['nsx_rw_count']);
    }

echo "<p><label for=\"nxrw_title\">".__('Title', 'nsx-referers'); 
echo ": <input class=\"widefat\" id=\"nsx_rw_title\" name=\"nsx_rw_title\" value=\"".get_option('nsx_rw_title')."\" type=\"text\"></label>";

echo "</p>";
echo "<p><label for=\"nxrw_count\">".__('Number of links', 'nsx-referers'); 
echo ": <input style=\"width: 30px; text-align: center;\" id=\"nsx_rw_count\" name=\"nsx_rw_count\" value=\"".get_option('nsx_rw_count')."\" type=\"text\"></label>";

echo "</p>";
}

function register_nsx_referers_widget() {
    register_sidebar_widget('NSx Referers Widget', 'nsx_referers_widget');
    register_widget_control('NSx Referers Widget', 'nsx_referers_widget_control');
}

add_action('init', 'register_nsx_referers_widget');
?>