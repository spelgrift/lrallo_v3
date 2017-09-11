<div class='resizeContentControls text-center'>
	<div class='btn-group' role='group'>
		<button type='button' class='btn btn-sm btn-default decreaseOffset' title='Decrease offset'>
			<i class="fa fa-arrow-left fa-fw"></i>
		</button>

		<button type='button' class='btn btn-sm btn-default increaseWidth' title='Increase block width'>
			<i class="fa fa-plus fa-fw"></i>
		</button>

		<button type='button' class='btn btn-sm btn-default resetBlock' title='Reset block'>
			<i class="fa fa-undo fa-fw"></i>
		</button>

		<button type='button' class='btn btn-sm btn-default decreaseWidth' title='Decrease block width'>
			<i class="fa fa-minus fa-fw"></i>
		</button>
		
		<button type='button' class='btn btn-sm btn-default increaseOffset' title='Increase offset'>
			<i class="fa fa-arrow-right fa-fw"></i>
		</button>
	</div><br>
	<label>Changes affect:</label>
	<select class="form-control targetSize">
		<option value='xs'>Mobile</option>
		<option value='sm' selected>Tablet and larger</option>
		<option value='md'>Desktop and larger</option>
		<option value='lg'>Large Desktop</option>
	</select>
	<p>Current screen size: <span class='screenSize'></span></p>
	<button type="button" class="btn btn-sm btn-primary saveResize">Save</button>
	<button type="button" class="btn btn-sm btn-default cancelResize">Cancel</button>
</div>