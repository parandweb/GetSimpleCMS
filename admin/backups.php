<?php
/**
 * All Backups
 *
 * Displays all available page backups. 	
 *
 * @package GetSimple
 * @subpackage Backups
 * @link http://get-simple.info/docs/restore-page-backup
 */
 
// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

exec_action('load-backups');


$path    = GSBACKUPSPATH .getRelPath(GSDATAPAGESPATH,GSDATAPATH); // backups/pages/
$counter = '0';
$table   = '';


// delete all backup files if the ?deleteall session parameter is set
if (isset($_GET['deleteall'])){
	check_for_csrf("deleteall");
	$filenames = getFiles($path);
	
	foreach ($filenames as $file) {
		if (file_exists($path . $file) ) {
			if (isFile($file, $path, 'bak')) {
				delete_file($path . $file);
			}
		}
	}
	
	$success = i18n_r('ER_FILE_DEL_SUC');
}


//display all page backups
$filenames      = getFiles($path);
$count          = "0";
$pagesArray_tmp = array();
$pagesSorted    = array(); 

if (count($filenames) != 0) 
{ 
	foreach ($filenames as $file) 
	{
		if (isFile($file, $path, 'bak')) 
		{
			$data   = getXML($path .$file);
			$status = $data->menuStatus;
			$pagesArray_tmp[$count]['title'] = html_entity_decode($data->title, ENT_QUOTES, 'UTF-8');
			$pagesArray_tmp[$count]['url']   = $data->url;
			$pagesArray_tmp[$count]['date']  = $data->pubDate;
			$count++;
		}
	}
	$pagesSorted = subval_sort($pagesArray_tmp,'title');
}

if (count($pagesSorted) != 0) 
{ 
	foreach ($pagesSorted as $page) 
	{					
		$counter++;
		$table .= '<tr id="tr-'.$page['url'] .'" >';
		
		if ($page['title'] == '' ) { $page['title'] = '[No Title] &nbsp;&raquo;&nbsp; <em>'. $page['url'] .'</em>'; }
		
		$table .= '<td class="pagetitle break"><a title="'.i18n_r('VIEWPAGE_TITLE').' '. var_out($page['title']) .'" href="backup-edit.php?p=view&amp;id='. $page['url'] .'">'. cl($page['title']) .'</a></td>';
		$table .= '<td style="width:80px;text-align:right;" ><span>'. output_date($page['date']) .'</span></td>';
		$table .= '<td class="delete" ><a class="delconfirm" title="'.i18n_r('DELETEPAGE_TITLE').' '. var_out($page['title']) .'?" href="backup-edit.php?p=delete&amp;id='. $page['url'] .'&amp;nonce='.get_nonce("delete", "backup-edit.php").'">&times;</a></td>';
		$table .= '</tr>';
	}
}	

$pagetitle = i18n_r('BAK_MANAGEMENT');
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main" >
			<h3 class="floated"><?php i18n('PAGE_BACKUPS');?></h3>
			
			<?php if ($counter > 0) { ?>
				<div class="edit-nav clearfix" >
					<a href="javascript:void(0)" id="filtertable" accesskey="<?php echo find_accesskey(i18n_r('FILTER'));?>" ><?php i18n('FILTER'); ?></a>
					<a href="backups.php?deleteall&amp;nonce=<?php echo get_nonce("deleteall"); ?>" title="<?php i18n('DELETE_ALL_BAK');?>" accesskey="<?php echo find_accesskey(i18n_r('ASK_DELETE_ALL'));?>" class="confirmation"  ><?php i18n('ASK_DELETE_ALL');?></a>
					<?php exec_action(get_filename_id().'-edit-nav'); ?>
				</div>
				<div id="filter-search">
					<form><input type="text" autocomplete="off" class="text" id="q" placeholder="<?php echo strip_tags(lowercase(i18n_r('FILTER'))); ?>..." /> &nbsp; <a href="<?php echo getDef('GSDEFAULTPAGE');?>" class="cancel"><?php i18n('CANCEL'); ?></a></form>
				</div>
				<?php exec_action(get_filename_id().'-body'); ?>				
				<table id="editpages" class="highlight paginate">
					<thead>
						<tr><th><?php i18n('PAGE_TITLE'); ?></th><th style="text-align:right;" ><?php i18n('DATE'); ?></th><th></th></tr>
					</thead>
					<tbody>
						<?php echo $table; ?>
					</tbody>						
				</table>
			<?php  } else { ?>
				<div class="clearfix" style="height:40px;"></div>
			<?php  }	?>
		
			<p class="clear"><em><b><span id="pg_counter"><?php echo $counter; ?></span></b> <?php echo i18n_r('PAGE_BACKUPS');?></em></p>
		</div>
	</div>
	
	<div id="sidebar" >
		<?php include('template/sidebar-backups.php'); ?>
	</div>

</div>
<?php get_template('footer'); ?>