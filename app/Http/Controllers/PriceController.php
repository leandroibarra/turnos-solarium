<?php

namespace App\Http\Controllers;

use App\Models\Price;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
	/**
	 * List next enabled prices.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list()
	{
		$oPrice = new Price();

		return view('admin.price')->with([
			'aEnabledPrices' => $oPrice->getEnabled()
		]);
	}

	/**
	 * Delete logically (disable) an enabled price and sort rest of enabled prices.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete(Request $request, $id)
	{
		if ($request->ajax()) {
			try {
				$oPrice = Price::find($id);

				if ((bool) $oPrice->enable) {
					try {
						// Update prices by transaction
						DB::beginTransaction();

						// Delete logically (disable) price
						Price::whereId($id)->update([
							'order' => null,
							'enable' => 0,
							'updated_at' => date('Y-m-d H:i:s')
						]);

						// Update rest of prices order
						$oPrice = new Price();

						foreach ($oPrice->getEnabled() as $iKey=>$aPrice)
							Price::whereId($aPrice->id)->update([
								'order' => $iKey + 1,
								'updated_at' => date('Y-m-d H:i:s')
							]);

						DB::commit();

						$aResponse = [
							'status' => 'success',
							'message' => __('The price has been deleted successfully.')
						];
					} catch (\Exception $oException) {
						DB::rollBack();

						throw new \Exception();
					}
				} else {
					throw new \Exception();
				}
			} catch (\Exception $oException) {
				$aResponse = [
					'status' => 'error',
					'message' => __('The price could not be deleted. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}

	public function create()
	{

	}

	public function store(Request $request)
	{

	}

	public function edit($id)
	{

	}

	public function update(Request $request, $id)
	{

	}

	/**
	 * Switch order between two enabled prices.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sort(Request $request)
	{
		if ($request->ajax()) {
			try {
				// Only allow to change order between two prices
				if (count($request->input('prices')) != 2)
					throw new \Exception();

				$oPrice = new Price();

				// Obtain amount of enabled prices
				$iEnabledPrices = $oPrice->getAmountEnabled();

				foreach ($request->input('prices') as $aPrice) {
					// Obtain price by id
					$oPrice = Price::find($aPrice['id']);

					// Validate if price exists
					if (!$oPrice || !(bool) $oPrice->enable)
						throw new \Exception();

					// Validate new price order
					if ($oPrice->order==$aPrice['order'] || $aPrice['order']<1 || $aPrice['order']>$iEnabledPrices)
						throw new \Exception();
				}

				try {
					// Update prices by transaction
					DB::beginTransaction();

					foreach ($request->input('prices') as $aPrice)
						Price::whereId($aPrice['id'])->update([
							'order' => $aPrice['order'],
							'updated_at' => date('Y-m-d H:i:s')
						]);

					DB::commit();

					$aResponse = [
						'status' => 'success',
						'message' => __('The prices has been ordered successfully.')
					];
				} catch (\Exception $oException) {
					DB::rollBack();

					throw new \Exception();
				}
			} catch (\Exception $oException) {
				$aResponse = [
					'status' => 'error',
					'message' => __('The prices could not be ordered. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}

	/**
	 * Validate request.
	 *
	 * @param Request $poRequest
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validatePrice(&$poRequest)
	{

	}
}
