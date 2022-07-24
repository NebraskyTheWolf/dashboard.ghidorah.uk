<x-guest-layout>
   <section class="login-content">
      <div class="row m-0 align-items-center bg-white vh-100">
         <div class="col-md-6">
            <div class="row justify-content-center">
               <div class="col-md-10">
                  <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                     <div class="card-body">
                        <a href="{{route('dashboard')}}" class="navbar-brand d-flex align-items-center mb-3">
                           <svg width="30" class="text-primary" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor"/>
                              <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor"/>
                              <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor"/>
                              <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor"/>
                           </svg>
                           <h4 class="logo-title ms-3">{{env('APP_NAME')}}</h4>
                        </a>
                        <h2 class="mb-2 text-center">Sign In</h2>
                        <p class="text-center">Login to stay connected.</p>
                        <x-auth-session-status class="mb-4" :status="session('status')" />
						 
						 <div class="alert alert-info mb-0" role="alert">
							<h4 class="alert-heading">Beta testing mode</h4>
							<p>The dashboard is still in developement.</p>
							<hr>
							<p class="mb-0">Some bugs can happens if ever you found a bugs please let us know on our <a href="https://discord.gg/Ew8DET4Ruz">Discord</a>.</p>
						 </div>
						 </br>

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST" action="{{ route('login') }}" data-toggle="validator">
                            {{csrf_field()}}
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="code" class="form-label">Code</label>
                                    <input id="code" type="text" name="code"  class="form-control"  placeholder="00000000-0000-0000-0000-000000000000" required autofocus>
                                 </div>
                              </div>
                           </div>
                           <div class="d-flex justify-content-center">
                              <button type="submit" class="btn btn-primary">{{ __('Sign In') }}</button>
                           </div>
                           <p class="mt-3 text-center">
                              Don’t have an account? <a href="" class="text-underline">Create one by using /accountlink on the your server!</a>
                           </p>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</x-guest-layout>
