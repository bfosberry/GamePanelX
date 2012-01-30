<?php
//
// Test SSH Connection
//
function gpx_remote_test($id)
{
    require(GPX_DOCROOT . '/include/db.php');
    require(GPX_DOCROOT . '/include/functions/ssh.php');
    
    // Encryption Key
    $enc_key = $config['encrypt_key'];
    
    // Escape all given values
    $safe_id = mysql_real_escape_string($id);
    
    // Get server info
    $query_server =  "SELECT 
                        ip,
                        AES_DECRYPT(conn_user, '$enc_key') AS conn_user,
                        AES_DECRYPT(conn_pass, '$enc_key') AS conn_pass,
                        AES_DECRYPT(conn_port, '$enc_key') AS conn_port 
                      FROM network 
                      WHERE id = '$safe_id'";
    
    // Get info for this server
    $result_server = @mysql_query($query_server) or die('<center><b>Error:</b> <i>remote.php</i>: Failed to get server info!</center>');

    while($row_server = mysql_fetch_array($result_server))
    {
        $conn_ip    = $row_server['ip'];
        $conn_user  = $row_server['conn_user'];
        $conn_pass  = $row_server['conn_pass'];
        $conn_port  = $row_server['conn_port'];
    }
    
    
    // Run the SSH Test and test files
    $ssh_cmd = '$HOME/scripts/CheckInstall';
    $result_test = gpx_ssh_exec($conn_ip,$conn_port,$conn_user,$conn_pass,$ssh_cmd,true);
    //die('<center><b>Error:</b> <i>remote.php:</i> Test connection failed!</center>');

    if(trim($result_test) == 'success')
    {
        return 'success';
    }
    else
    {
        return trim($result_test);
    }
}











//
// Test SSH Connection
//
function gpx_remote_get_home($networkid)
{
    // From API or Installer
    if(!defined('GPX_DOCROOT'))
    {
        define('GPX_DOCROOT', '../');
    }
    
    require(GPX_DOCROOT . '/include/db.php');
    require(GPX_DOCROOT . '/include/functions/ssh.php');
    
    // Encryption Key
    $enc_key = $config['encrypt_key'];
    
    // Escape all given values
    $safe_id = mysql_real_escape_string($networkid);
    
    // Get server info
    $query_server =  "SELECT 
                        ip,
                        AES_DECRYPT(conn_user, '$enc_key') AS conn_user,
                        AES_DECRYPT(conn_pass, '$enc_key') AS conn_pass,
                        AES_DECRYPT(conn_port, '$enc_key') AS conn_port 
                      FROM network 
                      WHERE id = '$safe_id'";
    
    // Get info for this server
    $result_server = @mysql_query($query_server) or die('<center><b>Error:</b> <i>remote.php</i>: Failed to get server info!</center>');

    while($row_server = mysql_fetch_array($result_server))
    {
        $conn_ip    = $row_server['ip'];
        $conn_user  = $row_server['conn_user'];
        $conn_pass  = $row_server['conn_pass'];
        $conn_port  = $row_server['conn_port'];
    }
    
    
    // Run the SSH Test and test files
    $ssh_cmd = 'echo $HOME';
    $result_test = gpx_ssh_exec($conn_ip,$conn_port,$conn_user,$conn_pass,$ssh_cmd,true);

    return $result_test;
}















