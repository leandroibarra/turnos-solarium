<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Branch_Working_Week;
use App\Models\SystemParameter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
	/**
	 * @var array
	 */
	private $aSystemParameters;

	/**
	 * BranchController constructor.
	 */
	public function __construct()
	{
		$this->aSystemParameters = SystemParameter::find(1)->toArray();
	}

	/**
	 * Check if there is a branch in request attributes and redirects to corresponds pages.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function index(Request $request)
	{
		if (!(bool) current($request->attributes)) {
			$oBranch = new Branch();

			return view('web.branch')->with([
				'aEnabledBranches' => $oBranch->getEnabled()
			]);
		} else {
			return redirect('/book');
		}
	}

	/**
	 * Check if there is a branch in request attributes and redirects to corresponds pages.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function showSelectBranch(Request $request)
	{
		if (!(bool) current($request->attributes)) {
			$oBranch = new Branch();

			return view('admin.branch-select')->with([
				'aEnabledBranches' => $oBranch->getEnabled()
			]);
		} else {
			return redirect('/admin/appointments');
		}
	}

	/**
	 * Put branch id request variable into branch id session variable.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function set(Request $request)
	{
		Session::put('branch_id', $request->input('branch_id'));

		return redirect('/book');
	}

	/**
	 * Put branch id request variable into branch id session variable.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function select(Request $request)
	{
		Session::put('branch_id', $request->input('branch_id'));

		return redirect('/admin/appointments');
	}

	/**
	 * List enabled branches.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list()
	{
		$oBranch = new Branch();

		return view('admin.branch')->with([
			'aEnabledBranches' => $oBranch->getEnabled()
		]);
	}

	/**
	 * Delete logically (disable) an enabled branch and validates there is at least one enabled branch still.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete(Request $request, $id)
	{
		if ($request->ajax()) {
			$oBranch = Branch::find($id);

			if ((bool) $oBranch->enable) {
				if ($oBranch->getAmountEnabled() > 1) {
					// Delete logically (disable) slide
					Branch::whereId($id)->update([
						'enable' => 0,
						'updated_at' => date('Y-m-d H:i:s')
					]);

					$aResponse = [
						'status' => 'success',
						'message' => __('The branch has been deleted successfully.')
					];
				} else {
					$aResponse = [
						'status' => 'error',
						'message' => __('The branch could not be deleted because at least one enabled branch is required.')
					];
				}
			} else {
				$aResponse = [
					'status' => 'error',
					'message' => __('The branch already has been deleted. Please, update your page.')
				];
			}

			return response()->json($aResponse);
		}
	}

	/**
	 * Show branch creation form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		return view('admin.branch-create')->with([
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes']
		]);
	}

	/**
	 * Store a new branch.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function store(Request $request)
	{
		try {
			// Validate request
			$this->validateBranch($request);

			// Create branch and working week by transaction
			DB::beginTransaction();

			// Create and save branch
			$oBranch = Branch::create([
				'name' => $request->input('name'),
				'address' => $request->input('address'),
				'city' => $request->input('city'),
				'province' => $request->input('province'),
				'country' => $request->input('country'),
				'country_code' => $request->input('country_code'),
				'amount_appointments_by_time' => $request->input('amount_appointments_by_time')
			]);

			// Save working week
			for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
				Branch_Working_Week::create([
					'branch_id' => $oBranch->id,
					'day_number' => $iWeekDay,
					'is_working_day' => ((bool) $request->input("is_working_day.{$iWeekDay}")) ? 1 : 0,
					'from' => $request->input("from.{$iWeekDay}"),
					'until' => $request->input("until.{$iWeekDay}")
				]);

			DB::commit();

			Flash()->success(__('The branch has been created successfully.'))->important();

			return redirect('/admin/branches');
		} catch (\Illuminate\Validation\ValidationException $oException) {
			DB::rollBack();

			// Throw exception from request validation
			throw $oException;
		} catch (\Exception $oException) {
			DB::rollBack();

			throw $oException;
		}
	}

	/**
	 * Show branch edition form.
	 *
	 * @param integer $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function edit($id)
	{
		$oBranch = Branch::find($id);

		// Validate slide status
		if (!(bool) $oBranch || !(bool) $oBranch->enable) {
			Flash()->error(__('The branch is not valid or has been deleted.'))->important();

			return redirect('/admin/branches');
		}

		return view('admin.branch-edit')->with([
			'oBranch' => $oBranch,
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes']
		]);
	}

	/**
	 * Update the give branch.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		$oBranch = Branch::find($id);

		// Validate slide status
		if (!(bool) $oBranch || !(bool) $oBranch->enable) {
			Flash()->error(__('The branch is not valid or has been deleted.'))->important();

			return redirect('/admin/branches');
		}

		try {
			// Validate request
			$this->validateBranch($request);

			// Create branch and working week by transaction
			DB::beginTransaction();

			// Update branch
			Branch::whereId($id)->update([
				'name' => $request->input('name'),
				'address' => $request->input('address'),
				'city' => $request->input('city'),
				'province' => $request->input('province'),
				'country' => $request->input('country'),
				'country_code' => $request->input('country_code'),
				'amount_appointments_by_time' => $request->input('amount_appointments_by_time'),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			// Update working week
			for ($iWeekDay=0; $iWeekDay<7; $iWeekDay++)
				Branch_Working_Week::where([
					'branch_id' => $id,
					'day_number' => $iWeekDay
				])->update([
					'is_working_day' => ((bool) $request->input("is_working_day.{$iWeekDay}")) ? 1 : 0,
					'from' => $request->input("from.{$iWeekDay}"),
					'until' => $request->input("until.{$iWeekDay}"),
					'updated_at' => date('Y-m-d H:i:s')
				]);

			DB::commit();

			Flash()->success(__('The branch has been edited successfully.'))->important();

			return redirect('/admin/branches');
		} catch (\Illuminate\Validation\ValidationException $oException) {
			DB::rollBack();

			// Throw exception from request validation
			throw $oException;
		} catch (\Exception $oException) {
			DB::rollBack();

			throw $oException;
		}
	}

	/**
	 * Validate request.
	 *
	 * @param Request $poRequest
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validateBranch(&$poRequest)
	{
		$this->validate(
			$poRequest,
			[
				'name' => 'required',
				'address' => 'required',
				'amount_appointments_by_time' => 'required|numeric|min:1',
				'city' => 'required',
				'province' => 'required',
				'country' => 'required',
				'country_code' => [
					'required',
					function ($attribute, $value, $fail) {
						if (strtoupper($value) !== $value)
							$fail(__('The :attribute field must be uppercase.'));
					}
				],
				'from.*' => 'nullable|required_with:is_working_day.*,1|date_format:H:i|before:until.*',
				'until.*' => 'nullable|required_with:is_working_day.*,1|date_format:H:i|after:from.*'
			],
			[],
			[
				'name' => strtolower(__('Name')),
				'address' => strtolower(__('Address')),
				'amount_appointments_by_time' => strtolower(__('Appointments by Time')),
				'city' => strtolower(__('City')),
				'province' => strtolower(__('Province')),
				'country' => strtolower(__('Country')),
				'country_code' => strtolower(__('Country Code')),
				'from.*' => strtolower(__('From')),
				'until.*' => strtolower(__('Until')),
			]
		);
	}
}
