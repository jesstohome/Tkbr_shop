<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use App\Models\ProductsImport;
use App\Models\ProductsExport;
use Illuminate\Support\Facades\Log;
use PDF;
use Excel;
use Auth;

class ProductBulkUploadController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'seller') {
            if(Auth::user()->shop->verification_status){
                return view('seller.product_bulk_upload.index');
            }
            else{
                flash('Your shop is not verified yet!')->warning();
                return back();
            }
        }
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.product.bulk_upload.index');
        }
    }

    public function export(){
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function pdf_download_category()
    {
        $categories = Category::all();

        return PDF::loadView('backend.downloads.category',[
            'categories' => $categories,
        ], [], [])->download('category.pdf');
    }

    public function pdf_download_brand()
    {
        $brands = Brand::all();

        return PDF::loadView('backend.downloads.brand',[
            'brands' => $brands,
        ], [], [])->download('brands.pdf');
    }

    public function pdf_download_seller()
    {
        $users = User::where('user_type','seller')->get();

        return PDF::loadView('backend.downloads.user',[
            'users' => $users,
        ], [], [])->download('user.pdf');

    }

    public function bulk_upload(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        
        if($request->hasFile('bulk_file')){
            try {
                $import = new ProductsImport;
                Excel::import($import, request()->file('bulk_file'));
            } catch (\Exception $exception) {
                Log::warning(var_export(['bulk_upload Failed:' . $exception->getMessage()], true));
            }
        }

        return back();
    }

}
