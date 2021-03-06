<?php
namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Model\Advertisement;

class AdvertisementController extends Controller
{
    private $fields_advertisements = array(
        'id',
        'image_url',
        'title',
        'caption',
        'type',
        'city_id',
        'status'
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

        $param_insert = array(
            'name' => 'advertisement_index',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        $advertisement = new Advertisement;
        $advertisement = $advertisement->with('city');
        $advertisement = $advertisement->where('status', '!=', 'deleted');
        
        // search query
        if ($req->input('search_query')) {
            $search_query = $req->input('search_query') ? $req->input('search_query') : '';

            $advertisement = $advertisement->where('title', 'LIKE', '%'.$search_query.'%');
        }

        // where custom
        if ($req->input('where_by') && $req->input('where_value')) {
            $explode_by = explode('|', $req->input('where_by'));
            $explode_value = explode('|', $req->input('where_value'));

            if ((count($explode_by)==count($explode_value)) && ($this->check_where($explode_by, $this->fields_advertisements))) {
                foreach ($explode_by as $key => $value) {
                    $advertisement = $advertisement->where($explode_by[$key], '=', $explode_value[$key]);
                }
            } else {
                $result = $this->generate_response($advertisement, 400, 'Bad Request.', true);

                $this->update_access_log($access_log_id, $result);
                
                return response()->json($result, 400);
            }
        }

        // order
        if ($req->input('order_by')) {
            if (in_array($req->input('order_by'), $this->fields_advertisements)) {
                $order_type = $req->input('order_type') ? $req->input('order_type') : 'asc';

                $advertisement = $advertisement->orderBy($req->input('order_by'), $order_type);
            } else {
                $result = $this->generate_response($advertisement, 400, 'Bad Request.', true);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // limit
        if ($req->input('limit')) {
            $offset = $req->input('offset') ? $req->input('offset') : 0;

            $advertisement = $advertisement->offset($offset);
            $advertisement = $advertisement->limit($req->input('limit'));
        }

        $advertisement = $advertisement->get();

        $result = $this->generate_response($advertisement,200,'All Data.',false);

        $this->update_access_log($access_log_id, $result);

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
            'name' => 'advertisement_store',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        /* Validation */
        $validator = Validator::make($req->all(), [
          //'image_url' => 'max:20480',
          'title' => 'required|max:255',
          'caption' => 'required|max:255',
          'type' => 'required|max:255',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($advertisement,400,'Bad Request.',true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 400);
        }else{
            $advertisement = new Advertisement();
            /* upload process */
            $advertisement->image_url = $req->has('image_url') ? env('BACKEND_URL').'public/images/advertisements/'.$this->uploadFile($this->public_path(). "/images/advertisements/", $req->image_url) : env('BACKEND_URL').'public/images/advertisements/default_advertisement.png';
            $advertisement->title = $req->has('title') ? $req->title : '';
            $advertisement->caption = $req->has('caption') ? $req->caption : '';
            $advertisement->type = $req->has('type') ? $req->type : '';
            $advertisement->status = $req->has('status') ? $req->status : 'active';
            $advertisement->city_id = $req->has('city_id') ? $req->city_id : 0;
            $advertisement->save();

            $result = $this->generate_response($advertisement,200,'Data Has Been Saved.',false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function show(Request $req, $id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'advertisement_show',
            'params' => '',
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        $advertisement = Advertisement::with('city')->where('status','!=','deleted')->find($id);

        if(!$advertisement){
            $result = $this->generate_response($advertisement, 404, 'Data Not Found.', true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 404);
        }else{
            $result = $this->generate_response($advertisement, 200, 'Detail Data.', false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */

    public function update(Request $req, $id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'advertisement_update',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        /* Validation */
        $validator = Validator::make($req->all(), [
          //'image_url' => 'max:20480',
          'title' => 'required|max:255',
          'caption' => 'required|max:255',
          'type' => 'required|max:255',
        ]);
        
        if($validator->fails()) {
            $result = $this->generate_response($advertisement,400,'Bad Request.',true);

            return response()->json($result, 400);
        }else{
            $advertisement = Advertisement::find($id);

            if(!$advertisement){
                $result = $this->generate_response($advertisement, 404, 'Data Not Found.', true);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 404);
            }else{
                /* upload process */
                $advertisement->image_url = $req->has('image_url') ? env('BACKEND_URL').'public/images/advertisements/'.$this->uploadFile($this->public_path(). "/images/advertisements/", $req->image_url, $advertisement->image_url) :  $advertisement->image_url;
                $advertisement->title = $req->has('title') ? $req->title : $advertisement->title;
                $advertisement->caption = $req->has('caption') ? $req->caption : $advertisement->title;
                $advertisement->type = $req->has('type') ? $req->type : $advertisement->title;
                $advertisement->status = $req->has('status') ? $req->status : $advertisement->status;
                $advertisement->city_id = $req->has('city_id') ? $req->city_id : $advertisement->city_id;
                $advertisement->save();

                $result = $this->generate_response($advertisement,200,'Data Has Been Saved.',false);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Advertisement  $advertisement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $req, $id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'advertisement_destroy',
            'params' => json_encode(array("id" => $id)),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        $advertisement = Advertisement::where('status', '!=', 'deleted')->find($id);
        
        if(!$advertisement){
            $result = $this->generate_response($advertisement, 404, 'Data Not Found.', true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 404);
        }else{
            $advertisement->status = 'deleted';

            $advertisement->save();

            $result = $this->generate_response($advertisement,200,'Data Has Been Deleted.',false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }
}
