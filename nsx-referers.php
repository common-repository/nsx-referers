<?php
/*
Plugin Name: NSx Referers
Plugin URI: http://newsoftportal.com/nsx-referers-en.html
Description: Display the most frequent visits from search engines such as Google, Msn, Yahoo, and others to your blog
Version: 1.3
Author: NewSoft
Author URI: http://newsoftportal.com
*/

function init_nsx_textdomain() {
    if (function_exists('load_plugin_textdomain')) {
        load_plugin_textdomain('nsx-referers', 'wp-content/plugins/nsx-referers/');
    }
}

add_action('init', 'init_nsx_textdomain');

global $wpdb;

define('REFWORK', true);

define('REFBUILD', 1);
define('REFVERSION', '1.3');

define('REFTABLE', $wpdb->prefix.'referers');
define('REFBASEPATH', trailingslashit(get_option('siteurl')).'wp-content/plugins/nsx-referers/');

include_once('nsx-referers-install.php');
include_once('nsx-referers-stat.php');
include_once('nsx-referers-widget.php');

register_activation_hook( __FILE__ , 'nsx_referers_new_install' );
register_deactivation_hook( __FILE__, 'nsx_referers_remove_data' );
add_action('get_header', 'nsx_referers_stat');


add_action('admin_menu',  'nsx_referers_add_menu' );
//add_action('admin_menu',  'nsx_referers_queries' );

function nsx_referers_add_menu(){
    if ( function_exists('add_options_page') )
    {
/*
	add_options_page('NSx Referers Queries',
            'NSx Referers -> Queries', 'manage_options', 'nsx_referers_queries_page', // basename(__FILE__),
            'nsx_referers_queries_page' );
			
        add_options_page('NSx Referers Options',
            'NSx Referers', 'manage_options',  'nsx_referers_options_page', //basename(__FILE__),
            'nsx_referers_form' );
			*/
    }
	
	add_menu_page('Page title', __('NSx Referers', 'nsx-referers'), 8, 'nsx_referers_settings_page', 'nsx_referers_form');
	add_submenu_page('nsx_referers_settings_page', __('NSx Referers Settings', 'nsx-referers'), 
	__('Settings', 'nsx-referers'), 8, 'nsx_referers_settings_page', 'nsx_referers_form');
	add_submenu_page('nsx_referers_settings_page', __('NSx Referers Search Phrases', 'nsx-referers'), 
	__('Search Phrases', 'nsx-referers'), 8, 'nsx_referers_queries_page', 'nsx_referers_queries_page');

}

