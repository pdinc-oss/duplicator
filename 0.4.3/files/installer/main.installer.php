<?php
/*
  Copyright 2011-12 Cory Lamle  lifeinthegrid.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

  SOURCE CONTRIBUTORS:
  Gaurav Aggarwal
  David Coveney of Interconnect IT Ltd
  https://github.com/interconnectit/Search-Replace-DB/
 */

//DOWNLOAD ONLY: 
if (isset($_GET['get']) && isset($_GET['file']) && file_exists($_GET['file'])) {
    if (strstr($_GET['file'], '_installer.php') || strstr($_GET['file'], 'installer.rescue.php')) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=installer.php');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($_GET['file']));
        @ob_clean();
        @flush();
        if (@readfile($_GET['file']) == false) {
            $data = file_get_contents($_GET['file']);
            if ($data == false) {
                die("Unable to read installer file.  The server currently has readfile and file_get_contents disabled on this server.  Please contact your server admin to remove this restriction");
            } else {
                print $data;
            }
        }
        exit;
    } else {
        header("HTML/1.1 404 Not Found", true, 404);
        header("Status: 404 Not Found");
    }
}

//Prevent Access from rovers or direct browsing in snapshop directory
if (file_exists('dtoken.php')) {
    exit;
}
?>

<?php if (false) : ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Error: PHP is not running</title>
        </head>
        <body>
            <h2>Error: PHP is not running</h2>
            <p>Duplicator requires that your web server is running PHP. Your server does not have PHP installed, or PHP is turned off.</p>
        </body>
    </html>
<?php endif; ?> 


<?php
/* ==============================================================================================
  ADVANCED FEATURES - Allows admins to perform aditional logic on the import.

  $GLOBALS['TABLES_SKIP_COLS']
  Add Known column names of tables you don't want the search and replace logic to run on.
  $GLOBALS['REPLACE_LIST']
  Add additional search and replace items add list here
  Example: array(array('search'=> '/html/oldpath/images',  'replace'=> '/html/newpath/images'));
  ================================================================================================= */

$GLOBALS['FW_TABLEPREFIX'] = '%fwrite_wp_tableprefix%';
$GLOBALS['FW_URL_OLD'] = '%fwrite_url_old%';
$GLOBALS['FW_URL_NEW'] = '%fwrite_url_new%';
$GLOBALS['FW_PACKAGE_NAME'] = '%fwrite_package_name%';
$GLOBALS['FW_SECURE_NAME'] = '%fwrite_secure_name%';
$GLOBALS['FW_DBHOST'] = '%fwrite_dbhost%';
$GLOBALS['FW_DBNAME'] = '%fwrite_dbname%';
$GLOBALS['FW_DBUSER'] = '%fwrite_dbuser%';
$GLOBALS['FW_BLOGNAME'] = '%fwrite_blogname%';
$GLOBALS['FW_RESCUE_FLAG'] = '%fwrite_rescue_flag%';
$GLOBALS['FW_WPROOT'] = '%fwrite_wproot%';

//DATABASE SETUP: all time in seconds	
$GLOBALS['DB_MAX_TIME'] = 5000;
$GLOBALS['DB_MAX_PACKETS'] = 268435456;
ini_set('mysql.connect_timeout', '5000');

//PHP SETUP: all time in seconds
ini_set('memory_limit', '5000M');
ini_set("max_execution_time", '5000');
ini_set("max_input_time", '5000');
ini_set('default_socket_timeout', '5000');
@set_time_limit(0);

$GLOBALS['DBCHARSET_DEFAULT'] = 'utf8';
$GLOBALS['DBCOLLATE_DEFAULT'] = 'utf8_general_ci';

//UPDATE TABLE SETTINGS
$GLOBALS['TABLES_SKIP_COLS'] = array('');
$GLOBALS['REPLACE_LIST'] = array();


/* ================================================================================================
  END ADVANCED FEATURES: Do not edit below here.
  =================================================================================================== */

