<?php
/**
 * Website: http://www.siteguarding.com/
 * Email: support@siteguarding.com
 *
 * @author John Coggins
 * @version 1.2
 * @date 25 May 2016
 * @package SiteGuarding Antivirus Scanner module
 */
 
// Init
error_reporting( 0 );
ignore_user_abort(true);
set_time_limit ( 600 );

$result = Antivirus::Init();

if ($result !== true) 
{
    Antivirus::TemplateHeader(true);
    $result = $result."<br><br>"."If you have Windows server or problems with the permissions.<br>We advice to download full package (<a target=\"_blank\" href=\"https://www.siteguarding.com/en/download-service/website-antivirus-standalone-package\">Download</a>)";
    Antivirus::PrintPage_Message($result, 'error');
    Antivirus::TemplateFooter();
    exit;
}

    /**
     * Start
     */
    $task = trim($_REQUEST['task']);
    
    Antivirus::TemplateHeader();
    
    // Check is antivirus_installer.php is loaded
    if (file_exists(Antivirus::GetPath().Antivirus::$antivirus_work_folder.'antivirus_installer.php') && $task == '')
    {
        Antivirus::PrintPage_Installation();
    }
    else {
        /**
         * Tasks
         */
        switch ($task)
        {
            case 'Installation':
                $result = Antivirus::Installation();
                if ($result !== true) Antivirus::PrintPage_Message($result, 'error');
                else Antivirus::PrintPage_Dashboard();
                break;
                
            case 'StartScanner':
                Antivirus::StartScanner();
                break;
                
            
            
            
            default:
                Antivirus::PrintPage_Dashboard();
            
        }
        
    }
    
    Antivirus::TemplateFooter();





class Antivirus {
    
    public static $SITEGUARDING_SERVER = 'http://www.siteguarding.com/ext/antivirus/index.php';
    public static $antivirus_work_folder = '/webanalyze/';
    public static $antivirus_assets_folder = '/webanalyze/assets/';
	public static $debug = false;
    
	static function Init()
	{
	    // Remove .htaccess
        if (file_exists(self::GetPath().self::$antivirus_work_folder.'.htaccess')) unlink(self::GetPath().self::$antivirus_work_folder.'.htaccess');
        
        // Create folder /webanalyze/
        if (!file_exists(self::GetPath().self::$antivirus_work_folder))
        {
            if ( !mkdir(self::GetPath().self::$antivirus_work_folder) ) return "Can't create folder ".self::$antivirus_work_folder;
        }
        
        // Create folder /webanalyze/assets/
        if (!file_exists(self::GetPath().self::$antivirus_assets_folder))
        {
            if ( !mkdir(self::GetPath().self::$antivirus_assets_folder) ) return "Can't create folder ".self::$antivirus_assets_folder;
        }
		
		if (!extension_loaded('curl')) 
		{
			return "cURL is not installed or not activated.";
		}
		
        
        $assets_files = array(
            'semantic.min.css',
            'jquery.min.js',
            'semantic.min.js',
            'icons.ttf',
            'icons.woff',
            'icons.woff2',
            'wpAntivirusSiteProtection-logo.png',
            'canvasloader-min.js',
            'logo_siteguarding.png'
        );
        foreach ($assets_files as $file)
        {
			if (self::$debug) echo "Check: ".$file;
            if (!file_exists(self::GetPath().self::$antivirus_assets_folder.$file) || filesize(self::GetPath().self::$antivirus_assets_folder.$file) == 0 )
            {
            	$destination = self::GetPath().self::$antivirus_assets_folder.$file;
            	$url = 'http://www.siteguarding.com/_get_file.php?file=antivirus_'.$file.'&time='.time();
            	
            	$status = self::CreateRemote_file_contents($url, $destination);
				if ($status === false) $status = self::CreateRemote_file_contents_save($url, $destination);
                if ($status === false) return "Can't get asset file: ".self::$antivirus_assets_folder.$file;
            }
			else if (self::$debug) echo " - file exists"."<br>";
        }
        
        if (!file_exists(self::GetPath().self::$antivirus_work_folder.'antivirus.php') || !file_exists(self::GetPath().self::$antivirus_work_folder.'antivirus_config.php'))
        {
			if ( !file_exists(self::GetPath().self::$antivirus_work_folder.'antivirus_installer.php') || filesize(self::GetPath().self::$antivirus_assets_folder.$file) == 0 )
			{
				$destination = self::GetPath().self::$antivirus_work_folder.'antivirus_installer.php';
				$url = 'http://www.siteguarding.com/_get_file.php?file=antivirus_antivirus_installer.php&time='.time();
				
				$status = self::CreateRemote_file_contents($url, $destination);
				if ($status === false) $status = self::CreateRemote_file_contents_save($url, $destination);
				if ($status === false) return "Can't get antivirus_installer.php"; 
			}
        }
        
        return true;
    }
    
    
	static function GetWebsiteURL()
	{
       $this_filename = pathinfo(__FILE__, PATHINFO_BASENAME);
	   return 'http://'.$_SERVER['HTTP_HOST'].str_replace($this_filename, "", $_SERVER['SCRIPT_NAME']);
    }
    
