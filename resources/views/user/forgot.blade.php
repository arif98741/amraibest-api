@extends('layouts.front')

@section('content')

<div class="breadcrumb-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="pages">
                    <li>
                        <a href="{{ route('front.index') }}">
                            {{ $langg->lang17 }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user-forgot') }}">
                            {{ $langg->lang190 }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>


<section class="login-signup forgot-password">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="login-area">
                    <div class="header-area forgot-passwor-area">
                        <h4 class="title">{{ $langg->lang191 }} </h4>
                        <p class="text">Please Write your Mobile No </p>
                    </div>
                    <div class="login-form">
                        @include('includes.admin.form-login')
                           <div class="alert" id="alertc">
          <p class="alert-success" id="alert">
            
          </p>
        </div>
                        <form id="forgotform" action="{{route('user-forgot-submit')}}" method="POST">
                            {{ csrf_field() }}
                            <div class="form-input">
                                <input type="text" name="email" class="User Name" placeholder="Mobile No"
                                    required="" id="mobiles">
                                <i class="icofont-user-alt-5"></i>
                            </div>
                             <div class="help-block with-errors mb-1" >
                      <p id="emailError" class="help-block with-errors"></p>
                  </div>
                                   <div class="form-group has-feedback otp">
          
            
             <div class="form-input">
                    <input type="text" class="Password" name="otp" id="otp" placeholder="OTP" required="">
                    <i class="icofont-refresh"></i>
                  </div>
            <div class="help-block with-errors otp-error" id="otpError">
       <button id="regenerateOTP" class="btn btn-danger btn_shadow" style="border-radius: 0;" onclick="sendOtp()" >Resend OTP <span id="timer"></span></button> 
        </div>
      </div>
                            <div class="to-login-page">
                                <a href="{{ route('user.login') }}">
                                    {{ $langg->lang194 }}
                                </a>
                            </div>
                            <input class="authdata" type="hidden" value="{{ $langg->lang195 }}">
                              <button type="button" class="submit-btn sent-otp" onclick="sendOtp()">Send OTP</button>
                  
                  <button type="submit" class="submit-btn otp">{{ $langg->lang196 }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

    <script>
      let timerOn = true;

function timer(remaining) {
  var m = Math.floor(remaining / 60);
  var s = remaining % 60;
  
  m = m < 10 ? '0' + m : m;
  s = s < 10 ? '0' + s : s;
  document.getElementById('timer').innerHTML = m + ':' + s;
  remaining -= 1;
  
  if(remaining >= 0 && timerOn) {
    setTimeout(function() {
        timer(remaining);
    }, 1000);
    return;
  }

  if(!timerOn) {
    // Do validate stuff here
    return;
  }
}


function disableResend()
{
 $("#regenerateOTP").attr("disabled", true);
 timer(60);
  $('#regenerateOTP').show();
  setTimeout(function() {
  
 $('#regenerateOTP').removeAttr("disabled");
      $('#timer').hide();
   
  }, 60000); 
}

      $(document).ready(function(){
  $('.otp').hide();
         $('#regenerateOTP').hide();
         $("#alert").hide();
         $("#alertss").hide();
        $(".alert").hide();
});


   function sendOtp() {
			disableResend();
          
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // alert($('#mobile').val());
            $.ajax( {
                url:'sendOtp-user',
                type:'post',
                data: {'mobile': $('#mobiles').val(),'medium':'forgot'},
                success:function(data) {
                   $("#alertc").show();
                  $("#alert").show();
                    $("#alert").text(data);
                    if(data != 0){
                        $('.otp').show();
                        $('.sent-otp').hide();
                    }else{
                        alert('Mobile No not found');
                    }
                },
                error:function (error) {
                    if(error.responseJSON.email){
                     $('#emailError').text(error.responseJSON.email);
                  }
                }
            });
        }
        
        
</script>

@endsection