//CONSTANTS
define("DUPLICATOR_SSDIR_NAME", 'wp-snapshots');  //This should match DUPLICATOR_SSDIR_NAME in duplicator.php
//GLOBALS
$GLOBALS['DUPLICATOR_INSTALLER_VERSION'] = '0.4.3';
$GLOBALS["SQL_FILE_NAME"] = "installer-data.sql";
$GLOBALS["LOG_FILE_NAME"] = "installer-log.txt";
$GLOBALS['SEPERATOR1'] = str_repeat("********", 10);
$GLOBALS['LOGGING'] = isset($_POST['logging']) ? $_POST['logging'] : 1;

$GLOBALS['CURRENT_ROOT_PATH'] = dirname(__FILE__);
$GLOBALS['CHOWN_ROOT_PATH'] = @chmod("{$GLOBALS['CURRENT_ROOT_PATH']}", 0755);
$GLOBALS['CHOWN_LOG_PATH'] = @chmod("{$GLOBALS['CURRENT_ROOT_PATH']}/{$GLOBALS['LOG_FILE_NAME']}", 0644);
$GLOBALS['URL_SSL'] = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? true : false;
$GLOBALS['URL_PATH'] = ($GLOBALS['URL_SSL']) ? "https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}" : "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";

//SHARED POST PARMS
$_POST['action_step'] = isset($_POST['action_step']) ? $_POST['action_step'] : "0";
$_POST['dbhost'] = isset($_POST['dbhost']) ? trim($_POST['dbhost']) : null;
$_POST['dbuser'] = isset($_POST['dbuser']) ? trim($_POST['dbuser']) : null;
$_POST['dbpass'] = isset($_POST['dbpass']) ? trim($_POST['dbpass']) : null;
$_POST['dbname'] = isset($_POST['dbname']) ? trim($_POST['dbname']) : null;
$_POST['dbcharset'] = isset($_POST['dbcharset']) ? trim($_POST['dbcharset']) : $GLOBALS['DBCHARSET_DEFAULT'];
$_POST['dbcollate'] = isset($_POST['dbcollate']) ? trim($_POST['dbcollate']) : $GLOBALS['DBCOLLATE_DEFAULT'];


//Restart log if user starts from step 1
if ($_POST['action_step'] == 1) {
    $GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_NAME'], "w+");
} else {
    $GLOBALS['LOG_FILE_HANDLE'] = @fopen($GLOBALS['LOG_FILE_NAME'], "a+");
}
?>

@@INC.UTILS.PHP@@

