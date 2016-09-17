<?
foreach($this->images as $img)
{
	$image = URL.$img[$this->_device.'Version'];
	$position = $img['position'];
	$caption = $img['caption'];

	if($position == 0) {
		$class = 'slide active';
	} else {
		$class = 'slide';
	}

	echo "<div class='$class' data-order='$position'><img src='$image' title=\"$caption\"></div>";
}
?>