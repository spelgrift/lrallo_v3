<?php
	$url = URL.$this->pageAttr['path']."/edit";
	$type = ucfirst($this->pageAttr['type']);
	if($this->pageAttr['home']){
		$url = URL.'page/edithome';
		$type = "Homepage";
	}
?>
<ul class="nav navbar-nav">
	<li>
		<a href='<? echo $url; ?>'>
			<i class='fa fa-fw fa-sliders'></i> Edit <? echo $type; ?>
		</a>
	</li>
</ul>
