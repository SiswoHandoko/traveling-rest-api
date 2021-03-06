<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Model\City;
use App\Model\TourismPlace;
use App\Model\Package;

class CityController extends Controller
{
    private $fields_cities = array(
        'id',
        'province_id',
        'name',
        'image_url',
        'rate',
        'status',
        'longitude',
        'latitude'
    );

    private $fields_tourismplaces = array(
        'id',
        'city_id',
        'category_id',
        'name',
        'description',
        'adult_price',
        'child_price',
        'infant_price',
        'tourist_price',
        'longitude',
        'latitude',
        'facilities',
        'status'
    );

    private $fields_packages = array(
        'id',
        'name',
        'description',
        'days',
        'start_date',
        'end_date',
        'image_url',
        'status',
        'city_id'
    );

    /**
    * Create a new auth instance.
    *
    * @return void
    */
    public function __construct()
    {
        // $this->middleware('auth');
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $req)
    {
        $this->check_account($req);

        // $param_insert = array(
        //     'name' => 'city_index',
        //     'params' => json_encode(collect($req)->toArray()),
        //     'result' => ''
        // );

        // $access_log_id = $this->create_access_log($param_insert);

        $city = new City;
        $city = $city->with('province');
        $city = $city->where('status', '!=', 'deleted');

        // search query
        if ($req->input('search_query')) {
            $search_query = $req->input('search_query') ? $req->input('search_query') : '';

            $city = $city->where('name', 'LIKE', '%'.$search_query.'%');
        }

        // where custom
        if ($req->input('where_by') && $req->input('where_value')) {
            $explode_by = explode('|', $req->input('where_by'));
            $explode_value = explode('|', $req->input('where_value'));

            if ((count($explode_by)==count($explode_value)) && ($this->check_where($explode_by, $this->fields_cities))) {
                foreach ($explode_by as $key => $value) {
                    $city = $city->where($explode_by[$key], '=', $explode_value[$key]);
                }
            } else {
                $result = $this->generate_response($city, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // order
        if ($req->input('order_by')) {
            if (in_array($req->input('order_by'), $this->fields_cities)) {
                $order_type = $req->input('order_type') ? $req->input('order_type') : 'asc';

                $city = $city->orderBy($req->input('order_by'), $order_type);
            } else {
                $result = $this->generate_response($city, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // limit
        if ($req->input('limit')) {
            $offset = $req->input('offset') ? $req->input('offset') : 0;

            $city = $city->offset($offset);
            $city = $city->limit($req->input('limit'));
        }

        $city = $city->get();

        $result = $this->generate_response($city, 200, 'All Data.', false);

        // $this->update_access_log($access_log_id, $result);

        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'city_store',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        /* Validation */
        $validator = Validator::make($req->all(), [
          'province_id' => 'required',
          'name' => 'required|max:255',
          // 'image_url' => 'max:2048'
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($city,400,'Bad Request.',true);
            
            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 400);
        }else{
            $city = new City();
            $city->province_id = $req->has('province_id') ? $req->province_id : 0;
            $city->name = $req->has('name') ? $req->name : '';
            $city->status = $req->has('status') ? $req->status : 'active';
            $city->rate = $req->has('rate') ? $req->rate : 0;
            $city->latitude = $req->has('latitude') ? $req->latitude : 0;
            $city->longitude = $req->has('longitude') ? $req->longitude : 0;
            $city->image_url = $req->has('image_url') ? env('BACKEND_URL').'public/images/cities/'.$this->uploadFile($this->public_path(). "/images/cities/", $req->image_url) : env('BACKEND_URL').'public/images/cities/default_img.png';
            $city->save();

            $result = $this->generate_response($city,200,'Data Has Been Saved.',false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(Request $req, $id)
    {
        $this->check_account($req);

        // $param_insert = array(
        //     'name' => 'city_show',
        //     'params' => '',
        //     'result' => ''
        // );

        // $access_log_id = $this->create_access_log($param_insert);

        $city = new City();
        $city = $city->with('province');
        $city = $city->where('status','!=','deleted');
        $city = $city->find($id);

        if(!$city){
            $result = $this->generate_response($city, 404, 'Data Not Found.', true);

            // $this->update_access_log($access_log_id, $result);

            return response()->json($result, 404);
        }else{
            $result = $this->generate_response($city, 200, 'Detail Data.', false);

            // $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */

    public function update(Request $req,$id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'city_update',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);
        
        /* Validation */
        $validator = Validator::make($req->all(), [
            'name' => 'required|max:255',
            // 'image_url' => 'max:2048'
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($city,400,'Bad Request.',true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 400);
        }else{
            $city = City::find($id);
            if(!$city){
                $result = $this->generate_response($city, 404, 'Data Not Found.', true);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 404);
            }else{
                $city->name = $req->has('name') ? $req->name : $city->name;
                $city->image_url = $req->has('image_url') ? env('BACKEND_URL').'public/images/cities/'.$this->uploadFile($this->public_path(). "/images/cities/", $req->image_url,$city->image_url) : $city->image_url;
                $city->status = $req->has('status') ? $req->status : $city->status;
                $city->rate = $req->has('rate') ? $req->rate : $city->rate;
                $city->latitude = $req->has('latitude') ? $req->latitude : $city->latitude;
                $city->longitude = $req->has('longitude') ? $req->longitude : $city->longitude;
                $city->province_id = $req->has('province_id') ? $req->province_id : $city->province_id;
                $city->save();

                $result = $this->generate_response($city,200,'Data Has Been Updated.',false);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $req, $id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'city_destroy',
            'params' => json_encode(array("id" => $id)),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        $city = City::where('status', '!=', 'deleted')->find($id);
        
        if(!$city){
            $result = $this->generate_response($city, 404, 'Data Not Found.', true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 404);
        }else{
            $city->status = 'deleted';
            $city->save();

            $result = $this->generate_response($city,200,'Data Has Been Deleted.',false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function tourismplace_by_city(Request $req, $id)
    {
        $this->check_account($req);

        // $param_insert = array(
        //     'name' => 'city_tourismplace',
        //     'params' => json_encode(collect($req)->toArray()),
        //     'result' => ''
        // );

        // $access_log_id = $this->create_access_log($param_insert);

        $tourismplace = new TourismPlace;
        $tourismplace = $tourismplace->with('city.province', 'picture', 'event', 'category');
        $tourismplace = $tourismplace->where('city_id', $id);
        $tourismplace = $tourismplace->where('status', '!=', 'deleted');

        // search query
        if ($req->input('search_query')) {
            $search_query = $req->input('search_query') ? $req->input('search_query') : '';

            $tourismplace = $tourismplace->where('name', 'LIKE', '%'.$search_query.'%');
        }

        // where custom
        if ($req->input('where_by') && $req->input('where_value')) {
            $explode_by = explode('|', $req->input('where_by'));
            $explode_value = explode('|', $req->input('where_value'));

            if ((count($explode_by)==count($explode_value)) && ($this->check_where($explode_by, $this->fields_tourismplaces))) {
                foreach ($explode_by as $key => $value) {
                    $tourismplace = $tourismplace->where($explode_by[$key], '=', $explode_value[$key]);
                }
            } else {
                $result = $this->generate_response($tourismplace, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // order
        if ($req->input('order_by')) {
            if (in_array($req->input('order_by'), $this->fields_tourismplaces)) {
                $order_type = $req->input('order_type') ? $req->input('order_type') : 'asc';

                $tourismplace = $tourismplace->orderBy($req->input('order_by'), $order_type);
            } else {
                $result = $this->generate_response($tourismplace, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // limit
        if ($req->input('limit')) {
            $offset = $req->input('offset') ? $req->input('offset') : 0;

            $tourismplace = $tourismplace->offset($offset);
            $tourismplace = $tourismplace->limit($req->input('limit'));
        }

        $tourismplace = $tourismplace->get();

        if ($req->input('latitude') && $req->input('longitude')) {
            $tourismplace = $this->sort_distance($tourismplace, $req->input('latitude'), $req->input('longitude'));
        }

        $result = $this->generate_response($tourismplace, 200, 'All Data.', false);

        // $this->update_access_log($access_log_id, $result);

        return response()->json($result, 200);
    }

    private function sort_distance($tourismplace, $latitude, $longitude)
    {
        $arr_distance = array();

        foreach ($tourismplace as $key => $value) {
            $distance = $this->haversineGreatCircleDistance($latitude, $longitude, $value->latitude, $value->longitude);
            $tourismplace[$key]['distance'] = $distance;
            $arr_distance[$key] = $distance;
        }

        $result = $this->array_msort($tourismplace, array('distance'=>SORT_ASC));

        $result = array_values($result);

        return $result;
    }

    private function array_msort($array, $cols)
    {
        $colarr = array();

        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }

        $eval = 'array_multisort(';

        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }

        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }

        return $ret;
    }

    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function package_by_city(Request $req, $id)
    {
        $this->check_account($req);
        
        // $param_insert = array(
        //     'name' => 'city_package',
        //     'params' => json_encode(collect($req)->toArray()),
        //     'result' => ''
        // );

        // $access_log_id = $this->create_access_log($param_insert);

        $package = new Package;
        $package = $package->with('packagedetail.tourismplace.city');
        $package = $package->where('status', '!=', 'deleted');
        $package = $package->whereHas('packagedetail.tourismplace.city', function($query) use ($id) {
            $query->where('id' , $id);
        });

        // search query
        if ($req->input('search_query')) {
            $search_query = $req->input('search_query') ? $req->input('search_query') : '';

            $package = $package->where('name', 'LIKE', '%'.$search_query.'%');
        }

        // where custom
        if ($req->input('where_by') && $req->input('where_value')) {
            $explode_by = explode('|', $req->input('where_by'));
            $explode_value = explode('|', $req->input('where_value'));

            if ((count($explode_by)==count($explode_value)) && ($this->check_where($explode_by, $this->fields_packages))) {
                foreach ($explode_by as $key => $value) {
                    $package = $package->where($explode_by[$key], '=', $explode_value[$key]);
                }
            } else {
                $result = $this->generate_response($package, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // order
        if ($req->input('order_by')) {
            if (in_array($req->input('order_by'), $this->fields_packages)) {
                $order_type = $req->input('order_type') ? $req->input('order_type') : 'asc';

                $package = $package->orderBy($req->input('order_by'), $order_type);
            } else {
                $result = $this->generate_response($package, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // limit
        if ($req->input('limit')) {
            $offset = $req->input('offset') ? $req->input('offset') : 0;

            $package = $package->offset($offset);
            $package = $package->limit($req->input('limit'));
        }

        $package = $package->get();

        $result = $this->generate_response($package, 200, 'All Data.', false);

        // $this->update_access_log($access_log_id, $result);

        return response()->json($result, 200);
    }
}
