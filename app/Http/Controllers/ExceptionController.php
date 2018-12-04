<?php

namespace App\Http\Controllers;

use App\Exception;
use Illuminate\Http\Request;

class ExceptionController extends Controller
{
	public function list() {
		$oException = new Exception();

		return view('admin.exception')->with([
			'aEnabledExceptions' => $oException->getNextEnabled()
		]);
	}

	public function delete(Request $request, $id) {
		if ($request->ajax()) {
			$aException = Exception::find($id);

			if ((bool) $aException->enable) {
				Exception::whereId($id)->update([
					'enable' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				]);

				$aResponse = [
					'status' => 'success',
					'message' => __('The exception has been deleted successfully.')
				];
			} else {
				$aResponse = [
					'status' => 'error',
					'message' => __('The exception could not be deleted. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}
}
