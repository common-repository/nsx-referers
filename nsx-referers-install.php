<?php

function nsx_referers_new_install()
{
	global $wpdb;
	$status = '';
	
	$sql = "
CREATE TABLE ".REFTABLE." (
`id` int(11) NOT NULL auto_increment,
`url` varchar(255) NOT NULL default '',
`date` date default NULL,
`host` varchar(50) default NULL,
`search` varchar(255) default NULL,
`hits` int(11) NOT NULL default 1,
PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;";

	if($wpdb->query($sql) === false)
	{
		$status = 'ERROR: Unable to create '.REFTABLE.' table<br />';
	}

    
	add_option('nsx_rw_title','Referers');
	add_option('nsx_rw_count','8');
	add_option('nsx_rw_top_pages','16');
	add_option('nsx_rw_top_search','10');
	add_option('nsx_rw_min_queries','1');

	return $status;
}

function nsx_referers_remove_data()
{
	global $wpdb;

    $wpdb->query("DROP TABLE IF EXISTS ".REFTABLE);
	
	delete_option('nsx_rw_title');
    delete_option('nsx_rw_count');
	delete_option('nsx_rw_top_pages');
	delete_option('nsx_rw_top_search');
	delete_option('nsx_rw_min_queries');
}

?>