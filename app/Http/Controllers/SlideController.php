<?php

namespace App\Http\Controllers;

use App\Models\Slide;

use Illuminate\Http\Request;

class SlideController extends Controller
{
	/**
	 * List next enabled slides.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function list()
	{
		$oSlide = new Slide();

		return view('admin.slide')->with([
			'aEnabledSlides' => $oSlide->getEnabled()
		]);
	}

	public function delete(Request $request, $id)
	{

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

	public function sort(Request $request)
	{

	}

	public function validatePrice(&$poRequest)
	{

	}

	public function formatPriceToSave($psPrice)
	{

	}

}
