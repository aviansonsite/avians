@if(Session::has('SUCCESS_MESSAGE')) 
		<strong id="smsg" hidden class="bg-success"> {!! Session::get('SUCCESS_MESSAGE') !!} </strong>
		{{ Session::forget('SUCCESS_MESSAGE') }}
@endif
@if(Session::has('ERROR_MESSAGE'))  
		<strong id="emsg" hidden class="bg-danger"> {!! Session::get('ERROR_MESSAGE') !!}  </strong>
		{{ Session::forget('ERROR_MESSAGE') }}
@endif

