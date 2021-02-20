@if ($message = Session::get('success'))
	<div class="alert alert-success alert-block">
	    <button type="button" class="close" data-dismiss="alert">×</button> 
	    <p style="margin: 0; text-align: center;"><strong>{{ $message }}</strong></p>
	</div>
@endif


@if ($message = Session::get('error'))
	<div class="alert alert-danger alert-block">
	    <button type="button" class="close" data-dismiss="alert">×</button> 
	    <p style="margin: 0; text-align: center;"><strong>{{ $message }}</strong></p>
	</div>
@endif


@if ($message = Session::get('warning'))
	<div class="alert alert-warning alert-block">
	    <button type="button" class="close" data-dismiss="alert">×</button> 
	    <p style="margin: 0; text-align: center;"><strong>{{ $message }}</strong></p>
	</div>
@endif