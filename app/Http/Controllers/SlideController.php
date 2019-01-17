<?php

namespace App\Http\Controllers;

use App\Models\Slide;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlideController extends Controller
{
	/**
	 * @var string
	 */
	private $sPathFolder;

	/**
	 * SlideController constructor.
	 */
	public function __construct()
	{
		$this->sPathFolder = config('app.path_folders')['slides'];
	}

	/**
	 * List next enabled slides.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list()
	{
		$oSlide = new Slide();

		return view('admin.slide')->with([
			'aEnabledSlides' => $oSlide->getEnabled()->each(function($poSlide) {
				$poSlide->fullPath = imageFullPath('slides', $poSlide->image);
			})
		]);
	}

	/**
	 * Delete logically (disable) an enabled slide and sort rest of enabled slides.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function delete(Request $request, $id)
	{
		if ($request->ajax()) {
			try {
				$oSlide = Slide::find($id);

				if ((bool) $oSlide->enable) {
					try {
						// Update slides by transaction
						DB::beginTransaction();

						// Delete logically (disable) slide
						Slide::whereId($id)->update([
							'order' => null,
							'enable' => 0,
							'updated_at' => date('Y-m-d H:i:s')
						]);

						// Update rest of slides order
						$oSlide = new Slide();

						foreach ($oSlide->getEnabled() as $iKey=>$aSlide)
							Slide::whereId($aSlide->id)->update([
								'order' => $iKey + 1,
								'updated_at' => date('Y-m-d H:i:s')
							]);

						DB::commit();

						$aResponse = [
							'status' => 'success',
							'message' => __('The slide has been deleted successfully.')
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
					'message' => __('The slide could not be deleted. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}

	/**
	 * Show slide creation form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function create()
	{
		return view('admin.slide-create');
	}

	/**
	 * Store a new slide.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function store(Request $request)
	{
		// Validate request
		$this->validateSlide($request, __METHOD__);

		// Obtain file name
		$sFileName = $this->getImageNameToSave();

		// Save file into
		$request->image->storeAs($this->sPathFolder, $sFileName);

		$oSlide = new Slide();

		// Build new slide data
		$aData = [
			'title' => $request->input('title'),
			'image' => $sFileName,
			'order' => $oSlide->getAmountEnabled() + 1
		];

		// Create and save slide
		$oSlide = new Slide($aData);
		$oSlide->save();

		Flash()->success(__('The slide has been created successfully.'))->important();

		return redirect('/admin/slides');
	}

	/**
	 * Show slide edition form.
	 *
	 * @param integer $id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
	 */
	public function edit($id)
	{
		$oSlide = Slide::find($id);

		// Validate slide status
		if (!(bool) $oSlide || !(bool) $oSlide->enable) {
			Flash()->error(__('The slide is not valid or has been deleted.'))->important();

			return redirect('/admin/slides');
		}

		// Build full path field data
		$oSlide->fullPath = $this->getImageFullPath($oSlide->image);

		return view('admin.slide-edit')->with([
			'aSlide' => $oSlide->toArray()
		]);
	}

	/**
	 * Update the give slide.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		$oSlide = Slide::find($id);

		// Validate slide status
		if (!(bool) $oSlide || !(bool) $oSlide->enable) {
			Flash()->error(__('The slide is not valid or has been deleted.'))->important();

			return redirect('/admin/slides');
		}

		// Validate request
		$this->validateSlide($request, __METHOD__);

		// Build new slide data
		$aData = [
			'title' => $request->input('title'),
			'updated_at' => date('Y-m-d H:i:s')
		];

		// Save image if correspond
		if (!empty($request->image)) {
			// Obtain file name
			$sFileName = $this->getImageNameToSave();

			// Save file into
			$request->image->storeAs($this->sPathFolder, $sFileName);

			$aData['image'] = $sFileName;
		}

		// Update slide
		Slide::whereId($id)->update($aData);

		Flash()->success(__('The slide has been edited successfully.'))->important();

		return redirect('/admin/slides');
	}

	/**
	 * Switch order between two enabled slides.
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function sort(Request $request)
	{
		if ($request->ajax()) {
			try {
				// Only allow to change order between two slides
				if (count($request->input('slides')) != 2)
					throw new \Exception();

				$oSlide = new Slide();

				// Obtain amount of enabled slides
				$iEnabledSlides = $oSlide->getAmountEnabled();

				foreach ($request->input('slides') as $aSlide) {
					// Obtain slide by id
					$oSlide = Slide::find($aSlide['id']);

					// Validate if slide exists
					if (!$oSlide || !(bool) $oSlide->enable)
						throw new \Exception();

					// Validate new slide order
					if ($oSlide->order==$aSlide['order'] || $aSlide['order']<1 || $aSlide['order']>$iEnabledSlides)
						throw new \Exception();
				}

				try {
					// Update slides by transaction
					DB::beginTransaction();

					foreach ($request->input('slides') as $aSlide)
						Slide::whereId($aSlide['id'])->update([
							'order' => $aSlide['order'],
							'updated_at' => date('Y-m-d H:i:s')
						]);

					DB::commit();

					$aResponse = [
						'status' => 'success',
						'message' => __('The slides has been ordered successfully.')
					];
				} catch (\Exception $oException) {
					DB::rollBack();

					throw new \Exception();
				}
			} catch (\Exception $oException) {
				$aResponse = [
					'status' => 'error',
					'message' => __('The slides could not be ordered. Please, try again.')
				];
			}

			return response()->json($aResponse);
		}
	}

	/**
	 * Validate request.
	 *
	 * @param Request $poRequest
	 * @param string $psAction
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function validateSlide(&$poRequest, $psAction='store')
	{
		$sRequired = ($psAction == 'store') ? 'required' : 'sometimes';

		$this->validate(
			$poRequest,
			[
				'title' => 'required',
				'image' => $sRequired.'|image|mimes:jpeg,jpg,png|max:2048'
			],
			[],
			[
				'title' => strtolower(__('Title')),
				'image' => strtolower(__('Image'))
			]
		);
	}

	/**
	 * Build image name to save in file system and database.
	 *
	 * @return string
	 */
	public function getImageNameToSave()
	{
		return time().'.'.request()->image->getClientOriginalExtension();
	}
}
