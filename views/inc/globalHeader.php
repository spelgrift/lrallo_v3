<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type='text/javascript'>
	window.baseURL = <?php echo json_encode(URL); ?>;
	window.blogURL = '<?php echo BLOGURL; ?>';
</script>
<link href="https://fonts.googleapis.com/css?family=Josefin+Sans|News+Cycle:400,700" rel="stylesheet">
<link href="<?php echo URL; ?>public/css/styles.css" rel="stylesheet">
<?php
	// Load additional css files if needed
	if(isset($this->css)){
		foreach($this->css as $css){
			echo '<link href="'.URL.'public/css/'.$css.'" rel="stylesheet">';
		}
	}
?>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


<title><?php echo $this->pageTitle; ?></title>
</head>
<body>
