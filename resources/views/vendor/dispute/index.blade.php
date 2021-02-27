@extends('layouts.vendor') 

@section('content')  
					<input type="hidden" id="headerdata" value="{{ __("CUSTOMER") }}">
					<div class="content-area">
						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
										<h4 class="heading">{{ $langg->lang378 }}</h4>
										<ul class="links">
											<li>
												<a href="{{ route('vendor-dashboard') }}">{{ __("Dashboard") }} </a>
											</li>
											<li>
												<a href="">{{ $langg->lang378 }}</a>
											</li>
										</ul>
								</div>
							</div>
						</div>
						<div class="product-area">
							<div class="row">
								<div class="col-lg-12">
								    <div class="header-area d-flex align-items-center">
							<h4 class="title">
									{{ $langg->lang378 }} <a data-toggle="modal" data-target="#vendorform" class="mybtn1" href="javascript:;"> <i class="fas fa-envelope"></i> {{ $langg->lang379 }}</a>
								</h4>
                            
							</div>
									<div class="mr-table allproduct">
										@include('includes.admin.form-success') 
										<div class="table-responsiv">
										<table id="example" class="table table-hover dt-responsive" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>{{ $langg->lang380 }}</th>
														<th>{{ $langg->lang381 }}</th>
														<th>{{ $langg->lang382 }}</th>
														<th>{{ $langg->lang383 }}</th>
													</tr>
												</thead>
												<tbody>
                        @foreach($convs as $conv)
                        
                          <tr class="conv">
                            <input type="hidden" value="{{$conv->id}}">
                            <td>{{$conv->subject}}</td>
                            <td>{{$conv->message}}</td>

                            <td>{{$conv->updated_at->diffForHumans()}}</td>
                            <td>
                              <a href="{{route('vendor-message-show',$conv->id)}}" class="link view"><i class="fa fa-eye"></i></a>
                              <a href="javascript:;" data-toggle="modal" data-target="#confirm-delete" data-href="{{route('user-message-delete1',$conv->id)}}"class="link remove"><i class="fa fa-trash"></i></a>
                            </td>

                          </tr>
                        @endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>



{{-- MESSAGE MODAL --}}
<div class="sub-categori">
	<div class="modal" id="vendorform" tabindex="-1" role="dialog" aria-labelledby="vendorformLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="vendorformLabel">{{ __("Send Message") }}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
				</div>
			<div class="modal-body">
				<div class="container-fluid p-0">
					<div class="row">
						<div class="col-md-12">
							<div class="contact-form">
                <form id="emailreply1">
                  {{csrf_field()}}
                  <ul>
                    <li>
                      <input type="text" class="input-field" id="order" name="order_number" placeholder="{{ $langg->lang386 }} *" required="">
                    </li>

                    <li>
                      <input type="text" class="input-field" id="subj1" name="subject" placeholder="{{ $langg->lang387 }} *" required="">
                    </li>
                    <li>
                      <textarea class="input-field textarea" name="message" id="msg1" placeholder="{{ $langg->lang388 }} *" required=""></textarea>
                    </li>
                  </ul>
                  <input type="hidden"  name="type" value="Dispute">
                  <button class="submit-btn" id="emlsub" type="submit">{{ $langg->lang366 }}</button>
                </form>
              </div>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

{{-- MESSAGE MODAL ENDS --}}


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="modal1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

    <div class="modal-header d-block text-center">
        <h4 class="modal-title d-inline-block">{{ $langg->lang367 }}</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
    </div>
                <div class="modal-body">
            <p class="text-center">{{ $langg->lang368 }}</p>
            <p class="text-center">{{ $langg->lang369 }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ $langg->lang370 }}</button>
                    <a class="btn btn-danger btn-ok">{{ $langg->lang371 }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection    

@section('scripts')

<script type="text/javascript">
    
          $(document).on("submit", "#emailreply1" , function(){
          var token = $(this).find('input[name=_token]').val();
          var subject = $(this).find('input[name=subject]').val();
          var message =  $(this).find('textarea[name=message]').val();
          var $type  = $(this).find('input[name=type]').val();
          var order = $('#order').val();
          $('#subj1').prop('disabled', true);
          $('#msg1').prop('disabled', true);
          $('#emlsub1').prop('disabled', true);
     $.ajax({
            type: 'post',
            url: "{{route('user-send-message')}}",
            data: {
                '_token': token,
                'subject'   : subject,
                'message'  : message,
                'type'   : $type,
                'order'  : order
                  },
            success: function( data) {
          $('#subj1').prop('disabled', false);
          $('#msg1').prop('disabled', false);
          $('#subj1').val('');
          $('#msg1').val('');
        $('#emlsub1').prop('disabled', false);
        if(data == 1)
          toastr.success("{{ $langg->message_sent }}");
        else
          toastr.success("{{ $langg->message_sent }}");
          
           location.reload();
        $('.close').click();
            }

        });          
          return false;
        });

</script>


<script type="text/javascript">

      $('#confirm-delete').on('show.bs.modal', function(e) {
          $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
      });

</script>

@endsection