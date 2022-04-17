<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shops;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
    }
    public function search(Request $request)
    {
        $userId = $request->userId;
        if (User::where('id', $userId)->exists()) {
            $serachDetails = [];
            $latitude       =   $request->latitude; //     "28.418715";
            $longitude      =    $request->longitude; //    "77.0478997";

            $shops          =       Shops::select(
                "*",
                DB::raw("6371 * acos(cos(radians(" . $latitude . ")) 
            * cos(radians(shops.lat)) 
            * cos(radians(shops.long) - radians(" . $longitude . ")) 
            + sin(radians(" . $latitude . ")) 
            * sin(radians(shops.lat))) AS distance")
            );
            $shops          =       $shops->orderBy('distance', 'asc');
            $shops          =       $shops->get();
            if (!empty($shops)) {
                foreach ($shops as $shop) {
                    $shop_timings = $shop->shopFunctionalTimings;
                    $shop_break_timings = $shop->shopBreakTimings;
                    $shop_timings__ = [];
                    $shop_break_timings__ = [];
                    if (!empty($shop_timings)) {
                        foreach ($shop_timings as $shop_timing) {
                            $timing_array = [
                                'timeid' => ($shop_timing['id'] == null) ? "" : $shop_timing['id'],
                                'days' => ($shop_timing['days'] == null) ? "" : $shop_timing['days'],
                                'startTime' => ($shop_timing['startTime'] == null) ? "" : $shop_timing['startTime'],
                                'endTime' => ($shop_timing['endTime'] == null) ? "" : $shop_timing['endTime'],
                                'day_status' => ($shop_timing['status'] == 1) ? TRUE : FALSE,
                                'is_open' => ($shop_timing['is_open'] == 1) ? TRUE : FALSE,
                            ];
                            $shop_timings__[] = $timing_array;
                        }
                    }
                    if (!empty($shop_break_timings)) {
                        $shop_break_timings__ = [
                            'breakId' => ($shop_break_timings['id'] == null) ? "" : $shop_break_timings['id'],
                            'days' => ($shop_break_timings['days'] == null) ? "" : $shop_break_timings['days'],
                            'startTime' => ($shop_break_timings['startTime'] == null) ? "" : $shop_break_timings['startTime'],
                            'endTime' => ($shop_break_timings['endTime'] == null) ? "" : $shop_break_timings['endTime'],
                            'breakStatus' => ($shop_break_timings['breakStatus'] == 1) ? TRUE : FALSE,
                            'is_open' => ($shop_break_timings['is_open'] == 1) ? TRUE : FALSE,
                        ];
                    }
                    $ret = array(
                        'storeId' => ($shop->id == null) ? "" : $shop->id,
                        'name' => ($shop->name == null) ? "" : $shop->name,
                        'image' => ($shop->image == null) ? "" : url("uploads/store_images/") . '/' . $shop->image,
                        'description' => ($shop->description == null) ? "" : $shop->description,
                        'email' => ($shop->email == null) ? "" : $shop->email,
                        'phoneNumber' => ($shop->phoneNumber == null) ? "" : $shop->phoneNumber,
                        'address' => ($shop->address == null) ? "" : $shop->address,
                        'street' => ($shop->street == null) ? "" : $shop->street,
                        'city' => ($shop->city == null) ? "" : $shop->city,
                        'state' => ($shop->state == null) ? "" : $shop->state,
                        'country' => ($shop->country == null) ? "" : $shop->country,
                        'lat' => ($shop->lat == null) ? "" : $shop->lat,
                        'long' => ($shop->long == null) ? "" : $shop->long,
                        'shop_break_timings__' => $shop_break_timings__,
                        'shop_timings__' => $shop_timings__
                    );
                    $serachDetails[] = $ret;
                }
            }
            if (!empty($serachDetails)) {
                $result['status'] = true;

                $result['message'] = 'Reservation List';

                $result["serachDetails"] = $serachDetails;
            } else {
                $result['status'] = false;

                $result['message'] = 'No Results';

                $result["serachDetails"] = $serachDetails;
            }
        } else {
            $result['status'] = false;

            $result['message'] = 'Invalid User Details';

            $result["serachDetails"] = [];
        }
        return json_encode($result);
    }
}