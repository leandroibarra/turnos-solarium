<?php

namespace App\Http\Controllers;

use App\Models\Price;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
	/**
	 * @var string
	 */
	private $sDecimalPointSeparator;

	/**
	 * @var string
	 */
	private $sThousandsSeparator;

	/**
	 * PriceController constructor.
	 */
	public function __construct()
	{
		$this->sDecimalPointSeparator = config('app.decimal_point_separator');

		$this->sThousandsSeparator = config('app.thousands_separator');
	}

	/**
	 * List next enabled prices.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list(Request $request)
	{
		$oPrice = new Price();

		return view('admin.price')->with([
			'aEnabledPrices' => $oPrice->getEnabled(current($request->attributes)['oBranch']->id)->each(function($poPrice) {
				$poPrice->price = formatPrice($poPrice->price);
			})
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
				// Validate if price is valid and is enable
				$this->validatePriceIdAndEnable(current($request->attributes)['oBranch']->id, $id, true);

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

					foreach ($oPrice->getEnabled(current($request->attributes)['oBranch']->id) as $iKey=>$aPrice)
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
			} catch (\Exception $oException) {
				$aResponse = [
					'status' => 'error',
					'message' => $oException->getMessage()
				];
			}

			return response()->json($aResponse);
		}
	}

	/**
	 * Show price creation form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		return view('admin.price-create')->with([
			'sDecimalPointSeparator' => $this->sDecimalPointSeparator,
			'sThousandsSeparator' => $this->sThousandsSeparator
		]);
	}

	/**
	 * Store a new price.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function store(Request $request)
	{
		// Validate request
		$this->validatePrice($request);

		$oPrice = new Price();

		// Build new price data
		$aData = [
			'branch_id' => current($request->attributes)['oBranch']->id,
			'title' => $request->input('title'),
			'description' => $request->input('description'),
			'price' => $this->formatPriceToSave($request->input('price')),
			'order' => $oPrice->getAmountEnabled(current($request->attributes)['oBranch']->id) + 1
		];

		// Create and save price
		$oPrice = new Price($aData);
		$oPrice->save();

		Flash()->success(__('The price has been created successfully.'))->important();

		return redirect('/admin/prices');
	}

	/**
	 * Show price edition form.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function edit(Request $request, $id)
	{
		try {
			// Validate if price is valid and is enable
			$aPrice = $this->validatePriceIdAndEnable(current($request->attributes)['oBranch']->id, $id);

			// Format price field data
			$aPrice['price'] = formatPrice($aPrice['price']);

			return view('admin.price-edit')->with([
				'sDecimalPointSeparator' => $this->sDecimalPointSeparator,
				'sThousandsSeparator' => $this->sThousandsSeparator,
				'aPrice' => $aPrice
			]);
		} catch (\Exception $oException) {
			Flash()->error($oException->getMessage())->important();

			return redirect('/admin/prices');
		}
	}

	/**
	 * Update the give price.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		try {
			// Validate if price is valid and is enable
			$this->validatePriceIdAndEnable(current($request->attributes)['oBranch']->id, $id);

			// Validate request
			$this->validatePrice($request);

			// Update price
			Price::whereId($id)->update([
				'title' => $request->input('title'),
				'description' => $request->input('description'),
				'price' => $this->formatPriceToSave($request->input('price')),
				'updated_at' => date('Y-m-d H:i:s')
			]);

			Flash()->success(__('The price has been edited successfully.'))->important();
		} catch (\Illuminate\Validation\ValidationException $oException) {
			// Throw exception from request validation
			throw $oException;
		} catch (\Exception $oException) {
			Flash()->error($oException->getMessage())->important();
		}

		return redirect('/admin/prices');
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
				$iEnabledPrices = $oPrice->getAmountEnabled(current($request->attributes)['oBranch']->id);

				foreach ($request->input('prices') as $aPrice) {
					// Obtain price by id
					$oPrice = Price::where([
						'branch_id' => current($request->attributes)['oBranch']->id,
						'id' => $aPrice['id']
					])->first();

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
		$this->validate(
			$poRequest,
			[
				'title' => 'required',
				'price' => [
					'required',
					function ($attribute, $value, $fail) {
						if (preg_match('/^\d+('.$this->sThousandsSeparator.'\d{3})*(\\'.$this->sDecimalPointSeparator.'\d{1,2})?$/', $value) != 1)
							$fail(__('The :attribute field is not valid.'));
					}
				],
				'description' => 'required'
			],
			[],
			[
				'title' => strtolower(__('Title')),
				'price' => strtolower(__('Price')),
				'description' => strtolower(__('Description'))
			]
		);
	}

	/**
	 * Validate if price is valid and is enable.
	 *
	 * @param integer $piBranchId
	 * @param integer $piPriceId
	 * @param boolean $pbIsAjax OPTIONAL
	 * @return array
	 * @throws \Exception
	 */
	public function validatePriceIdAndEnable($piBranchId, $piPriceId, $pbIsAjax=false)
	{
		$oPrice = Price::where([
			'branch_id' => $piBranchId,
			'id' => $piPriceId
		])->first();

		if (!(bool) $oPrice)
			throw new \Exception(__('Parameters are not valid'));

		if (!(bool) $oPrice->enable)
			throw new \Exception(__(($pbIsAjax) ? 'The price already has been deleted. Please, update your page.' : 'This price already has been deleted.'));

		return $oPrice->toArray();
	}

	/**
	 * Format price to save in database.
	 *
	 * @param string $psPrice
	 * @return mixed
	 */
	public function formatPriceToSave($psPrice)
	{
		return str_replace(
			[$this->sThousandsSeparator, $this->sDecimalPointSeparator],
			['', '.'],
			$psPrice
		);
	}
}
