
@extends('layouts.vendor') 

@section('content')  
					<input type="hidden" id="headerdata" value="{{ __("CUSTOMER") }}">
					<div class="content-area">
						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
										<h4 class="heading">{{ $langg->lang356 }}</h4>
										<ul class="links">
											<li>
												<a href="{{ route('vendor-dashboard') }}">{{ __("Dashboard") }} </a>
											</li>
											<li>
												<a href="{{ route('vendor-messages') }}">{{ $langg->lang356 }}</a>
											</li>
										</ul>
								</div>
							</div>
						</div>
						<div class="product-area">
							<div class="row">
								<div class="col-lg-12">
								  
									<div class="mr-table allproduct">
										@include('includes.admin.form-success') 

<div class="support-ticket-wrapper ">
                <div class="panel panel-primary">
                      <div class="gocover" style="background: url({{ asset('assets/images/'.$gs->loader) }}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>                  
                    <div class="panel-body" id="messages">
                      @foreach($conv->messages as $message)
                        @if($message->user_id != 0)
                        <div class="single-reply-area user">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="reply-area">
                                        <div class="left">
                                            <p>{{$message->message}}</p>
                                        </div>
                                        <div class="right">
                                            @if($message->conversation->user->is_provider == 1)
                                            <img class="img-circle" src="{{$message->conversation->user->photo != null ? $message->conversation->user->photo : asset('assets/images/noimage.png')}}" alt="">
                                            @else 

                                            <img class="img-circle" src="{{$message->conversation->user->photo != null ? asset('assets/images/users/'.$message->conversation->user->photo) : asset('assets/images/noimage.png')}}" alt="">

                                            @endif
                                            <p class="ticket-date">{{$message->conversation->user->name}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        @else
                        <div class="single-reply-area admin">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="reply-area">
                                        <div class="left">
                                            <img class="img-circle" src="{{ asset('assets/images/admin.jpg')}}" alt="">
                                            <p class="ticket-date">{{ $langg->lang399 }}</p>
                                        </div>
                                        <div class="right">
                                            <p>{{$message->message}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        @endif
                        @endforeach

                    </div>
                    <div class="panel-footer">
                        <form id="messageform" data-href="{{ route('user-message-load',$conv->id) }}" action="{{route('user-message-store')}}" method="POST">
                            {{csrf_field()}}
                            <div class="form-group">
                                <input type="hidden" name="conversation_id" value="{{$conv->id}}">
                                <input type="hidden" name="user_id" value="{{$conv->user->id}}">
                                <textarea class="form-control" name="message" id="wrong-invoice" rows="5" style="resize: vertical;" required="" placeholder="{{ $langg->lang400 }}"></textarea>
                            </div>
                            <div class="form-group">
                                <button class="mybtn1">
                                    {{ $langg->lang401 }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


						</div>
									</div>
								</div>
							</div>
						</div>
					</div>



@endsection



