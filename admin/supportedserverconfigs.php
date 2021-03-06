<?php
/*
 * GamePanelX Pro
 * Complete Game and Voice server management tool
 * 
 * Copyright(C) 2009-2010 GamePanelX Pro.  All Rights Reserved. 
 * 
 * Email: support@gamepanelx.com
 * Website: http://www.gamepanelx.com
 * 
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.                                                         
 *                                                                      
 * You may not reverse  engineer, decompile, defeat  license  encryption
 * mechanisms, or  disassemble this software product or software product
 * license.  GamePanelX Pro may terminate this license if you don't
 * comply with any of the terms and conditions set forth in our end user
 * license agreement (EULA).  In such event,  licensee  agrees to return
 * licensor  or destroy  all copies of software  upon termination of the
 * license.                                                             
 *                                                                      
 * Please see the EULA file for the full End User License Agreement.    
*/

//
// Smarty
//
require '../libs/Smarty.class.php';
$smarty = new Smarty;
$smarty->compile_dir  = '../admin/templates_c/';

// Required Files
require('../include/auth.php');
require('../include/config.php');

// Page Title
$smarty->assign('pagetitle', 'Server Config Management');


########################################################################

// Check license variable
if($gpxseckey_T2V1lmkWLli04Z7q3FT != 'F9hJt6up1h80qk9REDD2xyA89TfI185gwtLXJsSMhc61fWv5T33548rLqtW5MWGjkgFl8ISzsoF8491IT2V1lmkWLli04Z7q3FTls169B8PmTx0lRZet777Pr40p7R01FkQFymp1Z629GG5dEW8nI3')
{
    die('Invalid license');
}

########################################################################

// ID from the URL
$url_id = $_GET['id'];

// Check malformed ID
if(empty($url_id) || !is_numeric($url_id))
{
    die('<center><b>Error:</b> Invalid id given!</center>');
}

// Assign server ID
$smarty->assign('serverid', $url_id);

########################################################################




// Infobox from the URL
$url_info = $_GET['info'];

// Allowed info
$allowed_info = array('updated','deleted');

if(!empty($url_info))
{
    if(in_array($url_info, $allowed_info))
    {
        // Update Server
        if($url_info == 'updated')
        {
            $info_msg = 'Server successfully updated!';
            $smarty->assign('infobox', $info_msg);
        }
        // Delete Server
        if($url_info == 'deleted')
        {
            $info_msg = 'Server config deleted!';
            $smarty->assign('infobox', $info_msg);
        }
    }
}



########################################################################

// Set user's language
require('../include/functions/language.php');
$lang = gpx_language_get();
$smarty->assign('lang', $lang);

########################################################################


if(!isset($_POST['update']))
{
    // Connect to the database
    $db = @mysql_connect($config['sql_host'],$config['sql_user'],$config['sql_pass']) or die('<center><b>Error:</b> <i>supportedserverconfigs.php</i>: Failed to connect to the database!</center>');
    @mysql_select_db($config['sql_db']) or die('<center><b>Error:</b> <i>supportedserverconfigs.php</i>: Failed to select the database!</center>');

    //
    // Get client info
    //
    $result = @mysql_query("SELECT id,name,dir,description FROM cfg_configs WHERE srvid = '$url_id' ORDER BY id DESC") or die('<center><b>Error:</b> <i>supportedserverconfigs.php:</i> Failed to list client accounts!</center>');

    // Smarty loop
    while ($line = mysql_fetch_assoc($result))
    {
        $value[] = $line;
    }

    // Smarty mysql loop
    $smarty->assign('cfg_configs', $value);

    ####################################################################
    
    // Load GameQ game list INI File
    $ini_file     = '../include/query/games.ini';
    $arr_games    = parse_ini_file($ini_file,true);
    // $total_games  = count($arr_games);

    // Create smarty array
    $smarty_array = array();
    
    // Begin array counter
    $counter = 0;
     
    foreach($arr_games as $single_game=>$game_key)
    {
        // Specifics
        $game_query_name  = mysql_real_escape_string($single_game);
        $game_long_name   = mysql_real_escape_string($game_key['name']);

        // Add to smarty array
        $smarty_array[$counter]['long_name']  = $game_long_name;
        $smarty_array[$counter]['query_name'] = $game_query_name;
        
        // Add to the counter
        $counter++;
    }
    
    // Smarty mysql loop
    $smarty->assign('query_engines', $smarty_array);
    
    ####################################################################    
    
    
    // Display HTML Page
    $smarty->display($config['Template'] . '/supportedserverconfigs.tpl'); 
}



########################################################################


