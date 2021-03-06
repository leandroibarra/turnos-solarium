<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AdminController extends Controller
{
	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = 'admin/select-branch';

	/**
	 * Show the administration's login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showLoginForm()
	{
		if (Auth::user())
			return redirect('admin/appointments');

		return view('admin.login');
	}

	/**
	 * Handle an authentication attempt.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return Response
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function login(Request $request)
	{
		$this->validateLogin($request);

		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		if ($this->hasTooManyLoginAttempts($request)) {
			$this->fireLockoutEvent($request);
			return $this->sendLockoutResponse($request);
		}

		// This section is the only change
		if ($this->guard()->validate($this->credentials($request))) {
			$user = $this->guard()->getLastAttempted();

			// Make sure the user has roles
			if (($user->hasRole(['Sysadmin', 'Admin']) || ($user->hasRole('Employee') && $user->branch_id > 0)) && $this->attemptLogin($request)) {
				// Auto assign branch id session variable and change redirection page when user is Employee
				if ($user->hasRole('Employee') && $user->branch_id > 0) {
					Session::put('branch_id', $user->branch_id);

					$this->redirectTo = 'admin/appointments';
				}

				// Send the normal successful login response
				return redirect($this->redirectTo);
			} else {
				// Increment the failed login attempts and redirect back to the
				// login form with an error message.
				$this->incrementLoginAttempts($request);

				session()->invalidate();

				Flash()->error(__('You do not have permission to access this section'))->important();

				return redirect()
					->back()
					->withInput($request->only($this->username(), 'remember'));
			}
		}

		// If the login attempt was unsuccessful we will increment the number of attempts
		// to login and redirect the user back to the login form. Of course, when this
		// user surpasses their maximum number of attempts they will get locked out.
		$this->incrementLoginAttempts($request);

		return $this->sendFailedLoginResponse($request);
	}

	/**
	 * Validate the user login request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 *
	 * @throws \Illuminate\Validation\ValidationException
	 */
	protected function validateLogin(Request $request)
	{
		$request->validate(
			[
				$this->username() => 'required|string',
				'password' => 'required|string',
			],
			[],
			[
				$this->username() => strtolower(__($this->username())),
				'password' => strtolower(__('Password')),
			]
		);
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function logout(Request $request)
	{
		$request->session()->invalidate();

		return redirect('/admin/login');
	}
}
