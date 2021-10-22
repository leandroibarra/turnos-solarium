<?php

namespace App\Http\Controllers;

use App\Models\SiteParameter;

use Illuminate\Http\Request;

class SiteParameterController extends Controller
{
	/**
	 * Show site parameters edition form.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit()
	{
		return view('admin.site-parameters')->with([
			'aSiteParameter' => SiteParameter::find(1)->toArray()
		]);
	}

	/**
	 * Update site parameters.
	 *
	 * @param Request $request
	 * @param integer $id
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 * @throws \Illuminate\Validation\ValidationException
	 */
	public function update(Request $request, $id)
	{
		// Validate request
		$this->validate(
			$request,
			[
				'pinterest_url' => 'sometimes|regex:/^http(s)?:\/\/(www\.)?pinterest\.com\/(?:([a-z0-9]+(\_[a-z0-9]*)*\/)?)$/i|nullable',
				'facebook_url' => 'sometimes|regex:/^http(s)?:\/\/(www\.)?facebook\.com(\/(?:#!\/)?(?:pages\/)?(?:([a-z0-9](\.[a-z0-9])*)*\/)*(([a-z0-9](\.[a-z0-9])*)*)+)?$/i|nullable',
				'twitter_url' => 'sometimes|regex:/^http(s)?:\/\/(www\.)?twitter\.com\/([a-z0-9_]+\/?)?$/i|nullable',
				'instagram_url' => 'sometimes|regex:/^http(s)?:\/\/(www\.)?instagram\.com\/([a-z0-9_]+\/?)?$/i|nullable',
				'store_url' => 'sometimes|url'
			],
			[],
			[
				'pinterest_url' => strtolower(__('Pinterest URL')),
				'facebook_url' => strtolower(__('Facebook URL')),
				'twitter_url' => strtolower(__('Twitter URL')),
				'instagram_url' => strtolower(__('Instagram URL')),
				'store_url' => strtolower(__('Store URL'))
			]
		);

		// Update site parameters
		SiteParameter::whereId($id)->update([
			'about_tanning_text' => htmlentities($request->input('about_tanning_text')),
			'pinterest_url' => $request->input('pinterest_url'),
			'facebook_url' => $request->input('facebook_url'),
			'twitter_url' => $request->input('twitter_url'),
			'instagram_url' => $request->input('instagram_url'),
			'store_url' => $request->input('store_url')
		]);

		Flash()->success(__('Parameters were saved successfully'))->important();

		return redirect('admin/site-parameters');
	}
}