	static function Get_Access_Key()
	{
        include_once(self::GetPath().self::$antivirus_work_folder.'antivirus_config.php');
        
        return ACCESS_KEY;
    }
    
    
	static function Get_License_info()
	{
        $domain = self::GetDomain();
        $access_key = self::Get_Access_Key();
        
        
    	$link = self::$SITEGUARDING_SERVER.'?action=licenseinfo&type=json&data=';
    	
        $data = array(
    		'domain' => $domain,
    		'access_key' => $access_key,
    		'product_type' => 'any'
    	);
        $link .= base64_encode(json_encode($data));
        
        
        $a = self::GetRemote_file_contents($link, true);
        
        return $a;
      
    }
    
    
	static function GetDomain()
	{
	    $host_info = parse_url(self::GetWebsiteURL());
	    if ($host_info == NULL) return false;
	    $domain = $host_info['host'];
	    if ($domain[0] == "w" && $domain[1] == "w" && $domain[2] == "w" && $domain[3] == ".") $domain = str_replace("www.", "", $domain);
	    //$domain = str_replace("www.", "", $domain);
	    
	    return $domain;
    }
    
	static function GetPath()
	{
	   return dirname(__FILE__);
    }
    
    
    static function Installation()
    {
		// Send data
	    $link = self::$SITEGUARDING_SERVER.'?action=register&type=json&data=';
        
        $domain = self::GetWebsiteURL();
        $email = trim($_REQUEST['email']);
        $access_key = md5(time().$domain.rand(1, 20000).$email);
	    
	    $data = array(
			'domain' => $domain,
			'email' => $email,
			'access_key' => $access_key,
			'errors' => '',
			'call_back' => 1
		);
	    $link .= base64_encode(json_encode($data));
        
        $a = trim(self::GetRemote_file_contents($link));
        
        if ($a == 'installation_ok') return true;
        else return 'Can\'t register your website. cURL doesn\'t work correctly on this server and can\'t get any data from remote servers. Please contact with your hosting support. Very possible that outgoing traffic is blocked by your hosting company.';
    }
    
    
	static function TemplateHeader($remote_assets = false)
	{
	    ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Website Antivirus Scanner by SiteGuarding.com</title>
  
<?php
        if ($remote_assets)
        {
            ?>
                <link rel="stylesheet" type="text/css" href="https://www.siteguarding.com/ext/antivirus/assets/semantic.min.css">
                <script src="https://www.siteguarding.com/ext/antivirus/assets/jquery.min.js"></script>
                <script src="https://www.siteguarding.com/ext/antivirus/assets/semantic.min.js"></script>
            <?php
        }
        else {
            ?>
                <link rel="stylesheet" type="text/css" href="webanalyze/assets/semantic.min.css">
                <script src="webanalyze/assets/jquery.min.js"></script>
                <script src="webanalyze/assets/semantic.min.js"></script>
            <?php
            
        }
?>




  <style type="text/css">
    body {
      background-color: #DADADA;
    }
    body > .grid {
      height: 100%;
    }
    .image {
      margin-top: -100px;
    }
    .column {
      max-width: 450px;
    }
  </style>
</head>
<body>
<?php
    }
    
    
	static function TemplateFooter()
	{
        ?>
</body>
</html>
        <?php
    }
    
    
    static function PrintPage_Message($txt = '', $type = 'error')
    {
        switch ($type)
        {
            case 'error': $type = 'red'; break;
            case 'ok': $type = 'green'; break;
            case 'alert': $type = 'yellow '; break;
            default: $type = '';
        }
        ?>
            <div class="ui middle aligned center aligned grid">
                <div class="column">
                    <div class="ui <?php echo $type; ?> message"><?php echo $txt; ?></div>
                </div>
            </div>
        <?php
    }
    
    
    