elseif(isset($_POST['update']))
{
    // Connect to the database
    $db = @mysql_connect($config['sql_host'],$config['sql_user'],$config['sql_pass']) or die('<center><b>Error:</b> <i>supportedserverconfigs.php</i>: Failed to connect to the database!</center>');
    @mysql_select_db($config['sql_db']) or die('<center><b>Error:</b> <i>supportedserverconfigs.php</i>: Failed to select the database!</center>');


    // Basic Setup
    $post_available       = mysql_real_escape_string($_POST['available']);
    $post_type            = mysql_real_escape_string($_POST['type']);
    $post_based_on        = mysql_real_escape_string($_POST['based_on']);
    $post_is_steam        = mysql_real_escape_string($_POST['is_steam']);
    $post_query_engine    = mysql_real_escape_string($_POST['query_engine']);
        
    // Naming
    $post_long_name       = mysql_real_escape_string($_POST['long_name']);
    $post_short_name      = mysql_real_escape_string($_POST['short_name']);
    $post_mod_name        = mysql_real_escape_string($_POST['mod_name']);
    $post_steam_name      = mysql_real_escape_string($_POST['steam_name']);
    $post_nickname        = mysql_real_escape_string($_POST['nickname']);
    $post_description     = mysql_real_escape_string($_POST['description']);
    
    // Specifics
    $post_executable      = mysql_real_escape_string($_POST['executable']);
    $post_max_slots       = mysql_real_escape_string($_POST['max_slots']);
    $post_map             = mysql_real_escape_string($_POST['map']);
    $post_style           = mysql_real_escape_string($_POST['style']);
    $post_log_file        = mysql_real_escape_string($_POST['log_file']);
    $post_setup_cmd       = mysql_real_escape_string($_POST['setup_cmd']);
    $post_working_dir     = mysql_real_escape_string($_POST['working_dir']);
    $post_cmd_line        = mysql_real_escape_string($_POST['cmd_line']);
    $post_config_file     = mysql_real_escape_string($_POST['config_file']);
    $post_pid_file        = mysql_real_escape_string($_POST['pid_file']);
    
    // Ports
    $post_port            = mysql_real_escape_string($_POST['port']);
    $post_reserved_ports  = mysql_real_escape_string($_POST['reserved_ports']);
    $post_tcp_ports       = mysql_real_escape_string($_POST['tcp_ports']);
    $post_udp_ports       = mysql_real_escape_string($_POST['udp_ports']);
    
    // Config Values
    $post_cfg_ip          = mysql_real_escape_string($_POST['cfg_ip']);
    $post_cfg_port        = mysql_real_escape_string($_POST['cfg_port']);
    $post_cfg_max_slots   = mysql_real_escape_string($_POST['cfg_max_slots']);
    $post_cfg_map         = mysql_real_escape_string($_POST['cfg_map']);
    $post_cfg_password    = mysql_real_escape_string($_POST['cfg_password']);
    $post_cfg_internet    = mysql_real_escape_string($_POST['cfg_internet']);
    
    // Private Notes
    $post_notes           = mysql_real_escape_string($_POST['notes']);
        

    //
    // Update server
    //
    $update_supp_srv = "UPDATE cfg SET 
                          available = '$post_available',
                          type = '$post_type',
                          max_slots = '$post_max_slots',
                          based_on = '$post_based_on',
                          is_steam = '$post_is_steam',
                          long_name = '$post_long_name',
                          short_name = '$post_short_name',
                          query_name = '$post_query_engine',
                          mod_name = '$post_mod_name',
                          steam_name = '$post_steam_name',
                          nickname = '$post_nickname',
                          description = '$post_description',
                          executable = '$post_executable',
                          map = '$post_map',
                          style = '$post_style',
                          log_file = '$post_log_file',
                          setup_cmd = '$post_setup_cmd',
                          working_dir = '$post_working_dir',
                          cmd_line = '$post_cmd_line',
                          port = '$post_port',
                          reserved_ports = '$post_reserved_ports',
                          tcp_ports = '$post_tcp_ports',
                          udp_ports = '$post_udp_ports',
                          config_file = '$post_config_file',
                          pid_file = '$post_pid_file',
                          cfg_ip = '$post_cfg_ip',
                          cfg_port = '$post_cfg_port',
                          cfg_max_slots = '$post_cfg_max_slots',
                          cfg_map = '$post_cfg_map',
                          cfg_password = '$post_cfg_password',
                          cfg_internet = '$post_cfg_internet',
                          notes = '$post_notes' 
                        WHERE id = '$url_id'";
    
    @mysql_query($update_supp_srv) or die('<center><b>Error:</b> <i>supportedserverconfigs.php</i>: Failed to update the Supported Server!</center>');
    
    // Redirect to supportedserverconfigs.php
    header("Location: supportedserverconfigs.php?id=$url_id&info=updated");
    exit;
}
