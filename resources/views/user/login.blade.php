@extends('layouts.front')

@section('content')

    <style>
  .help-block{
    color:red !important;
  }

.alert-success {
    padding: 10px !important;
    text-align: center !important;
}
.alert {
    margin-bottom: 10px !important;
}

</style>

<section class="login-signup">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <nav class="comment-log-reg-tabmenu">
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link login active" id="nav-log-tab" data-toggle="tab" href="#nav-log" role="tab"
              aria-controls="nav-log" aria-selected="true">
              {{ $langg->lang197 }}
            </a>
            <a class="nav-item nav-link" id="nav-reg-tab" data-toggle="tab" href="#nav-reg" role="tab"
              aria-controls="nav-reg" aria-selected="false">
              {{ $langg->lang198 }}
            </a>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-log" role="tabpanel" aria-labelledby="nav-log-tab">
            <div class="login-area">
              <div class="header-area">
                <h4 class="title">{{ $langg->lang172 }}</h4>
              </div>
              <div class="login-form signin-form">
                @include('includes.admin.form-login')
                <form class="mloginform" action="{{ route('user.login.submit') }}" method="POST">
                  {{ csrf_field() }}
                  <div class="form-input">
                    <input type="text" name="email" placeholder="Mobile No" required="">
                    <i class="icofont-user-alt-5"></i>
                  </div>
                  <div class="form-input">
                    <input type="password" class="Password" name="password" placeholder="{{ $langg->lang174 }}"
                      required="">
                    <i class="icofont-ui-password"></i>
                  </div>
                  <div class="form-forgot-pass">
                    <div class="left">
                      <input type="checkbox" name="remember" id="mrp" {{ old('remember') ? 'checked' : '' }}>
                      <label for="mrp">{{ $langg->lang175 }}</label>
                    </div>
                    <div class="right">
                      <a href="{{ route('user-forgot') }}">
                        {{ $langg->lang176 }}
                      </a>
                    </div>
                  </div>
                  <input type="hidden" name="modal" value="1">
                  <input class="mauthdata" type="hidden" value="{{ $langg->lang177 }}">
                  <button type="submit" class="submit-btn">{{ $langg->lang178 }}</button>
                  @if(App\Models\Socialsetting::find(1)->f_check == 1 || App\Models\Socialsetting::find(1)->g_check ==
                  1)
                  <div class="social-area">
                    <h3 class="title">{{ $langg->lang179 }}</h3>
                    <p class="text">{{ $langg->lang180 }}</p>
                    <ul class="social-links">
                      @if(App\Models\Socialsetting::find(1)->f_check == 1)
                      <li>
                        <a href="{{ route('social-provider','facebook') }}">
                          <i class="fab fa-facebook-f"></i>
                        </a>
                      </li>
                      @endif
                      @if(App\Models\Socialsetting::find(1)->g_check == 1)
                      <li>
                        <a href="{{ route('social-provider','google') }}">
                          <i class="fab fa-google-plus-g"></i>
                        </a>
                      </li>
                      @endif
                    </ul>
                  </div>
                  @endif
                </form>
              </div>
            </div>
          </div>
          <div class="tab-pane fade" id="nav-reg" role="tabpanel" aria-labelledby="nav-reg-tab">
            <div class="login-area signup-area">
              <div class="header-area">
                <h4 class="title">{{ $langg->lang181 }}</h4>
              </div>
              <div class="login-form signup-form">
                @include('includes.admin.form-login')
                <div class="alert" id="alertc">
          <p class="alert-success" id="alert">

          </p>
        </div>
                <form class="mregisterform" action="{{route('user-register-submit')}}" method="POST">
                  {{ csrf_field() }}

                  <div class="form-input">
                    <input type="text" class="User Name" name="name" placeholder="{{ $langg->lang182 }}" required="">
                    <i class="icofont-user-alt-5"></i>
                  </div>

                  <div class="form-input">
                    <input type="text" class="User Name" name="phone" placeholder="{{ $langg->lang184 }}" required="" id="mobiles">
                    <i class="icofont-phone"></i>
                  </div>
                  <div class="help-block with-errors mb-1" >
                      <p id="emailError" class="help-block with-errors"></p>
                  </div>


                  <div class="form-input">
                    <input type="text" class="User Name" name="address" placeholder="{{ $langg->lang185 }}" required="">
                    <i class="icofont-location-pin"></i>
                  </div>

                  <div class="form-input">
                    <input type="password" class="Password" name="password" placeholder="{{ $langg->lang186 }}"
                      required="">
                    <i class="icofont-ui-password"></i>
                  </div>

                  <div class="form-input">
                    <input type="password" class="Password" name="password_confirmation"
                      placeholder="{{ $langg->lang187 }}" required="">
                    <i class="icofont-ui-password"></i>
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

                  @if($gs->is_capcha == 1)

                  <ul class="captcha-area">
                    <li>
                      <p><img class="codeimg1" src="{{asset("assets/images/capcha_code.png")}}" alt=""> <i
                          class="fas fa-sync-alt pointer refresh_code "></i></p>
                    </li>
                  </ul>

                  <div class="form-input">
                    <input type="text" class="Password" name="codes" placeholder="{{ $langg->lang51 }}" required="">
                    <i class="icofont-refresh"></i>
                  </div>

                  @endif

                  <input class="mprocessdata" type="hidden" value="{{ $langg->lang188 }}">
                  <div class="form-forgot-pass">
                    <div class="left">
                      <input type="checkbox" name="terms"  checked="checked">
    			<label for="mrp">I agree with <a href="https://amraibest.com/terms" target="_blank"> Terms & Policy</a></label>
                    </div>
                    
                  </div>
                  <button type="button" class="submit-btn sent-otp" onclick="sendOtp()">{{ $langg->lang189 }}</button>

                  <button type="submit" class="submit-btn otp">Verify Account</button>

                </form>
              </div>
            </div>
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
                url:"{{route('user.otp.send')}}",
                type:'post',
                data: {'mobile': $('#mobiles').val()},
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

