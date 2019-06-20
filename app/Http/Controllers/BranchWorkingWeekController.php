<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Branch_Working_Week;
use App\Models\SystemParameter;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchWorkingWeekController extends Controller
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
	 * Show branch working week edition form.
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

		return view('admin.branch-working-week-edit')->with([
			'oBranch' => $oBranch,
			'iAppointmentMinutes' => $this->aSystemParameters['appointment_minutes']
		]);
	}

	/**
	 * Update the give branch working week.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		$oBranch = Branch::find($id);

		// Validate branch status
		if (!(bool) $oBranch || !(bool) $oBranch->enable) {
			Flash()->error(__('The branch is not valid or has been deleted.'))->important();

			return redirect('/admin/branches');
		}

		try {
			// Validate request
			$this->validateBranchWorkingWeek($request);

			// Update branch working week by transaction
			DB::beginTransaction();

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

			Flash()->success(__('The branch schedule has been edited successfully.'))->important();

			return redirect('/admin/schedule/'.$oBranch->id.'/edit');
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
	 * @param $poRequest
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validateBranchWorkingWeek(&$poRequest)
	{
		$this->validate(
			$poRequest,
			[
				'from.*' => 'nullable|required_with:is_working_day.*,1|date_format:H:i|before:until.*',
				'until.*' => 'nullable|required_with:is_working_day.*,1|date_format:H:i|after:from.*'
			],
			[],
			[
				'from.*' => strtolower(__('From')),
				'until.*' => strtolower(__('Until')),
			]
		);
	}
}
