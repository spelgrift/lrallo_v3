<?php
	if (isset($this->js)){
		foreach($this->js as $js){
			echo '<script src="'.URL.'public/js/'.$js.'"></script>';
		}
	}
?>
</body>
</html>