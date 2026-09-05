<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Whitepaper;
use App\Models\WhitepaperTranslation;

class WhitepaperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $whitepapers = Whitepaper::orderBy('sort', 'asc')->orderBy('id', 'desc')->paginate(15);
        return view('backend.whitepapers.index', compact('whitepapers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.whitepapers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        if (empty($slug)) {
            flash(translate('Slug is required'))->warning();
            return back();
        }
        if (Whitepaper::where('slug', $slug)->first() != null) {
            flash(translate('Slug has been used already'))->warning();
            return back();
        }

        $whitepaper = new Whitepaper;
        $whitepaper->slug = $slug;
        $whitepaper->cover_image = $request->cover_image;
        $whitepaper->status = $request->status ? 1 : 0;
        $whitepaper->sort = $request->sort ?: 0;
        $whitepaper->views = 0;
        $whitepaper->save();

        $whitepaper_translation = WhitepaperTranslation::firstOrNew(['lang' => $request->lang, 'whitepaper_id' => $whitepaper->id]);
        $whitepaper_translation->title = $request->title;
        $whitepaper_translation->summary = $request->summary;
        $whitepaper_translation->content = $request->content;
        $whitepaper_translation->pdf = $request->pdf;
        $whitepaper_translation->save();

        flash(translate('Whitepaper has been created successfully'))->success();
        return redirect()->route('whitepapers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $whitepaper = Whitepaper::findOrFail($id);
        return view('backend.whitepapers.edit', compact('whitepaper', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $whitepaper = Whitepaper::findOrFail($id);

        if (!empty($request->slug)) {
            $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
            if (Whitepaper::where('id', '!=', $id)->where('slug', $slug)->first() != null) {
                flash(translate('Slug has been used already'))->warning();
                return back();
            }
            $whitepaper->slug = $slug;
        }

        $whitepaper->cover_image = $request->cover_image;
        $whitepaper->status = $request->status ? 1 : 0;
        $whitepaper->sort = $request->sort ?: 0;
        $whitepaper->save();

        $whitepaper_translation = WhitepaperTranslation::firstOrNew(['lang' => $request->lang, 'whitepaper_id' => $whitepaper->id]);
        $whitepaper_translation->title = $request->title;
        $whitepaper_translation->summary = $request->summary;
        $whitepaper_translation->content = $request->content;
        $whitepaper_translation->pdf = $request->pdf;
        $whitepaper_translation->save();

        flash(translate('Whitepaper has been updated successfully'))->success();
        return redirect()->route('whitepapers.index');
    }

    /**
     * Update the status of the specified resource.
     */
    public function update_status(Request $request)
    {
        $whitepaper = Whitepaper::findOrFail($request->id);
        $whitepaper->status = $request->status;
        $whitepaper->save();
        return 1;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $whitepaper = Whitepaper::findOrFail($id);
        foreach ($whitepaper->whitepaper_translations as $key => $whitepaper_translation) {
            $whitepaper_translation->delete();
        }
        $whitepaper->delete();
        flash(translate('Whitepaper has been deleted successfully'))->success();
        return redirect()->back();
    }

    /**
     * Display whitepaper list page on frontend.
     */
    public function all_whitepapers()
    {
        $whitepapers = Whitepaper::where('status', 1)->orderBy('sort', 'asc')->orderBy('id', 'desc')->paginate(12);
        return view('frontend.whitepapers.index', compact('whitepapers'));
    }

    /**
     * Display whitepaper detail page on frontend.
     */
    public function show_whitepaper($slug)
    {
        $whitepaper = Whitepaper::where('slug', $slug)->where('status', 1)->first();
        if ($whitepaper != null) {
            $whitepaper->views += 1;
            $whitepaper->save();
            return view('frontend.whitepapers.show', compact('whitepaper'));
        }
        abort(404);
    }

    /**
     * Download whitepaper PDF.
     */
    public function download_whitepaper($slug)
    {
        $whitepaper = Whitepaper::where('slug', $slug)->where('status', 1)->first();
        if ($whitepaper != null) {
            $pdf = $whitepaper->getTranslation('pdf');
            if (!empty($pdf)) {
                return redirect(uploaded_asset($pdf));
            }
            flash(translate('PDF file is not available'))->error();
            return back();
        }
        abort(404);
    }
}