    static function PrintPage_Installation()
    {
        ?>
        <script>
        $(document)
        .ready(function() {
          $('.ui.form')
            .form({
              fields: {
                email: {
                  identifier  : 'email',
                  rules: [
                    {
                      type   : 'empty',
                      prompt : 'Please enter your e-mail'
                    },
                    {
                      type   : 'email',
                      prompt : 'Please enter a valid e-mail'
                    }
                  ]
                }
              }
            })
          ;
        })
        ;
        </script>
        
        <div class="ui middle aligned center aligned grid">
          <div class="column left aligned">

            <form method="post" class="ui large form left aligned">
              <div class="ui stacked segment">
              
                <h2 class="ui image header">
                  <img src="<?php echo Antivirus::GetWebsiteURL().Antivirus::$antivirus_assets_folder; ?>wpAntivirusSiteProtection-logo.png" class="image">
                  <div class="content">
                    Antivirus Installation
                  </div>
                </h2>
                
                <div class="field">
                  <label>Website URL</label>
                    <input disabled="disabled" type="text" name="website_url" value="<?php echo Antivirus::GetWebsiteURL(); ?>" placeholder="Please enter your Website URL">
                </div>

                <div class="field">
                  <label>Email</label>
                    <input type="text" name="email" placeholder="Please enter your Email">
                </div>
                
                <div class="ui fluid large green submit button">Install Antivirus</div>
              </div>
        
              <div class="ui error message"></div>
              
              <input type="hidden" name="task" value="Installation">
        
            </form>
        
          </div>
        </div>
        <?php
    }
    
    
    static function PrintBlock_LogoMenu()
    {
        ?>
          <style type="text/css">
          .main.container {
            margin-top: 7em;
          }
          img.logo{width:250px!important;}
          </style>
                <div class="ui borderless  fixed menu">
                    <div class="ui container">
                      <div class="header item">
                        <a href="https://www.siteguarding.com">
                            <img class="logo" src="<?php echo Antivirus::GetWebsiteURL().Antivirus::$antivirus_assets_folder; ?>logo_siteguarding.png">
                        </a>
                      </div>
        
                          <a href="#" class="item">&nbsp;</a>
                          <a href="https://www.siteguarding.com/en/buy-service/website-antivirus-protection" class="ui right floated dropdown item">Get PRO</a>
                          <a href="https://www.siteguarding.com/en/protect-your-website" class="ui right floated dropdown item">Protect Your Website</a>
                          <a href="https://www.siteguarding.com/en/services/malware-removal-service" class="ui right floated dropdown item">Malware Removal Service</a>
                          <a href="https://www.siteguarding.com/en/contacts" class="ui right floated dropdown item">Contact Us</a>
                          
        
                    </div>
                  </div>
        
        <?php
    }
    
    static function PrintPage_Dashboard()
    {
        $license_info = self::Get_License_info();
        //print_r($license_info);
        
        self::PrintBlock_LogoMenu();
        ?>

        <div class="ui middle aligned center aligned grid">
            <div class="ui main text container">




<h2 class="ui dividing header">Antivirus Scanner</h2>



    <div class="ui list">
    	<?php
    	$txt = $license_info['membership'];
    	if ($txt != 'pro') $txt = ucwords($txt);
    	else $txt = '<span class="ui green label">'.ucwords($txt).'<span>';
    	?>
        <p class="item">Your subscription: <b><?php echo $txt; ?></b> valid till: <?php echo $license_info['exp_date']."&nbsp;&nbsp;"; 
        if ($license_info['exp_date'] < date("Y-m-d")) echo '<span class="ui red label">'.'Expired'.'</span> [<a href="https://www.siteguarding.com/en/buy-service/antivirus-site-protection?domain='.urlencode( self::GetWebsiteURL() ).'&email='.urlencode($license_info['email']).'" target="_blank">Upgrade</a>]';
        else if ($license_info['exp_date'] < date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-7, date("Y")))) echo '<span class="msg_box msg_warning">'.'Will Expire Soon'.'</span>';
        ?></p>

    </div>
    
    <div class="ui list">
        <p class="item">Google Blacklist Status: <?php if ($license_info['blacklist']['google'] != 'ok') echo '<span class="ui red label">Blacklisted ['.$license_info['blacklist']['google'].']</span> [<a href="https://www.siteguarding.com/en/services/malware-removal-service" target="_blank">Remove From Blacklist</a>]'; else echo '<span class="ui green label">Not blacklisted</span>'; ?></p>
        <p class="item">File Change Monitoring: <?php if ($license_info['filemonitoring']['status'] == 0) echo '<span class="ui red label">Disabled</span> [<a href="https://www.siteguarding.com/en/protect-your-website" target="_blank">Subscribe</a>]'; else echo '<b>'.$license_info['filemonitoring']['plan'].'</b> ['.$license_info['filemonitoring']['exp_date'].']'; ?></p>
        <?php
        if (count($license_info['reports']) > 0) 
        {
            if ($license_info['last_scan_files_counters']['main'] == 0 && $license_info['last_scan_files_counters']['heuristic'] == 0) echo '<p class="item">Website Status: <span class="ui green label">Clean</span></p>';
            if ($license_info['last_scan_files_counters']['main'] > 0) echo '<p class="item">Website Status: <span class="ui red label">Infected</span> [<a href="https://www.siteguarding.com/en/services/malware-removal-service" target="_blank">Clean My Website</a>]</p>';
            else if ($license_info['last_scan_files_counters']['heuristic'] > 0)  echo '<p class="item">Website Status: <span class="ui red label">Review is required</span> [<a href="https://www.siteguarding.com/en/services/malware-removal-service" target="_blank">Review My Website</a>]</p>';
        }
        else {
            echo '<p class="item">Website Status: <span class="ui red label">Never Analyzed</span></p>';
        }
        ?>
    </div>



