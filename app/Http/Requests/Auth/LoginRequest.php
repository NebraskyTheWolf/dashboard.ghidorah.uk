<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
		$maintenance = resolve('\ApiObsifight')->get('/')->body['maintenance'];
		if ($maintenance === "true") {
			 throw ValidationException::withMessages([
                'code' => 'Maintenance mode enabled, Unable to login.',
             ]);
		}
		
		$account = resolve('\ApiObsifight')->get('/user/by-token/' . $this->input('code'));
		if ($account->success) {
			if ($account->body['activated']) {
				throw ValidationException::withMessages([
					'code' => 'Code already used. please use /accountlink to relogin.'
				]);
			} else {
				$blacklist = resolve('\ApiObsifight')->get('/users/isBlacklisted/' . $account->body['id']);
				if ($blacklist->success) {
					if ($blacklist->body !== NULL && $blacklist->body['data']['active']) {
						throw ValidationException::withMessages([
							'code' => 'You are blacklisted.'
						]);
					}
				} else {
					$email = $account->body['id'] . '@ghidorah.uk';
					
					$user = User::where('email', $email)->first();
					User::where('email', $email)->update(['server_id' => '' . $account->body['serverId']]);
					
					if (Auth::loginUsingId($user->id, false)) {
						$activate = resolve('\ApiObsifight')->get('/user/activate/' . $account->body['id']);
						if ($activate->success) {
							// ACTIVATED. NO PROCESSING NEEDED.
						} else {
							throw ValidationException::withMessages([
								'code' => 'Activation failed.'
							]);
						}
					} else {
						Auth::login($user = User::create([
							'username' => strtolower($account->body['user']['username']),
							'discord_id' => $account->body['id'] . '',
							'server_id' => $account->body['serverId'] . '',
							'first_name' => "0",
							'last_name' => "0",
							'phone_number' => "0",
							'email' => $account->body['id'] . '@ghidorah.uk',
							'password' => Hash::make($account->body['id']),
							'user_type' => 'admin'
						]));
					}
				}
			}
		} else {
			throw ValidationException::withMessages([
				'code' => 'Account not found.'
			]);
		}
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'code' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
}
