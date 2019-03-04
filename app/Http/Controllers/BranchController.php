<?php

namespace App\Http\Controllers;

use App\Models\Branch;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
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
		return view('admin.branch-create');
	}
}