<div style="clear:both"></div>




<div class="mod-box">		
<p>To start the scan process click "Start Scanner" button.</p>
<p>Scanner will automatically collect and analyze the files of your website. The scanning process can take up to 10 mins (it depends of speed of your server and amount of the files to analyze).</p>
<p>After full analyze you will get the report. The copy of the report we will send by email for your records.</p>

			
		<form method="post">
		
        
		<div class="startscanner">
            <p style="text-align: center;">
		      <input type="submit" name="submit" id="submit" class="huge ui green button" value="Start Scanner">
          </p>
		</div>
		
		<input type="hidden" name="task" value="StartScanner"/>
		
		</form>
        
        <div class="ui ignored warning message">
        <p>Don't forget to remove antivirus script from the server when analyze is finished. </p>
        </div>

<?php

if (count($license_info['reports']))
{ ?>		
    <h3 class="ui dividing header">Latest Reports</h3>

<?php

    foreach ($license_info['reports'] as $report_info)
    {
        ?><a href="<?php echo $report_info['report_link']."&showtrial=1"; ?>" target="_blank">Click to view report for <?php echo $report_info['domain']; ?>. Date: <?php echo $report_info['date']; ?></a><br /><?php
    }
}

?>

<h3 class="ui dividing header">Extra Options</h3>

<h2 class="ui center aligned header">Do you need clean and protected website? Please learn how it works.</h2>
<p class="ui center aligned"><center>Our security packages cover all your needs. Focus on your business and leave security to us.</center></p>

<iframe src="https://player.vimeo.com/video/140200465" width="100%" height="378" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