//
// Get remote server cpu,memory,load info
//
function remote_gpx_remote_serverinfo($networkid)
{
    require(GPX_DOCROOT . '/include/db.php');
    require(GPX_DOCROOT . '/include/functions/ssh.php');
    
    // Encryption Key
    $enc_key = $config['encrypt_key'];
    
    // Escape all given values
    $safe_id = mysql_real_escape_string($networkid);
    
    // Get server info
    $query_server =  "SELECT 
                        ip,
                        AES_DECRYPT(conn_user, '$enc_key') AS conn_user,
                        AES_DECRYPT(conn_pass, '$enc_key') AS conn_pass,
                        AES_DECRYPT(conn_port, '$enc_key') AS conn_port 
                      FROM network 
                      WHERE id = '$safe_id'";
    
    // Get info for this server
    $result_server = @mysql_query($query_server) or die('<center><b>Error:</b> <i>remote.php</i>: Failed to get server info!</center>');

    while($row_server = mysql_fetch_array($result_server))
    {
        $conn_ip    = $row_server['ip'];
        $conn_user  = $row_server['conn_user'];
        $conn_pass  = $row_server['conn_pass'];
        $conn_port  = $row_server['conn_port'];
    }
    
    
    
    // System Commands
    $remote_load_avg    = 'cat /proc/loadavg | awk \'{print $1}\'';
    $remote_cpu_total   = 'cat /proc/cpuinfo | grep processor | wc -l';
    $remote_cpu_type    = 'cat /proc/cpuinfo | grep "model name" | awk \'{print $4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15}\' | head -n 1';
    $remote_meminfo     = 'free -m | grep Mem | awk \'{print $2,$3,$4}\'';
    $remote_disk_usage  = 'df -h | grep -v "Filesystem"';

    // Run the commands all-in-one
    $separater      = '%gpx%';
    $ssh_cmd        = $remote_load_avg . " ; echo $separater ; " . $remote_cpu_total . " ; echo $separater ; " . $remote_cpu_type . " ; echo $separater ; " . $remote_meminfo . " ; echo $separater ; " . $remote_disk_usage;


    // Run the commands (with server response)
    if(!$result_info = gpx_ssh_exec($conn_ip,$conn_port,$conn_user,$conn_pass,$ssh_cmd,true))
    {
        return false;
    }
    else
    {
        return $result_info;
    }
}











########################################################################



//
// Get remote load average and memory info (for cron job)
//
function gpx_remote_loadinfo($networkid)
{
    //require(GPX_DOCROOT . '/include/db.php');
    require_once(GPX_DOCROOT . '/include/functions/ssh.php');
    
    // Encryption Key
    $enc_key = GPX_ENCKEY;
    
    // Escape all given values
    $safe_id = mysql_real_escape_string($networkid);
    
    // Get server info
    $query_server =  "SELECT 
                        ip,
                        AES_DECRYPT(conn_user, '$enc_key') AS conn_user,
                        AES_DECRYPT(conn_pass, '$enc_key') AS conn_pass,
                        AES_DECRYPT(conn_port, '$enc_key') AS conn_port 
                      FROM network 
                      WHERE id = '$safe_id'";
    
    // Get info for this server
    $result_server = @mysql_query($query_server) or die('<center><b>Error:</b> <i>remote.php</i>: Failed to get server info!</center>');

    while($row_server = mysql_fetch_array($result_server))
    {
        $conn_ip    = $row_server['ip'];
        $conn_user  = $row_server['conn_user'];
        $conn_pass  = $row_server['conn_pass'];
        $conn_port  = $row_server['conn_port'];
    }
    
    ####################################################################

    // Get the load average / mem info
    $ssh_cmd  = '$HOME/scripts/CheckLoad';
    
    // Run the commands (with server response)
    if(!$result_info = gpx_ssh_exec($conn_ip,$conn_port,$conn_user,$conn_pass,$ssh_cmd,true))
    {
        return false;
    }
    else
    {
        // Check for no file
        if(preg_match("/No\ such\ file\ or\ directory/i", $result_info))
        {
            return 'FAILURE: No load average script found for Network ID ' . $safe_id . '.  Check the remote server version.';
        }

        ################################################################

        // Parse the results
        //
        // Should be "0.39 ,3960116,379748" (cpu load avg, total mem, free mem)
        $arr_results = explode(',', $result_info);
        
        // Strip whitespace off results
        $load_avg   = trim($arr_results[0]);
        $mem_total  = trim($arr_results[1]);
        $mem_free   = trim($arr_results[2]);
        
        // Double-check results
        if(!is_numeric($mem_total) || !is_numeric($mem_free))
        {
            return 'FAILURE: Received unknown response from the Remote Server';
        }

        ################################################################
        
        // Add to the database
        @mysql_query("INSERT INTO loadavg (networkid,mem_total,mem_free,date_added,cpu) VALUES('$safe_id','$mem_total','$mem_free',NOW(),'$load_avg')");

        
        // Finish
        return true;
    }
}
?>