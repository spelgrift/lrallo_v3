<!--GLOBAL JS-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="<?php echo URL; ?>public/js/bootstrap.min.js"></script>
<script src="<?php echo URL; ?>public/js/login.js"></script>
<!-- Page Specific JS -->
<?php
	if (isset($this->js)){
		foreach($this->js as $js){
			echo '<script src="'.URL.'public/js/'.$js.'"></script>';
		}
	}
?>
</body>
</html>