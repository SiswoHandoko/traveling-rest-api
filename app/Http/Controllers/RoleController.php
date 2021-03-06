<?php
namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Model\Role;

class RoleController extends Controller
{
    private $fields_roles = array(
        'id',
        'name',
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

        // $param_insert = array(
        //     'name' => 'role_index',
        //     'params' => json_encode(collect($req)->toArray()),
        //     'result' => ''
        // );

        // $access_log_id = $this->create_access_log($param_insert);

        $role = new Role;
        $role = $role->where('status', '!=', 'deleted');

        // search query
        if ($req->input('search_query')) {
            $search_query = $req->input('search_query') ? $req->input('search_query') : '';

            $role = $role->where('name', 'LIKE', '%'.$search_query.'%');
        }

        // where custom
        if ($req->input('where_by') && $req->input('where_value')) {
            $explode_by = explode('|', $req->input('where_by'));
            $explode_value = explode('|', $req->input('where_value'));

            if ((count($explode_by)==count($explode_value)) && ($this->check_where($explode_by, $this->fields_roles))) {
                foreach ($explode_by as $key => $value) {
                    $role = $role->where($explode_by[$key], '=', $explode_value[$key]);
                }
            } else {
                $result = $this->generate_response($role, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);
                
                return response()->json($result, 400);
            }
        }

        // order
        if ($req->input('order_by')) {
            if (in_array($req->input('order_by'), $this->fields_roles)) {
                $order_type = $req->input('order_type') ? $req->input('order_type') : 'asc';

                $role = $role->orderBy($req->input('order_by'), $order_type);
            } else {
                $result = $this->generate_response($role, 400, 'Bad Request.', true);

                // $this->update_access_log($access_log_id, $result);

                return response()->json($result, 400);
            }
        }

        // limit
        if ($req->input('limit')) {
            $offset = $req->input('offset') ? $req->input('offset') : 0;

            $role = $role->offset($offset);
            $role = $role->limit($req->input('limit'));
        }

        $role = $role->get();

        $result = $this->generate_response($role, 200, 'All Data.', false);

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
            'name' => 'role_store',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        /* Validation */
        $validator = Validator::make($req->all(), [
            'name' => 'required|max:255',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($role, 400, 'Bad Request.', true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 400);
        }else{
            $role = new Role();

            $role->name = $req->has('name') ? $req->name : '';
            $role->status = $req->has('status') ? $req->status : 'active';

            $role->save();

            $result = $this->generate_response($role, 200, 'Data Has Been Saved.', false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Request $req, $id)
    {
        $this->check_account($req);

        // $param_insert = array(
        //     'name' => 'role_show',
        //     'params' => '',
        //     'result' => ''
        // );

        // $access_log_id = $this->create_access_log($param_insert);

        $role = Role::where('status', '!=', 'deleted')->find($id);
        
        if (!$role) {
            $result = $this->generate_response($role, 404, 'Data Not Found.', true);

            // $this->update_access_log($access_log_id, $result);

            return response()->json($result, 404);
        } else {
            $result = $this->generate_response($role, 200, 'Detail Data.', false);

            // $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */

    public function update(Request $req, $id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'role_update',
            'params' => json_encode(collect($req)->toArray()),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        /* Validation */
        $validator = Validator::make($req->all(), [
            'name' => 'required|max:255',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($role, 400, 'Bad Request.', true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 400);
        }else{
            $role = Role::where('status', '!=', 'deleted')->find($id);

            if (!$role) {
                $result = $this->generate_response($role, 404, 'Data Not Found.', true);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 404);
            } else {
                $role->name = $req->has('name') ? $req->name : $role->name;
                                        
                $role->save();

                $result = $this->generate_response($role, 200, 'Data Has Been Updated.', false);

                $this->update_access_log($access_log_id, $result);

                return response()->json($result, 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $req, $id)
    {
        $this->check_account($req);

        $param_insert = array(
            'name' => 'role_destroy',
            'params' => json_encode(array("id" => $id)),
            'result' => ''
        );

        $access_log_id = $this->create_access_log($param_insert);

        $role = Role::where('status', '!=', 'deleted')->find($id);

        if (!$role) {
            $result = $this->generate_response($role, 404, 'Data Not Found.', true);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 404);
        } else {
            $role->status = 'deleted';
            
            $role->save();
            
            $result = $this->generate_response($role, 200, 'Data Has Been Deleted.', false);

            $this->update_access_log($access_log_id, $result);

            return response()->json($result, 200);
        }
    }
}
