<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Order\OrderData;

class VendorController extends Controller
{
    public function find_vendor(Request $request) {
        $search = $request -> all();
        $data['vendors'] = Vendor::search_vendor($search);
        return view('Vendor.search-vendor', $data);
    }

    public function recent_order_from_vendor(Request $request) {
        $data['orders'] = OrderData::recent_vendor_order($request->vendor_idx);

        return view('Vendor.include.vendor-orders', $data);
    }
}
