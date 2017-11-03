<?php  
	@session_start();
	// Gets the current url
	function full_path(){
	    $s = &$_SERVER;
	    $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
	    $sp = strtolower($s['SERVER_PROTOCOL']);
	    $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	    $port = $s['SERVER_PORT'];
	    $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
	    $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
	    $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
	    $uri = $protocol . '://' . $host . $s['REQUEST_URI'];
	    $segments = explode('?', $uri, 2);
	    $url = $segments[0];
	    return $url;
	}
	// Counts the files inside the folder
	function count_files($dir){
		if(!is_dir($dir)) return 1;
		$i = 0; 
	    if ($handle = opendir($dir)) {
	        while (($file = readdir($handle)) !== false){
	            if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
	                $i++;
	        }
	    }
	    return $i;
	}
	// Limits characters
	function limit_text($text, $limit = 12){
		return (strlen($text) >= $limit) ? substr($text, 0, $limit).'...' : $text;
	}
	// Removes all spaces
	function no_spaces($text){
		return str_replace(' ', '', $text);
	}
	// Returns Bootstrap Theme
	$themes = array(
		'cerulean',
		'cosmo',
		'cyborg',
		'darkly',
		'flatly',
		'journal',
		'lumen',
		'paper',
		'readable',
		'sandstone',
		'simplex',
		'slate',
		'solar',
		'spacelab',
		'superhero',
		'united',
		'yeti',
	);
	if(@$_GET['theme'] != '' && @$_GET['theme'] <= 16){
		@$_SESSION['theme'] = @$_GET['theme'];
	}
	$theme_index = (@$_SESSION['theme'] != '') ? $_SESSION['theme'] : 2;
	$theme = 'https://bootswatch.com/3/'.$themes[$theme_index].'/bootstrap.min.css';
	// Gets all the files and directories inside current folder
	$dirs = scandir('.'); 
	// Removes backlink and index link
	$dirs = array_diff($dirs, ["..", ".", "index.php"]); 	
	// Sorts the files alphabetically
	$i = 0;
	foreach ($dirs as $key => $dir){
		$dirs_sort[strtolower($dir)] = $dir;		
		$i++;
	}
	ksort($dirs_sort);
	// Gets the number of folders inside
	$dirs = scandir('.'); 
	// Removes backlink and index link
	$dirs = array_diff($dirs, ["..", ".", "index.php"]); 	
	$total_folders = count($dirs);
?>
<!DOCTYPE html>
<html>
   <head>
      	<title>PHP Projects</title>
      	<!-- CSS -->
      	<link rel="stylesheet" type="text/css" href="<?php echo $theme; ?>" />     	
      	<link rel="stylesheet" type="text/css" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />      	
      	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
      	<link rel="shortcut icon" href="http://www.iconarchive.com/download/i61296/hopstarter/mac-folders-2/Folder-Mac.ico" type="image/x-icon">
      	<style type="text/css">
	        .hide { 
	        	display: none; 
	        }
	        .show { 
	        	display: block; 
	        }
	        .well { 
	        	cursor: pointer; 
	        }
	        .well:hover {  
	        	filter: invert(100%);
	        }
	        .none { 
	        	margin: 0; 
	        	padding-left: 10px; 
	        	padding-right: 10px; 
	        	display: none; 
	        }
	        .logo {
	        	vertical-align: inherit !important;
	        }	 
	        @media (max-width: 991px){
	        	.col-total {
	        		text-align: center;
	        	}
	        }
  		</style>
   	</head>
   	<body>
    	<div class="row" style="width:100%;padding:5px;background:whitesmoke;margin:0;border-bottom:1px solid #e3e3e3;">
    		<div class="col-md-2"></div>
    		<div class="col-md-2">
    			<select class="form-control" onchange="location.href='.?theme='+this.value;">
    				<?php foreach ($themes as $key => $theme) { ?>
    				<option value="<?php echo $key; ?>" <?php if($theme_index == $key) echo 'selected="selected"'; ?>>
    					<?php echo $theme; ?>
    				</option>
    				<?php } ?>
    			</select>
    		</div>
	        <div class="col-md-4" align="center">
	            <img class="logo" src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/PHP-logo.svg/500px-PHP-logo.svg.png" height="40" style="display: inline-table;" />
	            &nbsp; &nbsp;	           
	            <div class="input-group" style="width:75%;display:inline-table;margin:0;">
		      		<input class="form-control search" placeholder="Search Folder or Files"  onkeyup="search(this);" autofocus="autofocus" />
			      	<span class="input-group-btn">
			        	<button class="btn btn-primary" type="button"
			        	onclick="$('.search').val('');clear_search();">
			        		<i class="icon ion-close"></i>
			        	</button>
			      	</span>
			    </div>
	        </div>
	        <div class="col-md-4 col-total" style="padding-top:10px;">
	            Total Folders/Files: 
	            <strong id="total_folders"><?php echo $total_folders; ?></strong>
	        </div>
      	</div>
	  	<div style="margin-top:14px !important;margin:0;white-space:nowrap;" class="folders row">
	        <?php 
	         	$i = 1;  
	         	foreach ($dirs_sort as $key => $dir) { 
	        ?>
	        <div class="col-md-2 col-sm-6 folder show" id="<?php echo no_spaces($dir); ?>">
	            <span class="hidden"><?php echo $dir; ?></span>
	            <div class="well" onclick="location.href='<?php echo $dir; ?>';" 
	               	title="<?php echo full_path().$dir; ?>">
	               	<i class="icon ion-ios-folder"></i> &nbsp; 
	               	<?php echo limit_text($dir); ?>	
	               	<br />
	               	<span class="small">
	               		<strong>Path:</strong> 
	               		/<?php echo limit_text($dir, 15); ?>
	               	</span>
	                <br />
	                <span class="small">
	                	<strong>Files:</strong> 
	                	<span count-up end-val="<?php echo count_files($dir); ?>">
	                		<?php echo count_files($dir); ?>
	                	</span>
	                </span>
	            </div>
	        </div>
	        <?php 
		        	$i++; 
		        } 
	        ?>
	  	</div>	  
	  	<div class="none">
	     	<div class="alert alert-warning" align="center">No results were found</div>
	  	</div>
	  	<!-- JS -->
	  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	  	<script type="text/javascript">
	     	function search(input) {
	            var filter, folders, folder, span, i;			    
	            filter = input.value.toUpperCase();
	            folders = $('.folders');
	            folder = $('.folder');
	            for (i = 0; i < folder.length; i++) {
	                span = folder[i].getElementsByTagName("span")[0];		
	               	if (span.innerHTML.toUpperCase().indexOf(filter) > -1) {     
	                    folder[i].classList.add("show");
	                    folder[i].classList.remove("hide");
	                } else {
	                    folder[i].classList.remove("show");
	                    folder[i].classList.add("hide");
	                }
	                if(!$('.show').length) $('.none').show();
	                else $('.none').hide();	  
	                $('#total_folders').html($('.show').length);     
	            }
	     	}
	     	function clear_search(){
	     		$('.folder').removeClass('hide');
	     		$('.folder').addClass('show');
	     		$('#total_folders').html($('.show').length);
	     		$('.none').hide();
	     	}
	  	</script>
   </body>
</html>