<form style="padding: 40px 0 100px 0;" class="ui middle aligned center aligned grid" method="post" action="https://www.siteguarding.com/en/protect-your-website">

		  <input type="submit" name="submit" class="big ui green button center aligned" value="Protect My Website">

	</form>



        </div>
        
        
        <div class="center aligned row">
        <div style="text-align:center">
    		<p>
    		For more information and details about Antivirus Site Protection please <a target="_blank" href="https://www.siteguarding.com/en/antivirus-site-protection">click here</a>.<br /><br />
    		<a href="http://www.siteguarding.com/livechat/index.html" target="_blank">
    			<img src="https://www.siteguarding.com/images/livechat.png"/>
    		</a><br />
    		For any questions and support please use LiveChat or this <a href="https://www.siteguarding.com/en/contacts" rel="nofollow" target="_blank" title="SiteGuarding.com - Website Security. Professional security services against hacker activity. Daily website file scanning and file changes monitoring. Malware detecting and removal.">contact form</a>.<br>
    		<br>
    		Copyright &copy; 2008 - <?php echo date("Y"); ?> <a href="https://www.siteguarding.com/" target="_blank">SiteGuarding.com</a></br>Website Security. Professional security services against hacker activity.<br />
    		</p>
        </div>
        </div>


        <?php
    }
    
    
    
    
    
    static function StartScanner()
    {
        self::PrintBlock_LogoMenu();
        
        $session_report_key = md5(self::GetWebsiteURL().rand(1, 10000).time());
        $license_info = self::Get_License_info(); 
        
        
        ?>
          
        <script src="<?php echo Antivirus::GetWebsiteURL().Antivirus::$antivirus_assets_folder; ?>canvasloader-min.js" type="text/javascript"></script>
        
        <div class="ui middle aligned center aligned grid">
            <div class="ui main text container">
            
            
        <div class="ui middle aligned center aligned grid">
            <div class="ui main text container">
            
            <div class="ui middle aligned center aligned grid">
                <div class="container">
                    <div class="ui yellow message" style="text-align: center;">If the scanning process takes too long. Get the results using the link<br /><a href="https://www.siteguarding.com/antivirus/viewreport?report_id=<?php echo $session_report_key; ?>&showtrial=1" target="_blank">https://www.siteguarding.com/antivirus/viewreport?report_id=<?php echo $session_report_key; ?></a></div>
                </div>
            </div>
                <h2 class="ui header aligned center aligned">Please wait. It can take up to 5 - 10 minutes to get the results.</h2>
                <p style="text-align: center;" id="progress_bar_txt"></p>
                
                <div id="canvasloader-container" style="position:absolute;top:65%;left:50%;"></div>
            
            </div>
        </div>
        
        
            </div>
        </div>
            
            
            <script type="text/javascript">
            	var cl = new CanvasLoader('canvasloader-container');
            	cl.setColor('#4b9307'); // default is '#000000'
            	cl.setShape('spiral'); // default is 'oval'
            	cl.setDiameter(118); // default is 40
            	cl.setDensity(26); // default is 40
            	cl.setSpeed(1); // default is 2
            	cl.show(); // Hidden by default
            	
            	// This bit is only for positioning - not necessary
            	  var loaderObj = document.getElementById("canvasLoader");
            	loaderObj.style.position = "absolute";
            	loaderObj.style["top"] = cl.getDiameter() * -0.5 + "px";
            	loaderObj.style["left"] = cl.getDiameter() * -0.5 + "px";
                
  
            $(document).ready(function(){
            	
            	var refreshIntervalId;
            	
         		<?php
               	$ajax_url = self::GetWebsiteURL().'/webanalyze/antivirus.php?task=scan&access_key='.$license_info['access_key'].'&session_report_key='.$session_report_key.'&email='.$license_info['email'].'&cache='.time();
               	?>
               	var link = "<?php echo $ajax_url; ?>";

				$.post(link, {
					    no_html: "1"
					},
					function(data){
						/*if (data != '') alert(data);*/
					}
				);
				
				
				
				function GetProgress()
				{
             		<?php
                   	$ajax_url = self::GetWebsiteURL().'/webanalyze/antivirus.php?task=scan_status&access_key='.$license_info['access_key'].'&cache='.time();
                   	?>
	               	var link = "<?php echo $ajax_url; ?>";
	
					$.post(link, {
						    no_html: "1"
						},
						function(data){
							if (data == 'report_redirect') 
							{
								document.location.href = 'https://www.siteguarding.com/antivirus/viewreport?report_id=<?php echo $session_report_key; ?>&showtrial=1';
								return;
							}
						    var tmp_data = data.split('|');
						    $("#progress_bar_txt").html(tmp_data[0]+'% - '+tmp_data[1]);
						    
						    if (parseInt(tmp_data[0]) >= 100) $("#adminForm").submit();
						}
					);	
				}
				
				refreshIntervalId =  setInterval(GetProgress, 3000);
				
            });
            
                        
            </script>
            
            
			<form action="<?php echo JRoute::_('index.php?option=com_securapp&task=AntivirusViewReport&showtrial=1'); ?>" method="get" enctype="multipart/form-data" name="adminForm" id="adminForm" >
					<input type="hidden" name="report_id" value="<?php echo $session_report_key; ?>" />
			</form>
        
        
        <?php
    }
    
    

    
    
    static function CreateRemote_file_contents($url, $dst)
    {
        if (extension_loaded('curl')) 
        {
            $dst = fopen($dst, 'w');
            
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
            curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3600000);
            curl_setopt($ch, CURLOPT_FILE, $dst);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 sec
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000); // 10 sec
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $a = curl_exec($ch);
            if ($a === false)  return false;
            
            $info = curl_getinfo($ch);
            
            curl_close($ch);
            fflush($dst);
            fclose($dst);
            
            return $info['size_download'];
        }
        else return false;
    }
    
    
    static function GetRemote_file_contents($url, $parse = false)
    {
        if (extension_loaded('curl')) 
        {
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
            curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3600000);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 sec
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 10000); // 10 sec
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            $output = trim(curl_exec($ch));
            curl_close($ch);
            
            if ($output === false)  return false;
            
            if ($parse === true) $output = (array)json_decode($output, true);
            
            return $output;
        }
        else return false;
    }
    
	static function CreateRemote_file_contents_save($url, $dst)
	{
		$content = self::GetRemote_file_contents($url);
		
		if ($content === false || strlen($content) == 0) return false;
		
		$fp = fopen($dst, 'w');
		if ($fp === false) return false;
		$a = fwrite($fp, $content);
		if ($a === false) return false;
		fclose($fp);
		
		return true;
	}

}

?>