<?php

namespace App\Http\Controllers;

use App\Models\Shops;
use App\Models\ShopTimings;
use Illuminate\Http\Request;

class shopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shops =    Shops::where('is_active', 1)->get();
        return view('admin.dashbord', compact('shops'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pname = '';
        if ($request->hasFile('filename')) {

            $pfileimg = $request->filename;

            $pextension = $pfileimg->getClientOriginalExtension();

            $pname = time() . rand(11111, 99999) . '.' . $pextension;

            $pfileimg->move('uploads/store_images/', $pname);
        }
        $fullAddress =  $request->street . " " . $request->city . " " . $request->state;
        $latLong = $this->get_lat_long($fullAddress);
        $latitude = '';
        $longitude = '';
        if ($latLong) {
            $latitude = ($latLong['lat'] == null) ? "" : $latLong['lat'];
            $longitude = ($latLong['long'] == null) ? "" : $latLong['long'];
        }
        $data = [
            "name" => $request->name,
            "image" => $pname,
            "description" => $request->description,
            "email" => $request->email,
            "phoneNumber"    => $request->phoneNumber,
            "address" => $request->address,
            "street" => $request->street,
            "city" => $request->city,
            "state" => $request->state,
            "country" => $request->country,
            "lat"    => $latitude,
            "long"    => $longitude,
            "is_active" => 1,
        ];
        $shop_id = Shops::insertGetId([$data]);
        $days = $request->days;
        $starttime = $request->starttime;
        $endtime = $request->endtime;
        $missing_days__ = array(
            'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
        );
        if ($shop_id) {
            if (!empty($days)) {
                foreach ($days as $key => $day) {
                    if (isset($starttime[$key]) && isset($endtime[$key])) {
                        if (!empty($starttime[$key]) && !empty($endtime[$key]) && ($starttime[$key] < $endtime[$key])) {
                            if (($days___key = array_search($day, $missing_days__)) !== false) {
                                unset($missing_days__[$days___key]);
                            }
                            ShopTimings::insert([
                                [
                                    "storeId" => $shop_id,
                                    "days" => $day,
                                    "startTime" => $starttime[$key],
                                    "endTime" => $endtime[$key],
                                    "status" => 1,
                                    "breakStatus" => 0,
                                    "is_open" => 1
                                ]
                            ]);
                        }
                    }
                }
                if (!empty($missing_days__)) {
                    foreach ($missing_days__ as $missedDay) {
                        ShopTimings::insert([
                            [
                                "storeId" => $shop_id,
                                "days" => $missedDay,
                                "startTime" => NULL,
                                "endTime" => NULL,
                                "status" => 1,
                                "breakStatus" => 0,
                                "is_open" => 0
                            ]
                        ]);
                    }
                }
            }
            if ($request->break && $request->breakstarttime != null &&  $request->breakendtime != null && ($request->breakstarttime < $request->breakendtime)) {
                ShopTimings::insert([
                    [
                        "storeId" => $shop_id,
                        "days" => 'break',
                        "startTime" => $request->breakstarttime,
                        "endTime" => $request->breakendtime,
                        "status" => 2,
                        "breakStatus" => 1,
                        "is_open" => 1
                    ]
                ]);
            }
        }
        return redirect('shop');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = base64_decode($id);
        $data['shop'] = Shops::find($id);
        $html = view('admin.view', $data)->render();
        return $html;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = base64_decode($id);
        $data['shop'] = Shops::find($id);
        return view('admin.update', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = base64_decode($id);
        $data = Shops::find($id);
        if (!empty($data)) {
            if ($request->hasFile('filename')) {

                $image = public_path() . "/uploads/store_images/" . $data->image;
                unset($image);

                $pfileimg = $request->filename;

                $pextension = $pfileimg->getClientOriginalExtension();

                $pname = time() . rand(11111, 99999) . '.' . $pextension;

                $pfileimg->move('uploads/store_images/', $pname);

                $data->image = $pname;
            }
            $fullAddress =  $request->street . " " . $request->city . " " . $request->state;
            $latLong = $this->get_lat_long($fullAddress);
            $latitude = '';
            $longitude = '';
            if ($latLong) {
                $latitude = ($latLong['lat'] == null) ? "" : $latLong['lat'];
                $longitude = ($latLong['long'] == null) ? "" : $latLong['long'];
            }
            $data->name = $request->name;
            $data->description = $request->description;
            $data->email = $request->email;
            $data->phoneNumber = $request->phoneNumber;
            $data->address = $request->address;
            $data->street = $request->street;
            $data->city = $request->city;
            $data->country = $request->country;
            $data->lat = $latitude;
            $data->long = $longitude;
            $data->is_active = 1;
            $data->save();
            $shop_id = $id;
            $days = $request->days;
            $workingDaysIds = $request->workingDaysId;
            $starttime = $request->starttime;
            $endtime = $request->endtime;

            if ($shop_id) {
                if (!empty($days) && !empty($workingDaysIds)) {
                    foreach ($days as $key => $day) {
                        if (isset($starttime[$key]) && isset($endtime[$key])) {
                            if (!empty($starttime[$key]) && !empty($endtime[$key]) && ($starttime[$key] < $endtime[$key])) {

                                ShopTimings::where('id', $workingDaysIds[$key])->update(
                                    [
                                        "storeId" => $shop_id,
                                        "days" => $day,
                                        "startTime" => $starttime[$key],
                                        "endTime" => $endtime[$key],
                                        "status" => 1,
                                        "breakStatus" => 0,
                                        "is_open" => 1
                                    ]
                                );
                            }
                        }
                    }
                } else if (!empty($days) && empty($workingDaysIds)) {
                    $missing_days__ = array(
                        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                    );
                    foreach ($days as $key => $day) {
                        if (isset($starttime[$key]) && isset($endtime[$key])) {
                            if (!empty($starttime[$key]) && !empty($endtime[$key]) && ($starttime[$key] < $endtime[$key])) {
                                if (($days___key = array_search($day, $missing_days__)) !== false) {
                                    unset($missing_days__[$days___key]);
                                }
                                ShopTimings::insert([
                                    [
                                        "storeId" => $shop_id,
                                        "days" => $day,
                                        "startTime" => $starttime[$key],
                                        "endTime" => $endtime[$key],
                                        "status" => 1,
                                        "breakStatus" => 0,
                                        "is_open" => 1
                                    ]
                                ]);
                            }
                        }
                    }
                    if (!empty($missing_days__)) {
                        foreach ($missing_days__ as $missedDay) {
                            ShopTimings::insert([
                                [
                                    "storeId" => $shop_id,
                                    "days" => $missedDay,
                                    "startTime" => NULL,
                                    "endTime" => NULL,
                                    "status" => 1,
                                    "breakStatus" => 0,
                                    "is_open" => 0
                                ]
                            ]);
                        }
                    }
                }
                if ($request->break && $request->break_id && $request->breakstarttime != null &&  $request->breakendtime != null && ($request->breakstarttime < $request->breakendtime)) {
                    ShopTimings::where('id', $request->break_id)->update(
                        [
                            "storeId" => $shop_id,
                            "days" => 'break',
                            "startTime" => $request->breakstarttime,
                            "endTime" => $request->breakendtime,
                            "status" => 2,
                            "breakStatus" => 1,
                            "is_open" => 1
                        ]
                    );
                } else if ($request->break && $request->breakstarttime != null &&  $request->breakendtime != null && ($request->breakstarttime < $request->breakendtime)) {
                    ShopTimings::insert([
                        [
                            "storeId" => $shop_id,
                            "days" => 'break',
                            "startTime" => $request->breakstarttime,
                            "endTime" => $request->breakendtime,
                            "status" => 2,
                            "breakStatus" => 1,
                            "is_open" => 1
                        ]
                    ]);
                }
            }
        }
        return redirect('shop');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = base64_decode($id);
        $shop = Shops::find($id);
        $image = public_path() . "/uploads/store_images/" . $shop->image;
        unset($image);
        $shop->delete();
        ShopTimings::where('storeId', $id)->delete();
    }
    private function get_lat_long($address)
    {
        $return = [];
        $apiKey = getenv('GeeoAPiKey');
        $json = file_get_contents("http://api.positionstack.com/v1/forward?access_key=$apiKey&query=" . urlencode($address));
        $json = json_decode($json);
        $return['lat'] = @$json->data[0]->latitude;
        $return['long'] = @$json->data[0]->longitude;
        return $return;
    }
}