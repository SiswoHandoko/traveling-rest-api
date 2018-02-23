<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Model\PlanDetail;

class PlanDetailController extends Controller
{
    /**
    * Create a new auth instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $req)
    {
        $search_query = $req->input('search_query') ? $req->input('search_query') : '';
        $offset = $req->input('offset') ? $req->input('offset') : 0;
        $limit = $req->input('limit') ? $req->input('limit') : 255;
        $order_by = $req->input('order_by') ? $req->input('order_by') : 'id';
        $order_type = $req->input('order_type') ? $req->input('order_type') : 'asc';
        
        $plandetail = PlanDetail::with('plan', 'tourismplace')
            ->where('status', '!=', 'deleted')
            ->where('start_time', 'LIKE', '%'.$search_query.'%')
            ->orderBy($order_by, $order_type)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $result = $this->generate_response($plandetail, 200, 'All Data.', false);

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
        /* Validation */
        $validator = Validator::make($req->all(), [
            'plan_id' => 'required|numeric|min:0',
            'tourism_place_id' => 'required|numeric|min:0',
            'start_time' => 'required|date_format:"H:i"',
            'end_time' => 'required|date_format:"H:i"',
            'day' => 'required|numeric|min:0',
            'total_price_adult' => 'required|numeric|min:0',
            'total_price_child' => 'required|numeric|min:0',
            'total_price_infant' => 'required|numeric|min:0',
            'total_price_tourist' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            $result = $this->generate_response($plandetail,400,'Bad Request.',true);

            return response()->json($result, 400);
        } else {
            $plandetail = new PlanDetail();

            $plandetail->plan_id = $req->has('plan_id') ? $req->plan_id : 0;
            $plandetail->tourism_place_id = $req->has('tourism_place_id') ? $req->tourism_place_id : 0;
            $plandetail->start_time = $req->has('start_time') ? $req->start_time : '00:00';
            $plandetail->end_time = $req->has('end_time') ? $req->end_time : '00:00';
            $plandetail->day = $req->has('day') ? $req->day : 0;
            $plandetail->total_price_adult = $req->has('total_price_adult') ? $req->total_price_adult : 0;
            $plandetail->total_price_child = $req->has('total_price_child') ? $req->total_price_child : 0;
            $plandetail->total_price_infant = $req->has('total_price_infant') ? $req->total_price_infant : 0;
            $plandetail->total_price_tourist = $req->has('total_price_tourist') ? $req->total_price_tourist : 0;
            $plandetail->no_ticket = $req->has('no_ticket') ? $req->no_ticket : '';
            $plandetail->status = $req->has('status') ? $req->status : 'active';

            $plandetail->save();

            $result = $this->generate_response($plandetail, 200, 'Data Has Been Saved.', false);

            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PlanDetail  $plandetail
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plandetail = PlanDetail::with('plan', 'tourismplace')->where('status', '!=', 'deleted')->find($id);

        if (!$plandetail) {
            $result = $this->generate_response($plandetail, 404, 'Data Not Found.', true);

            return response()->json($result, 404);
        } else {
            $result = $this->generate_response($plandetail, 200, 'Detail Data.', false);

            return response()->json($result, 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlanDetail  $plandetail
     * @return \Illuminate\Http\Response
     */

    public function update(Request $req, $id)
    {
        /* Validation */
        $validator = Validator::make($req->all(), [
            'tourism_place_id' => 'required|numeric|min:0',
            'start_time' => 'required|date_format:"H:i"',
            'end_time' => 'required|date_format:"H:i"',
            'day' => 'required|numeric|min:0',
            'total_price_adult' => 'required|numeric|min:0',
            'total_price_child' => 'required|numeric|min:0',
            'total_price_infant' => 'required|numeric|min:0',
            'total_price_tourist' => 'required|numeric|min:0'
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($plandetail, 400, 'Bad Request.', true);
            
            return response()->json($result, 400);
        }else{
            $plandetail = PlanDetail::where('status', '!=', 'deleted')->find($id);

            if (!$plandetail) {
                $result = $this->generate_response($plandetail, 404, 'Data Not Found.', true);

                return response()->json($result, 404);
            } else {
                $plandetail->tourism_place_id = $req->has('tourism_place_id') ? $req->tourism_place_id : $plandetail->tourism_place_id;
                $plandetail->start_time = $req->has('start_time') ? $req->start_time : $plandetail->start_time;
                $plandetail->end_time = $req->has('end_time') ? $req->end_time : $plandetail->end_time;
                $plandetail->day = $req->has('day') ? $req->day : $plandetail->day;
                $plandetail->total_price_adult = $req->has('total_price_adult') ? $req->total_price_adult : $plandetail->total_price_adult;
                $plandetail->total_price_child = $req->has('total_price_child') ? $req->total_price_child : $plandetail->total_price_child;
                $plandetail->total_price_infant = $req->has('total_price_infant') ? $req->total_price_infant : $plandetail->total_price_infant;
                $plandetail->total_price_tourist = $req->has('total_price_tourist') ? $req->total_price_tourist : $plandetail->total_price_tourist;
                $plandetail->no_ticket = $req->has('no_ticket') ? $req->no_ticket : $plandetail->no_ticket;
                $plandetail->status = $req->has('status') ? $req->status : $plandetail->status;

                $plandetail->save();

                $result = $this->generate_response($plandetail, 200, 'Data Has Been Updated.', false);

                return response()->json($result, 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PlanDetail  $plandetail
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plandetail = PlanDetail::where('status', '!=', 'deleted')->find($id);

        if (!$plandetail) {
            $result = $this->generate_response($plandetail, 404, 'Data Not Found.', true);

            return response()->json($result, 404);
        } else {
            $plandetail->status = 'deleted';
            
            $plandetail->save();
            
            $result = $this->generate_response($plandetail, 200, 'Data Has Been Deleted.', false);
            
            return response()->json($result, 200);
        }
    }
}