<?php
if (isset($_POST['action_ajax'])) {
    switch ($_POST['action_ajax']) {
        case "1" :
            ?> @@AJAX.STEP1.PHP@@ <?php break;
        case "2" :
            ?> @@AJAX.STEP2.PHP@@ <?php
            break;
    }
    @fclose($GLOBALS["LOG_FILE_HANDLE"]);
    die("");
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex,nofollow">
        <title>Wordpress Duplicator</title>
        @@INC.STYLE.CSS@@
        @@INC.SCRIPTS.JS@@
    </head>
    <body>

        <div id="content">
            <!-- =========================================
            HEADER TEMPLATE: Common header on all steps -->
            <table cellspacing="0" class="header-wizard">
                <tr>
                    <td style="width:100%;">
                        <div style="font-size:19px; text-shadow:1px 1px 1px #777;">
                            <!-- !!DO NOT CHANGE/EDIT OR REMOVE PRODUCT NAME!!
                            If your interested in Private Label Rights please contact us at the URL below to discuss
                            customizations to product labeling: http://lifeinthegrid.com/services/	-->
                            &nbsp; Duplicator - Installer
                        </div>
                    </td>
                    <td style="white-space:nowrap;padding:4px">
                        <select id="dup-hlp-lnk">
                            <option value="null"> - Online Resources -</option>
                            <option value="http://lifeinthegrid.com/duplicator-docs">&raquo; Knowledge Base</option>
                            <option value="http://lifeinthegrid.com/duplicator-guide">&raquo; User Guide</option>
                            <option value="http://lifeinthegrid.com/duplicator-faq">&raquo; Common FAQs</option>
                            <option value="http://lifeinthegrid.com/duplicator-hosts">&raquo; Approved Hosts</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        $step1CSS = ($_POST['action_step'] <= 1) ? "active-step" : "complete-step";
                        $step2CSS = ($_POST['action_step'] == 2) ? "active-step" : "";

                        $step3CSS = "";
                        if ($_POST['action_step'] == 3) {
                            $step2CSS = "complete-step";
                            $step3CSS = "active-step";
                        }
                        ?>
                        <div class="wizard-steps">
                            <div class="<?php echo $step1CSS; ?>"><a><span>1</span> Deploy</a></div>
                            <div class="<?php echo $step2CSS; ?>"><a><span>2</span> Update </a></div>
                            <div class="<?php echo $step3CSS; ?>"><a><span>3</span> Test </a></div>
                        </div>
                        <div style="float:right; padding-right:8px">
                            <i style='font-size:11px; color:#999'>installer version: <?php echo $GLOBALS['DUPLICATOR_INSTALLER_VERSION'] . $GLOBALS['FW_RESCUE_FLAG'] ?></i> &nbsp;
                            <a href="javascript:void(0)" onclick="Duplicator.dlgHelp()">[Help]</a>
                        </div>
                    </td>
                </tr>
            </table>	

            <!-- =========================================
            FORM DATA: Data Steps -->
            <div id="content-inner">
		<?php
		switch ($_POST['action_step']) {
		    case "0" :
			?> @@VIEW.STEP1.PHP@@ <?php
		    break;
		    case "1" :
			?> @@VIEW.STEP1.PHP@@ <?php
		    break;
		    case "2" :
			?> @@VIEW.STEP2.PHP@@ <?php
		    break;
		    case "3" :
			?> @@VIEW.STEP3.PHP@@ <?php
		    break;
		}
		?>
            </div>
        </div><br/>


        <!-- =========================================
        HELP FORM -->
        <div id="dup-main-help" title="Quick Help" style="display:none; font-size:12px">

            <div style="text-align:center">For in-depth help please see the <a href="http://lifeinthegrid.com/duplicator-docs" target="_blank">online resources</a></div>

            <h3>Step 1 - Deploy</h3>
            <div id="dup-help-step1" class="dup-help-page">
                <!-- MYSQL SERVER -->
                <fieldset>
                    <legend><b>MySQL Server</b></legend>

                    <b>Host:</b><br/>
                    The name of the host server that the database resides on.  Many times this will be localhost, however each hosting provider will have it's own naming convention please check with your server administrator.
                    <br/><br/>

                    <b>User:</b><br/>
                    The name of a MySQL database server user. This is special account that has privileges to access a database and can read from or write to that database.  <i style='font-size:11px'>This is <b>not</b> the same thing as your WordPress administrator account</i> 
                    <br/><br/>

                    <b>Password:</b><br/>
                    The password of the MySQL database server user.
                    <br/><br/>

                    <b>Database Name:</b><br/>
                    The name of the database to which this installation will connect and install the new tables onto.
                    <br/><br/>

                    <b>Database Creation:</b><br/>
                    If checked this option will try to create the database if it does not exist.  This option will not work on many hosting providers as they usually lock down the ability to create a database.  If the database does not exist then you will need to login to your control panel and create your database.  Please contact your server administrator for more details.
                    <br/><br/>

                    <b>Table Removal:</b><br/>
                    If checked this will automatically remove all tables in the database you are connecting to.  The Duplicator requires a blank database in order for an install to take place.  Please make sure you have backups of all your data before using an portion of the installer, as this option WILL remove data.
                    <br/>
                </fieldset>				

                <!-- ADVANCED OPTS -->
                <fieldset>
                    <legend><b>Advanced Options</b></legend>
                    <b>Manual Package Extraction:</b><br/>
                    This allows you to manually extract the zip archive on your own. This can be useful if your system does not have the ZipArchive support enabled.
                    <br/><br/>		

                    <b>Turn off wp-admin SSL:</b><br/>
                    Turn off SSL support for WordPress. This sets FORCE_SSL_ADMIN in your wp-config file to false.
                    <br/><br/>	

                    <b>Fix non-breaking space characters:</b><br/>
                    The process will remove utf8 characters represented as 'xC2' 'xA0' and replace with a uniform space.  Use this option if you find strange question marks in you posts
                    <br/><br/>	

                    <b>MySQL Charset &amp; MySQL Collation:</b><br/>
                    When the database is populated from the SQL script it will use this value as part of its connection.  Only change this value if you know what your databases character set should be.
                    <br/>				
                </fieldset>			
            </div>

            <h3>Step 2 - Update</h3>
            <div id="dup-help-step1" class="dup-help-page">

                <!-- SETTINGS-->
                <fieldset>
                    <legend><b>Settings</b></legend>
                    <b>Old Settings:</b><br/>
                    The URL and Path settings are the original values that the package was created with.  These values should not be changed.
                    <br/><br/>

                    <b>New Settings:</b><br/>
                    These are the new values (URL, Path and Title) you can update for the new location at which your site will be installed at.
                    <br/>		
                </fieldset>
				
				<!-- NEW ADMIN ACCOUNT-->
                <fieldset>
                    <legend><b>New Admin Account</b></legend>
                    <b>Username:</b><br/>
                    The new username to create.  This will create a new WordPress administrator account.  Please note that usernames are not changeable from the within the UI.
                    <br/><br/>

                    <b>Password:</b><br/>
                    The new password for the user. 
                    <br/>		
                </fieldset>

                <!-- ADVANCED OPTS -->
                <fieldset>
                    <legend><b>Advanced Options</b></legend>
                    <b>Site URL:</b><br/>
                    For details see WordPress <a href="http://codex.wordpress.org/Changing_The_Site_URL" target="_blank">Site URL</a> &amp; <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory" target="_blank">Alternate Directory</a>.  If you're not sure about this value then leave it the same as the new settings URL.
                    <br/><br/>

                    <b>Scan Tables:</b><br/>
                    Select the tables to be updated. This process will update all of the 'Old Settings' with the 'New Settings'. Hold down the 'ctrl key' to select/deselect multiple.
                    <br/><br/>

                    <b>Activate Plugins:</b><br/>
                    These plug-ins are the plug-ins that were activated when the package was created and represent the plug-ins that will be activated after the install.
                    <br/><br/>

                    <b>Post GUID:</b><br/>
                    If your moving a site keep this value checked. For more details see the <a href="http://codex.wordpress.org/Changing_The_Site_URL#Important_GUID_Note" target="_blank">notes on GUIDS</a>.	Changing values in the posts table GUID column can change RSS readers to evaluate that the posts are new and may show them in feeds again.		
                    <br/>		
                </fieldset>

            </div>

            <h3>Step 3 - Test</h3>
            <fieldset>
                <legend><b>Final Steps</b></legend>

                <b>Resave Permalinks</b><br/>
                Re-saving your perma-links will reconfigure your .htaccess file to match the correct path on your server.  This step requires logging back into the WordPress administrator.
                <br/><br/>

                <b>Delete Installer Files</b><br/>
                When you're completed with the installation please delete all installer files.  Leaving these files on your server can impose a security risk!
                <br/><br/>

                <b>Test Entire Site</b><br/>
                After the install is complete run through your entire site and test all pages and posts.
                <br/><br/>

                <b>View Install Report</b><br/>
                The install report is designed to give you a synopsis of the possible errors and warnings that may exist after the installation is completed.
                <br/>
            </fieldset>

        </div>


    </body>
</html>