function nsx_referers_queries_page() {
global $wpdb;
	if(isset($_POST['ids'])) {
       if ( function_exists('current_user_can') &&
            !current_user_can('manage_options'))
                die ( _e('Hacker?', 'nsx-referers') );

        if (function_exists ('check_admin_referer') ) {
            check_admin_referer('nsx_referers_form');
        }
		foreach($_POST['ids'] as $qid){
			if (intval($qid) > 0) $ids[] = intval($qid);
		}
		if (count($ids) > 0) {
			$wpdb->query("DELETE FROM {$wpdb->prefix}referers WHERE id in (".implode(', ', $ids).")");
			
		}
	}
?>
    <div class='wrap'>
        <h2><?php _e('NSx Referers Search Phrases', 'nsx-referers'); ?></h2>
<?php
	
	$q = $wpdb->get_results("SELECT * FROM  {$wpdb->prefix}referers ORDER BY hits DESC");
	if ($q) {
	?>
	<form name="nsx-referers" method="post"
            action="<?php echo $_SERVER['PHP_SELF']; ?>?page=nsx_referers_queries_page&amp;updated=true">
<?php
		if (function_exists ('wp_nonce_field') )
			wp_nonce_field('nsx_referers_form');
?>

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th style="" class="manage-column column-cb check-column" id="cb" scope="col">
			<label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select all', 'nsx-referers'); ?></label>
			<input type="checkbox" id="cb-select-all-1"></th>
			<th style="" class="manage-column column-title sortable desc" id="title" scope="col">
		   <!-- <a href="http://wp.webstart.in.ua/wp-admin/edit.php?orderby=page&amp;order=asc"><span>--><?php _e('Page', 'nsx-referers'); ?><!--</span>
			<span class="sorting-indicator"></span></a>--></th>
		<th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Search Phrase', 'nsx-referers'); ?></th>
		<th style="" class="manage-column column-categories" id="categories" scope="col"><?php _e('Visits', 'nsx-referers'); ?></th>
		<!-- <th style="" class="manage-column column-tags" id="tags" scope="col">Action</th> -->
		</tr>
	</thead>
	<!--
	<tfoot>
	<tr>
		<th style="" class="manage-column column-cb check-column" scope="col"><label for="cb-select-all-2" class="screen-reader-text">Выделить все</label><input type="checkbox" id="cb-select-all-2"></th><th style="" class="manage-column column-title sortable desc" scope="col"><a href="http://wp.webstart.in.ua/wp-admin/edit.php?orderby=title&amp;order=asc"><span>Заголовок</span><span class="sorting-indicator"></span></a></th><th style="" class="manage-column column-author" scope="col">Автор</th><th style="" class="manage-column column-categories" scope="col">Рубрики</th><th style="" class="manage-column column-tags" scope="col">Метки</th><th style="" class="manage-column column-comments num sortable desc" scope="col"><a href="http://wp.webstart.in.ua/wp-admin/edit.php?orderby=comment_count&amp;order=asc"><span><span class="vers"><div class="comment-grey-bubble" title="Комментарии"></div></span></span><span class="sorting-indicator"></span></a></th><th style="" class="manage-column column-date sortable asc" scope="col"><a href="http://wp.webstart.in.ua/wp-admin/edit.php?orderby=date&amp;order=desc"><span>Дата</span><span class="sorting-indicator"></span></a></th>	</tr>
	</tfoot> -->
	<tbody id="the-list">
	<?php
		foreach ($q as $qrow) {
		?>
 <tr valign="top" class="post-1 type-post status-publish format-standard hentry category-- alternate iedit author-self" id="post-1"> 
				<th class="check-column" scope="row">
								<!--<label for="cb-select-1" class="screen-reader-text">Row</label>-->
				<input type="checkbox" value="<?php echo $qrow->id; ?>" name="ids[]" id="cb-select-1">
							</th>
		<td class="post-title page-title column-title">
			<?= $qrow->url ?>
		</td>
		<td class="post-title page-title column-title">
			<?= $qrow->search ?>
		</td>
		<td class="post-title page-title column-title">
			<?= $qrow->hits ?>
		</td>
		</tr>

		<?php	
		}
		?>
		</tbody>
</table>
	<input type=submit value="<?php _e('Delete selected items', 'nsx-referers'); ?>">
	</form>
<?php		
	} ?>
	</div>
	<?php
}

function nsx_referers_form() {
    $nsx_rw_top_pages = get_option('nsx_rw_top_pages');
    $nsx_rw_top_search = get_option('nsx_rw_top_search');
    $nsx_rw_min_queries = get_option('nsx_rw_min_queries');

    if ( isset($_POST['submit']) ) {  
       if ( function_exists('current_user_can') &&
            !current_user_can('manage_options') )
                die ( _e('Hacker?', 'nsx-referers') );

        if (function_exists ('check_admin_referer') ) {
            check_admin_referer('nsx_referers_form');
        }

        $nsx_rw_top_pages = $_POST['nsx_rw_top_pages'];
        $nsx_rw_top_search = $_POST['nsx_rw_top_search'];
        $nsx_rw_min_queries = $_POST['nsx_rw_min_queries'];

        update_option('nsx_rw_top_pages', $nsx_rw_top_pages);
        update_option('nsx_rw_top_search', $nsx_rw_top_search);
        update_option('nsx_rw_min_queries', $nsx_rw_min_queries);
    }
    ?>
    <div class='wrap'>
        <h2><?php _e('NSx Referers Settings', 'nsx-referers'); ?></h2>

        <form name="nsx-referers" method="post"
            action="<?php echo $_SERVER['PHP_SELF']; ?>?page=nsx_referers_settings_page&amp;updated=true">

            <!-- Имя ljusers_form используется в check_admin_referer -->
            <?php
                if (function_exists ('wp_nonce_field') )
                {
                    wp_nonce_field('nsx_referers_form');
                }
            ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Top pages limit', 'nsx-referers'); ?>:</th>

                    <td>
                        <input type="text" name="nsx_rw_top_pages"
                            size="80" value="<?php echo $nsx_rw_top_pages; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Top search phrases limit', 'nsx-referers'); ?>:</th>

                    <td>
                        <input type="text" name="nsx_rw_top_search"
                            size="80" value="<?php echo $nsx_rw_top_search; ?>" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Minimum of Search Phrases', 'nsx-referers'); ?>:</th>

                    <td>
                        <input type="text" name="nsx_rw_min_queries"
                            size="80" value="<?php echo $nsx_rw_min_queries; ?>" />
                    </td>
                </tr>
            </table>

            <input type="hidden" name="action" value="update" />

            <input type="hidden" name="page_options"
                value="nsx_rw_top_pages,nsx_rw_top_search,nsx_rw_min_queries" />

            <p class="submit">
            <input type="submit" name="submit" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
    <?
}


?>