
<!--GLOBAL JS-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo URL; ?>public/js/bootstrap.min.js"></script>

<!-- Custom JS -->
<?php
	if (isset($this->js)){
		foreach($this->js as $js){
			echo '<script src="'.URL.'public/js/'.$js.'"></script>';
		}
	}

?>


</body>
</html>