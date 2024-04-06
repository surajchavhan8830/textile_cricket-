<?php

namespace App\Http\Controllers;

use App\Models\FabricCategory;
use App\Models\User;
use App\Models\Yarn;
use App\Models\YarnCategory;
use App\Models\FabricCost;
use App\Models\Warp;
use App\Models\Weft;
use App\Models\Oldnumber;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\Models\Package;
use App\Models\Packagelist;
use App\Models\PaymentDetail;
use Hamcrest\Core\IsNot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;





class YarnApiController extends Controller
{

    public function testingjoins()
    {
        $j = Yarn::rightJoin('yarn_categories', 'yarns.category_id', '=', 'yarn_categories.id')
                ->join('wefts', 'yarns.id', '=', 'wefts.yarn_id')
                ->select('yarns.*', 'yarn_categories.yarn_category as category_name', 'wefts.fabric_cost_id')
                ->get();

        return response()->json([
            'data' => $j
        ]);
    }
    public function yarnIndex(Request $request)
    {

        if (empty($request->user_id)) {
            return json_encode(['success' => false,  'message' => ('User ID paramater not found'), 'data' =>   []]);
        }

        $user_id = $request->user_id;

        $yarns_query = Yarn::where('user_id', $user_id);
        $yarns = $yarns_query->get();
        // dd($yarns);
        // die;
        if ($request->keyword) {
            $yarns->where('yarn_name', 'LIKE', '%' . $request->keyword . '%')->get();
        }

        return json_encode(['success' => true,  'message' => ('Yarns List'), 'data' =>  $yarns,]);
    }



    public function yarnCreate(Request $request)
    {
        $user_id = $request->user_id;

        $validator = Validator::make($request->all(), [
            // 'yarn_name'        => 'required|unique:yarns',
            'yarn_name'   => Rule::unique('yarns')->where(function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            }),
            'yarn_denier'      => 'required',
            'yarn_rate'        => 'required',
            'category_id'      => 'required',
        ], ['yarn_name.unique'   => 'This yarn is already saved']);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {

            $yarn = new Yarn();
            $yarn->yarn_name = $request->yarn_name;
            $yarn->yarn_denier = round($request->yarn_denier);
            $yarn->yarn_rate = round($request->yarn_rate);
            $yarn->category_id = $request->category_id;
            $yarn->user_id = $user_id;
            $yarn->save();
        }

        return json_encode(['success' => true,  'message' => ('Saved Successfully'), 'Yarn id' => $yarn->id]);
    }

    public function yarnUpdate(Request $request, $id)
    {
        $yarn = Yarn::find($id);
        if (is_null($yarn)) {
            return json_encode(
                [
                    'success' => false,
                    'message' => 'User dose not exists'
                ],
                404
            );
        } else {
            DB::beginTransaction();
            try {

                // $yarn = new Yarn();
                $yarn->yarn_name   = $request->yarn_name;
                $yarn->yarn_denier = round($request->yarn_denier);
                $yarn->yarn_rate   = round($request->yarn_rate);
                $yarn->category_id = $request->category_id;
                $yarn->user_id     = $request->user_id;
                $yarn->save();


                DB::commit();
            } catch (\Exception $err) {
                DB::rollBack();
                $yarn = null;
            }
            if (is_null($yarn)) {
                return json_encode(
                    [

                        'success' => false,
                        'message' => 'Internal server error',
                        'error_msg' => $err->getMessage()
                    ],
                    500
                );
            } else {
                return json_encode(
                    [
                        'success' => true,
                        'message' => 'Updated Successfully'
                    ],
                    200
                );
            }
        }
    }

    public function yarnDestroy(Request $request, $id)
    {


        $user_id = $request->user_id;
        // dd($user_id);

        $yarn = Yarn::where('id', $id)->first();

        $warp = Warp::where('yarn_name', $id)->first();
        $weft = Weft::where('yarn_id', $id)->first();

        if ($warp || $weft) {
            return json_encode(['success' => true,  'message' => ('This yarn cannot be deleted'),]);
        } else {

            $yarn = $yarn->delete();

            if ($yarn) {
                return json_encode(['success' => true,  'message' => ('Deleted Successfully'),]);
            } else {
                return json_encode(['success' => false,  'message' => ('Delete opration failed'),]);
            }
        }
    }

    // Yarn Category Api Function 

    public function yarnCategory(Request $request)
    {

        if (empty($request->user_id)) {
            return json_encode(['success' => false,  'message' => ('User ID paramater not found'), 'data' =>   []]);
        }
        $user_id = $request->user_id;
        $yarnCategory = YarnCategory::where('user_id', $user_id)
                                     ->orWhere('id', 0)
                                     ->OrderBy('yarn_category')
                                     ->get();
        return json_encode(['success' => true,  'message' => ('Yarns Category List'), 'data' =>  $yarnCategory,]);
    }

    public function yarnCreateCategory(Request $request)
    {
        $user_id = $request->user_id;

        $validator = Validator::make($request->all(), [

            // 'yarn_category'  => 'required|unique:yarn_categories',
            'yarn_category'   => Rule::unique('yarn_categories')->where(function ($query) use ($user_id) {
                return $query->where('user_id', $user_id)->orwhere('id', 0);
            }),

        ], ['yarn_category.unique'   => 'This category is already saved']);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {

            $yarn = new YarnCategory();
            $yarn->yarn_category = $request->yarn_category;
            $yarn->user_id = $user_id;
            $yarn->save();
        }

        return json_encode(['success' => true,  'message' => ('Saved Successfully'), 'yarn_category_id' => $yarn->id]);
    }

    public function yarnUpdateCategory(Request $request, $id)
    {

        // dd($id);
        // $yarnCategory = YarnCategory::find($id);



        $user_id = $request->user_id;

        $validator = Validator::make($request->all(), [

            // 'yarn_category'  => 'required|unique:yarn_categories',
            'yarn_category'   => Rule::unique('yarn_categories')->where(function ($query) use ($user_id) {
                return $query->where('user_id', $user_id)->orwhere('id', 0);
            }),

        ], ['yarn_category.unique'   => 'This category is already saved']);



        // $user_id = $request->user_id;
        // dd($user_id);
        $yarnCategory = YarnCategory::where('id', $id)->where('user_id', $user_id)->first();

        // dd($yarnCategory->id);

        if (is_null($yarnCategory)) {
            return json_encode(
                [
                    'success' => false,
                    'message' => 'General category cannot be edited'
                ],
                404
            );
        } elseif ($yarnCategory->id == 0) {
            return json_encode(
                [
                    'success' => false,
                    'message' => 'General category cannot be edited'
                ],
            );
        } elseif (strtolower($request->yarn_category) === "general") {
            return json_encode(
                [
                    'success' => false,
                    'message' => 'This category is already saved'
                ],
            );
        } else {
            DB::beginTransaction();
            try {

                $validator = Validator::make($request->all(), [

                    // 'yarn_category'  => 'required|unique:yarn_categories',
                    'yarn_category'   => Rule::unique('yarn_categories')->where(function ($query) use ($user_id) {
                        return $query->where('user_id', $user_id);
                    }),
                ]);

                if ($validator->fails()) {
                    return json_encode([
                        // 'status' => 422,
                        'success' => false,
                        'message' => ('This category is already saved')
                    ], 422);
                } else {


                    $yarnCategory->yarn_category = $request->yarn_category;
                    $yarnCategory->save();
                    DB::commit();
                }
            } catch (\Exception $err) {
                DB::rollBack();
                $yarn = null;
            }
            if (is_null($yarnCategory)) {
                return json_encode(
                    [

                        'success' => false,
                        'message' => 'Internal server error',
                        // 'error_msg' => $err->getMessage()
                    ],
                    500
                );
            } else {
                return json_encode(
                    [
                        'success' => true,
                        'message' => 'Updated Successfully'
                    ],
                    200
                );
            }
        }
    }

    public function yarnDestroyCategory($id)
    {
        $yarn = YarnCategory::find($id);
        // dd($yarn);

        $yarn_id = Yarn::where('category_id', $id)->first();

        if ($id == 0) {
            return json_encode(['success' => false,  'message' => ('General category cannot be deleted'),]);
        } elseif ($yarn_id) {

            return json_encode(['success' => false,  'message' => ('This category cannot be deleted'),]);
        } else {

            $yarn = $yarn->delete();

            if ($yarn) {
                return json_encode(['success' => true,  'message' => ('Deleted Successfully'),]);
            } else {
                return json_encode(['success' => false,  'message' => ('Delete opration failed'),]);
            }
        }
    }

    // Fabric Category Api Function 

    public function fabricCategory(Request $request)
    {

        if (empty($request->user_id)) {
            return json_encode(['success' => false,  'message' => ('User ID paramater not found'), 'data' =>   []]);
        }

        $user_id = $request->user_id;


        $yarnCategory = FabricCategory::where('user_id', $user_id)->orWhere('id', 0)->OrderBy('fabric_category')->get();


        return json_encode(['success' => true,  'message' => ('Yarns Category List'), 'data' =>  $yarnCategory,]);
    }

    public function fabricCreateCategory(Request $request)
    {
        $user_id = $request->user_id;
        // dd($user_id);

        $validator = Validator::make($request->all(), [
            // 'fabric_category'  => 'required|unique:fabric_categories',

            'fabric_category'   => Rule::unique('fabric_categories')->where(function ($query) use ($user_id) {
                return $query->where('user_id', $user_id)->orwhere('id', 0);
            }),
        ]);

        if ($validator->fails()) {
            return json_encode([
                // 'status' => 422,
                'success' => false,
                'message' => ('This category is already saved')
            ], 422);
        } else {

            $category = new FabricCategory();
            $category->fabric_category = $request->fabric_category;
            $category->user_id = $user_id;
            $category->save();
        }

        return json_encode(['success' => true,  'message' => ('Saved Successfully'), 'category_id' => $category->id]);
    }

    public function fabricUpdateCategory(Request $request, $id)
    {

        $user_id = $request->user_id;
        // dd($user_id);



        $validator = Validator::make($request->all(), [

            'fabric_category'   => Rule::unique('fabric_categories')->where(function ($query) use ($user_id) {
                return $query->where('user_id', $user_id)->orwhere('id', 0);
            }),
        ], ['fabric_categories.unique'   => 'This category is already saved']);

        $fabricCategory = FabricCategory::find($id);
        // $user_id = $request->user_id;


        if (is_null($fabricCategory)) {
            return json_encode(
                [
                    'success' => false,
                    'message' => 'User dose not exists'
                ],
                404
            );
        } else {
            DB::beginTransaction();
            try {
                $validator = Validator::make($request->all(), [
                    'fabric_category' => Rule::unique('fabric_categories')->where(function ($query) use ($user_id) {
                        return $query->where('user_id', $user_id);
                    }),
                ]);

                if ($validator->fails()) {
                    return json_encode([
                        // 'status' => 422,
                        'success' => false,
                        'message' => ('This category is already saved')
                    ], 422);
                } elseif ($fabricCategory->id === 0) {
                    return json_encode([
                        'success' => false,
                        'message' => ('General category cannot be edited')
                    ], 422);
                } elseif (strtolower($request->fabric_category) === "general") {
                    return json_encode(
                        [
                            'success' => false,
                            'message' => 'This category is already saved'
                        ],
                    );
                } else {

                    $fabricCategory->fabric_category  = $request->fabric_category;
                    $fabricCategory->save();
                    DB::commit();
                }
            } catch (\Exception $err) {
                DB::rollBack();
                $yarn = null;
            }
            if (is_null($fabricCategory)) {
                return json_encode(
                    [

                        'success' => false,
                        'message' => 'Internal server error',
                        'error_msg' => $err->getMessage()
                    ],
                    500
                );
            } else {
                return json_encode(
                    [
                        'success' => true,
                        'message' => 'Updated Successfully'
                    ],
                    200
                );
            }
        }
    }

    public function fabricDestroyCategory($id)
    {
        $fabric = FabricCategory::find($id);
        $fabric_id = FabricCost::where('fabric_category_id', $id)->first();


        if ($fabric->id === 0) {
            return json_encode(['success' => false,  'message' => ('General category cannot be deleted'),]);
        } elseif ($fabric_id) {
            return json_encode(['success' => false,  'message' => ('This category cannot be deleted'),]);
        } else {
            $yarn_c = $fabric->delete();
        };

        if ($yarn_c) {
            return json_encode(['success' => true,  'message' => ('Deleted Successfully'),]);
        } else {
            return json_encode(['success' => false,  'message' => ('Delete opration failed'),]);
        }
    }

    public function login(Request $request)
    {
        $mobile = $request->mobile_number;
        $providedPassword = $request->password;
        $current_time = Carbon::now();

        $query = User::where('mobile_number', $mobile)->select('id', 'name', 'email', 'is_customer', 'is_active', 'mobile_number', 'device_id', 'password')->first();


            if ($query && $query->is_active == 1) {

                if ($query->device_id !== $request->device_id) {
                    // dd($request->device_id); 
                    $user = User::find($query->id);
                    $user->device_id = $request->device_id;
                    $user->updated_at = $current_time;
                    $user->is_delete = 1;
                    $user->save();
                }

        if ($query) {
            $user = User::find($query->id);
            $user->updated_at = $current_time;
            $user->is_delete = 1;
            $user->save();
            $storedPassword = Crypt::decrypt($query->password);

            if ($providedPassword === $storedPassword) {
                // Authentication successful
                return json_encode([
                    'message'       => 'Login Successful',
                    'id'            => $query->id,
                    'name'          => $query->name,
                    'email'         => $query->email,
                    'is_customer'   => $query->is_customer,
                    'is_active'     => $query->is_active,
                    'mobile_number' => $query->mobile_number,
                ], 200);
            }
        }
    }


        // Authentication failed
        return json_encode(['error' => 'Incorrect Username or Password'], 401);
    }

    public function userlogin(Request $request)
    {
       
        $validator = Validator::make($request->all(), [

            'mobile_number'  => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {
            $mobile = $request->mobile_number;
            // dd($mobile);
            // $providedPassword = $request->password;

            $query = User::where('mobile_number', $mobile)->first();

                    
            // dd($query);

            if ($query && $query->is_active == 1) {
                    $user = User::find($query->id);
                    $user->is_delete = 1;
                    $user->save();

                $user = User::find($query->id);
                if ($query->device_id !== $request->device_id) {
                    // dd($request->device_id);
                    
                    $user->is_delete = 1;
                    $user->device_id = $request->device_id;
                    $user->save();
                }
                // $storedPassword = Crypt::decrypt($query->password);

                // if ($providedPassword === $storedPassword) {
                // Authentication successful
                return json_encode([
                    'id'            => $query->id,
                    'name'          => $query->name,
                    'email'         => $query->email,
                    'is_customer'   => $query->is_customer,
                    'is_active'     => $query->is_active,
                    'mobile_number' => $query->mobile_number,
                    'city'          => $query->city,
                    'company_name'  => $query->company_name,
                    'message'       => 'Login Successful',
                    'success'       => true,
                ], 200);
                // }
            } elseif ($query && $query->is_active == 0) {
                return json_encode(['success' => false,  'message' => ('User is inactive'),]);
            } else {
                return json_encode(['success' => false,  'message' => ('Incorrect Mobile Number or Otp'),]);
            }

            // Authentication failed
            // return json_encode(['error' => 'Incorrect Username or Password'], 401);
        }
    }

    public function userRegistration(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'name'  => 'required',
            'mobile_number'  => 'required|Unique:users',
        ],[
                'mobile_number.unique'   => 'Account already exists for this mobile number.',
                
            ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {

            // dd($request->all());
            // die;
            $user = new User();
            $user->name = ucwords($request->name);
            $user->email = $request->email;
            $user->mobile_number = $request->mobile_number;
            $user->password = Crypt::encrypt($request->password);
            $user->company_name = $request->company_name;
            $user->city = $request->city;
            $user->is_customer = 1;
            $user->device_id = $request->device_id;
            $user->save();

            $mobile = $request->mobile_number;
            
             $isEligible = !Oldnumber::where('mobile_number', $request->mobile_number)->exists();

            if(!$isEligible)
            {
                $user = User::find($user->id);
                $user->is_delete = 0;
                $user->save();
            }else{
            //    $providedPassword = $request->password;	

            $query = User::where('mobile_number', $mobile)->first();

            // dd($query);

            $current_time = Carbon::now();
            $current_time_100 = Carbon::now();
            $time_after_100_days = $current_time_100->addDays(100);

            $package = new Package();
            $package->user_id = $query->id;
            $package->package_id = 1;
            $package->amount = 0;
            $package->starting_date = $current_time->toDateString();
            $package->ending_date = $time_after_100_days->toDateString();
            $package->notes = "free package";
            $package->payment_method = "free package";
            $package->save();
        }

        return json_encode(['success' => true,  'message' => ('Registration Successfully'), 'data' => $query,]);
        
        }
    }

    // user Edit page 
    public function userUpadate(Request $request)
    {
        $user_id = $request->id;

        $validator = Validator::make($request->all(), [
            'name'  => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {

            $user = User::find($user_id);
            $user->name = ucwords($request->name);
            $user->email = $request->email;
            $user->password = Crypt::encrypt($request->password);
            $user->company_name = $request->company_name;
            $user->city = $request->city;
            $user->is_customer = 1;
            $user->save();
        }

        return json_encode([
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'is_customer'   => $user->is_customer,
            'is_active'     => $user->is_active,
            'mobile_number' => $user->mobile_number,
            'city'          => $user->city,
            'company_name'  => $user->company_name,
            'message'       => 'Updated Successfully',
            'success'       => true,
        ], 200);


        // return json_encode(['success' => true,  'message' => ('Profile Updated Successfully'),]);

    }

    public function updateNumber(Request $request)
    {
        $user_id = $request->id;

        $validator = Validator::make($request->all(), [

            'mobile_number' => 'required',
             'mobile_number'  => 'required|Unique:users',
        ],[
                'mobile_number.unique'   => 'Account already exists for this mobile number.',
                
            ]);

        if ($validator->fails()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {
            $number = User::find($user_id);
            $number->mobile_number = $request->mobile_number;
            $number->save();
        }

        return json_encode([
            'id'            => $number->id,
            'name'          => $number->name,
            'email'         => $number->email,
            'is_customer'   => $number->is_customer,
            'is_active'     => $number->is_active,
            'mobile_number' => $number->mobile_number,
            'city'          => $number->city,
            'company_name'  => $number->company_name,
            'message'       => 'Mobile number updated Successfully',
            'success'       => true,
        ], 200);
    }


    public function yarmsrc(Request $request)
    {

        $user_id = $request->user_id;

        // $yarns_query = Yarn::leftJoin('yarn_categories', 'yarns.category_id', '=', 'yarn_categories.id')
        // ->where('yarns.user_id', $user_id)
        // ->select('yarns.*', 'yarn_categories.yarn_category');


        $yarns_query = Yarn::leftJoin('yarn_categories', 'yarns.category_id', '=', 'yarn_categories.id')
            ->where('yarns.user_id', $user_id)
            ->select('yarns.*', 'yarn_categories.yarn_category');

        if ($request->search) {

            $yarns_query->where('yarn_name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->category) {

            $yarns_query->whereHas('category', function ($query) use ($request) {
                $categaries =  $request->category;
                $jsonString = str_replace("'", '"',  $categaries);
                $array = json_decode($jsonString, true);
                $query->whereIn('category_id', $array);
            });
        }

        if ($request->priceSort) {
           $yarns_query->orderBy('yarn_rate', $request->priceSort === 'asc' ? 'asc' : 'desc');
        }

        if ($request->dateSort) {
           $yarns_query->orderBy('created_at', $request->dateSort === 'asc' ? 'asc' : 'desc');
        }

        if ($request->atoz) {
            
            $yarns_query->orderBy('yarn_name', $request->atoz === 'asc' ? 'asc' : 'desc');
            
        }

        $yarns = $yarns_query->orderBy('created_at', 'desc')->get();

        return json_encode(['success' => true,  'message' => ('Yarns Category List'), 'data' =>   $yarns,]);
    }

    public function categorysrc(Request $request)
    {
        $category = YarnCategory::where('yarn_category', 'LIKE', '%' . $request->search . '%')->orderBy('yarn_category')->get();

        return json_encode(['success' => true,  'message' => ('Yarns Category List'), 'data' =>   $category,]);
    }
    public function AddFabricDetails(Request $request)
    {
       
        $user_id = $request->user_id;
        // dd(round($request->butta_cutting_cost, 2));
        if ($request->fabric_cost_id) {
            $validator = Validator::make($request->all(), [
                'fabric_name'          => 'required',
                'warp_yarn'            => 'required',
                'weft_yarn'            => 'required|numeric|min:0|not_in:0',
                'width'                => 'required',
                'final_ppi'            => 'required',
                'fabric_category_id'   => 'required',
                'user_id'              => 'required',
            ], [
                // 'fabric_name.unique'   => 'This Fabric Cost is already saved.',
                'width.numeric' => 'The input must be a numeric value.',
                'width.min' => 'Width should be greater than 0.',
                'width.not_in' => 'PPI should be greater than 0.'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                // 'fabric_name'          => 'required',
                // 'fabric_name'          => 'required|unique:fabric_costs',

                'fabric_name' => Rule::unique('fabric_costs')->where(function ($query) use ($user_id) {
                    return $query->where('user_id', $user_id);
                }),
                'warp_yarn'            => 'required',
                'weft_yarn'            => 'required',
                'width'                => 'required|numeric|min:0|not_in:0',
                'final_ppi'            => 'required',
                'fabric_category_id'   => 'required',
                'user_id'              => 'required',
            ], [
                'fabric_name.unique'   => 'This Fabric Cost is already saved.',
                'width.numeric' => 'The input must be a numeric value.',
                'width.min' => 'Width should be greater than 0.',
                'width.not_in' => 'PPI should be greater than 0.'
            ]);
        }



        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {

            $array = $request->ppi;
            $sum = array_sum($array);

            if ($sum === 0) {
                return response()->json(['success' => false,  'message' => ('At least one PPI should be greater than 0'),]);
            }

            if ($request->fabric_cost_id) {
                $cost = FabricCost::where('id', $request->fabric_cost_id)->first();
                // dd($cost);
            } else {
                $cost = new FabricCost();
            }
            // $cost = new FabricCost();
            $cost->fabric_name    = $request->fabric_name;
            $cost->warp_yarn      = $request->warp_yarn;
            $cost->weft_yarn      = $request->weft_yarn;
            $cost->width          = round($request->width, 2);
            $cost->final_ppi      = round($request->final_ppi, 2);
            $cost->warp_wastage   = round($request->warp_wastage, 2);
            $cost->weft_wastage   = round($request->weft_wastage, 2);
            $cost->butta_cutting_cost = round($request->butta_cutting_cost, 2);
            $cost->additional_cost = round($request->additional_cost, 2);
            $cost->fabric_category_id =  $request->fabric_category_id;
            $cost->user_id = $request->user_id;
            $cost->save();

            // $arrays = $request->warp_id;
            $warps  = $request->yarn_id_warp;
            $ends   = $request->ends;
            $data    = $request->ppi;

            $ppi = array_map(function ($value) {
                return is_numeric($value) ? round($value,2) : $value;
            }, $data);


            if ($request->is_advance == 1) {
                $repeatData = $request->repeat;

                $repeat = array_map(function ($value) {
                    return is_numeric($value) ? round($value) : $value;
                }, $repeatData);

            }

            $tmp_warp_w = $request->tpm_cost_warp;

            $tmp_warp = array_map(function ($value) {
                return round($value);
            },$tmp_warp_w);


            $wefts  = $request->yarn_id_weft;
            $tmp_weft_w = $request->tpm_cost_weft;

            $tmp_weft = array_map(function ($value){
                return round($value);
            },$tmp_weft_w);

           

            foreach ($warps as $key => $warp_yid) {

                //    Get yarn data
                $yarn = Yarn::find($warp_yid);
                // dd($yarn);
                $denier = $yarn->yarn_denier;
                // $ends = $ends[$key];
                // dd($ends);
                $rate = $yarn->yarn_rate + $tmp_warp[$key];

                $weight = $ends[$key] * $denier * 110 / 900000000;
                // dd($weight);
                $roundedWeight = round($weight, 3);

                $amount = $roundedWeight * $rate;

                if ($request->fabric_cost_id) {

                    $warpss =  $request->warp_id[$key];
                    $warp = Warp::find($warpss);
                    $warp->yarn_name = $warp_yid;
                    $warp->ends = $ends[$key];
                    $warp->tpm_cost = $tmp_warp[$key];
                    $warp->fabric_cost_id = $cost->id;
                    $warp->weight =  $roundedWeight;
                    $warp->amount = $amount;
                    $warp->denier =  $denier;
                    $warp->rate = $rate;
                    $warp->save();

                } else {

                    $warp = new Warp();
                    $warp->yarn_name = $warp_yid;
                    $warp->ends = $ends[$key];
                    $warp->tpm_cost = $tmp_warp[$key];
                    $warp->fabric_cost_id = $cost->id;
                    $warp->weight =  $roundedWeight;
                    $warp->amount = $amount;
                    $warp->denier =  $denier;
                    $warp->rate = $rate;
                    $warp->save();
                }
            }
            $totalLength = 0;

            if ($request->repeat) {


                // dd($ppi[$key]);
                foreach ($wefts as $key => $weft_yid) {
                    if ($ppi[$key] == 0) {
                        $lenght = 0;
                    } else {

                        $lenght  =  $repeat[$key] / $ppi[$key];
                    }

                    $totalLength += $lenght;
                }

                // dd($totalLength);
            }

            foreach ($wefts as $key => $weft_yid) {

                //    Get yarn data
                $yarn = Yarn::find($weft_yid);

                $denier = $yarn->yarn_denier;
                // dd($denier);
                $width = $cost->width;
                // dd($width);  
                $ends = $ppi[$key];
                // dd($ends);
                $rate = $yarn->yarn_rate + $tmp_weft[$key];
                // dd($ppi[$key]);
                // Calculate weight and amount
                if ($request->is_advance == 1) {
                    // if ppi value 0
                    if ($ppi[$key] == 0) {
                        $lenght = 0;
                    } else {

                        $lenght  =  $repeat[$key] / $ppi[$key];
                    }
                    // dd($totalLength);

                    $final_ppi = $repeat[$key] / $totalLength;
                    $roundedfinal_ppi = round($final_ppi, 2);

                    // dd( $final_ppi);

                    $weight = $denier * $final_ppi *  $width * 100 / 900000000;
                } else {

                    $weight = $ppi[$key] * $denier * $width * 100 / 900000000;
                }

                $roundedWeight = round($weight, 4);
                // $roundedfinal_ppi = round($final_ppi, 2);
                //    dd( $roundedfinal_ppi);
                $amount = $roundedWeight * $rate;


                if ($request->fabric_cost_id) {
                    $weftss =  $request->weft_id[$key];
                    // dd($fabricId);
                    // foreach ($wefts as $key => $weft_id) {

                    if ($request->is_advance == 1) {
                        $weft = Weft::find($weftss);
                        // dd($weft);
                        $weft->yarn_id = $weft_yid;
                        $weft->final_ppi = $roundedfinal_ppi;
                        $weft->row_ppi = $ppi[$key];
                        $weft->repeat = $repeat[$key];
                        $weft->tpm_cost = $tmp_weft[$key];
                        $weft->lenght = $lenght;
                        $weft->fabric_cost_id = $cost->id;
                        $weft->weight =  $roundedWeight;
                        $weft->amount = $amount;
                        $weft->denier =  $denier;
                        $weft->rate = $rate;
                        $weft->is_advance = 1;
                        $weft->save();

                    } else {

                        $weft = Weft::find($weftss);
                        $weft->yarn_id = $weft_yid;
                        $weft->final_ppi = $ppi[$key];
                        $weft->row_ppi = $ppi[$key];
                        $weft->tpm_cost = $tmp_weft[$key];
                        $weft->fabric_cost_id = $cost->id;
                        $weft->weight =  $roundedWeight;
                        $weft->amount = $amount;
                        $weft->denier =  $denier;
                        $weft->is_advance = 0;
                        $weft->rate = $rate;
                        $weft->save();
                    }


                    // }
                } elseif ($request->is_advance == 1) {

                    $weft = new Weft();
                    $weft->yarn_id = $weft_yid;
                    $weft->final_ppi = $roundedfinal_ppi;
                    $weft->row_ppi = $ppi[$key];
                    $weft->repeat = $repeat[$key];
                    $weft->tpm_cost = $tmp_weft[$key];
                    $weft->lenght = $lenght;
                    $weft->fabric_cost_id = $cost->id;
                    $weft->weight =  $roundedWeight;
                    $weft->amount = $amount;
                    $weft->denier =  $denier;
                    $weft->rate = $rate;
                    $weft->is_advance = 1;
                    $weft->save();

                } else {

                    $weft = new Weft();
                    $weft->yarn_id = $weft_yid;
                    $weft->final_ppi = $ppi[$key];
                    $weft->row_ppi = $ppi[$key];
                    $weft->tpm_cost = $tmp_weft[$key];
                    $weft->fabric_cost_id = $cost->id;
                    $weft->weight =  $roundedWeight;
                    $weft->amount = $amount;
                    $weft->denier =  $denier;
                    $weft->rate = $rate;
                    $weft->save();

                }
            }

            if ($request->fabric_cost_id) {
                return response()->json(['success' => true,  'message' => ('Updated Successfully'), 'fabaric_cost_id' => $cost->id]);
            } else {
                return response()->json(['success' => true,  'message' => ('Saved Successfully'), 'fabaric_cost_id' => $cost->id]);
            }
        }
    }



    public function creatWarp(Request $request)
    {


        $user_id = $request->user_id;

        $yarns = FabricCost::where('user_id', $user_id)->latest()->first();

        return json_encode(['success' => true,  'message' => ('Yarns List'), 'data' =>  $yarns,]);
    }

    public function storeWarp(Request $request)
    {

        // $yarns = FabricCost::where('user_id', 1)->latest()->first();
        // dd($yarns->warp_yarn);
        // die;

        // $arrays = $request->fabric_cost_id;
        $arrays = $request->yarn_name;
        $ends = $request->ends;

        foreach ($arrays as $key => $fabricId) {
            $warp = new Warp();
            $warp->yarn_name = $fabricId;
            // $warp->yarn_name = $yarn_id[$key];
            $warp->ends = $ends[$key];
            $warp->fabric_cost_id = 0;
            $warp->weight = 0;
            $warp->amount = 0;
            $warp->denier = 0;
            $warp->rate = 0;
            $warp->save();
        }

        return json_encode(['success' => true,  'message' => ('Saved Successfully'),]);
    }


    public function updateWarp(Request $request)
    {
        // Convert the comma-separated IDs into an array
        $idArray = $request->ids;

        DB::beginTransaction();

        foreach ($idArray as $id) {
            // Find the Warp model by ID

            $warp = Warp::where('id', $id)->first();


            if (is_null($warp)) {
                // Handle the case when the record with a specific ID is not found
                // continue;
                dd("error");
            }

            // Extract necessary data from the existing Warp model
            $yarn_id = $warp->yarn_name;

            $ends = $warp->ends;


            // Find the Yarn model by ID
            $yarn = Yarn::find($yarn_id);
            if (empty($yarn)) {
                dd("error");
            }

            //  yarn properties
            $denier = $yarn->yarn_denier;
            // dd($denier);
            $rate = $yarn->yarn_rate;
            // dd($rate);


            // Calculate weight and amount
            $weight = $ends * $denier * 110 / 900000000;

            $roundedWeight = round($weight, 7);
            $amount = $roundedWeight * $rate;

            // Update the existing Warp model
            $warp->weight = $roundedWeight;
            $warp->amount = $amount;
            $warp->denier = $denier;
            $warp->rate   = $rate;
            $warp->save();
        }

        DB::commit();

        return json_encode(
            [
                'success' => true,
                'message' => 'Warp Updated'
            ],
            200
        );
    }

    public function updateWeft(Request $request)
    {

        $idArray = $request->ids;

        DB::beginTransaction();

        foreach ($idArray as $id) {
            // Find the Warp model by ID

            $warp = Weft::find($id);


            if (is_null($warp)) {
                // Handle the case when the record with a specific ID is not found
                continue;
            }

            // Extract necessary data from the existing Warp model
            $yarn_id = $warp->yarn_id;
            $ends = $warp->ends;

            // Find the Yarn model by ID
            $yarn = Yarn::find($yarn_id);

            if (is_null($yarn)) {
                // Handle the case when the Yarn with a specific ID is not found
                continue;
            }

            // Retrieve yarn properties
            $denier = $yarn->yarn_denier;
            $rate = $yarn->yarn_rate;

            // Calculate weight and amount
            $weight = $ends * $denier * 100 / 900000000;
            // dd($weight);
            // die;
            $roundedWeight = round($weight, 7);
            $amount = $roundedWeight * $rate;

            // Update the existing Warp model
            $warp->weight = $roundedWeight;
            $warp->amount = $amount;
            $warp->denier = $denier;
            $warp->rate = $rate;
            $warp->save();
        }

        DB::commit();

        return json_encode(
            [
                'status' => 1,
                'message' => 'Warp Updated'
            ],
            200
        );
    }

     public function getresult(Request $request, $id)
    {

        // $warplist =  Warp::where('fabric_cost_id', $id)->get();

        $warplist = Warp::select('warps.id',
                                 'warps.fabric_cost_id',
                                 'warps.tpm_cost', 
                                 'warps.ends', 
                                 'warps.weight',
                                 'yarns.yarn_name as yarn_name', 
                                 'yarns.id as yarn_id',
                                 'yarns.yarn_denier',
                                 'yarns.yarn_rate')
            ->leftjoin('yarns', 'warps.yarn_name', '=', 'yarns.id')
            ->where('fabric_cost_id', $id)
            ->get();


        $weftlist = Weft::select(
            'wefts.id',
            'wefts.fabric_cost_id',
            'wefts.final_ppi',
            'wefts.row_ppi',
            'wefts.is_advance',
            'wefts.lenght',
            'wefts.repeat',
            'wefts.rate as wefts_rate',
            'wefts.tpm_cost',
            'yarns.yarn_denier',
            'yarns.yarn_rate',
            'yarns.yarn_name as yarn_name',
            'yarns.id as yarn_id',
        )
            ->leftjoin('yarns', 'wefts.yarn_id', '=', 'yarns.id')
            ->where('fabric_cost_id', $id)
            ->get();




        $general = FabricCost::select('fabric_costs.*', 'fabric_categories.fabric_category as category_name')
            ->leftJoin('fabric_categories', 'fabric_costs.fabric_category_id', '=', 'fabric_categories.id')
            ->where('fabric_costs.id', $id)
            ->where('fabric_costs.user_id', $request->user_id)
            ->first();



        $totalWarp           = 0;
        $totalWeft           = 0;
        $amount_warp         = 0;
        $amount_weft         = 0;
        $final_ppi           = 0;
        $ends                = 0;
        $lenght              = 0;
        $cut_in_inch_advance = 0;
        $repeat              = 0;
        $adv                 = 0;
        $ppi_r               = 0;

        foreach ($warplist as $list) {
            $id = $list->id;
            $list->denier =  $list->yarn_denier;
            $list->weight = round($list->WarpWeight(), 4);
            $list->weight = number_format(round($list->weight, 4), 4);
            // $list->amount = round($list->amount);
            $totalWarp += $list->weight;
            $ends += $list->ends;
            $list->tpm = $list->yarn_rate + $list->tpm_cost;
            $list->amount = round($list->tpm * $list->weight, 2);
            $amount_warp += $list->amount;

        }

        // dd($list->yarn_rate);


        foreach ($weftlist as $list) {

            // dd($list->final_ppi);
            $list->yarn_denier = $list->yarn_denier;

            $adv = $list->is_advance;

            $final_ppi   += round($list->final_ppi, 2);

            $ppi_r       = round($list->final_ppi, 2);
            if ($adv == 1) {
                $list->weight = round($list->yarn_denier  * $ppi_r *  $general->width * 100 / 900000000, 4);
                $list->rate   = round($list->yarn_rate * $list->weight, 2);
            } else {
                $list->weight = round($list->yarn_denier  * $list->final_ppi *  $general->width * 100 / 900000000, 4);
                $list->rate   = round($list->yarn_rate * $list->weight, 2);
            }

            $totalWeft    += $list->weight;

            if ($adv == 1) {
                $cut_in_inch_advance += $list->repeat / $list->final_ppi;
            }

            $lenght += $list->lenght;
            $repeat += $list->repeat;
            $list->lenght_w = round($list->lenght, 10);

            $list->lenght = round($list->lenght, 2);
            if ($adv == 1) {
                if ($list->lenght == 0) {
                    $list->ppi = 0;
                } else {
                    $list->ppi = $list->repeat / $list->lenght_w;
                }
            }
            // dd($list->repeat/$list->lenght);
            // dd( $list->ppi);
            $list->rate = round($list->rate, 2);
            // $amount_weft +=  $list->rate;
            $list->repeat = round($list->repeat, 2);
            $list->ppi = round($list->final_ppi, 2);
            $list->row_ppi = round($list->row_ppi, 2);
            $list->final_ppi = round($list->ppi, 2);
            $list->tpm = $list->tpm_cost + $list->yarn_rate;
            $list->amount = round($list->tpm * $list->weight, 2);
            $amount_weft += $list->amount;

        }

        // dd($list->tpm); 



        $cut_in_inch = round($lenght, 2);



        // ADVANCE CALUCLATION
        if ($adv == 1) {

            $total_final_ppi_advance = $repeat /  $cut_in_inch;
            $total_final_ppi_round = round($total_final_ppi_advance, 2);
        }


        // dd($total_final_ppi_round);
        // final cost per meter
        // if ($adv == 1) {
        //     $Production_Cost_on_Total_Final_PPI_advance =  $total_final_ppi_round * $general->final_ppi;
        //     $Production_Cost_on_Total_Final_PPI_advance_r = round($Production_Cost_on_Total_Final_PPI_advance, 2);
        // }

        $yarn_cost =  $amount_warp + $amount_weft;

        $yarn_cost_r = round($yarn_cost, 2);

        $total = $totalWarp + $totalWeft;


        $totals = round($total, 4);

        // dd($general->final_ppi);

        $Production_Cost_on_Total_Final_PPI = $general->final_ppi * $final_ppi;
        // dd($Production_Cost_on_Total_Final_PPI);

        $Wastage_on_Warpt = $general->warp_wastage * $amount_warp / 100;
        $Wastage_on_Warp_Amount = round($Wastage_on_Warpt, 2);

        // dd($amount_weft);
        $Wastage_on_Weft = $general->weft_wastage * $amount_weft / 100;
        $Wastage_on_Weft_Amount = round($Wastage_on_Weft, 2);

        // dd($Wastage_on_Weft_Amount);
        $Butta_Cutting_Cost = $general->butta_cutting_cost;

        // Advance
        if ($adv == 1) {
            $Final_Cost_advance =  $yarn_cost  + $Production_Cost_on_Total_Final_PPI +  $Wastage_on_Warp_Amount + $Wastage_on_Weft_Amount + $Butta_Cutting_Cost
                + $general->additional_cost;

            // dd($Final_Cost_advance);

            $Final_Cost_advance_per_meter = round($Final_Cost_advance, 2);
            $cut_in_inch_r = round($cut_in_inch / 39.37, 2);
            // dd($cut_in_inch);

            $final_Cost_Per_Piece_advance = $Final_Cost_advance_per_meter * $cut_in_inch_r;

            $final_Cost_Per_Piece_advance_r = round($final_Cost_Per_Piece_advance, 2);
            // dd($final_Cost_Per_Piece_advance_r); 
        }


        $Final_Cost_basic = $yarn_cost  + $Production_Cost_on_Total_Final_PPI +  $Wastage_on_Warp_Amount + $Wastage_on_Weft_Amount + $Butta_Cutting_Cost
            + $general->additional_cost;

        $Final_Cost_Per_Metre_b = round($Final_Cost_basic, 2);
        // dd($Final_Cost_Per_Metre_b);f

        $cut_in_mtr =     $cut_in_inch / 39.37;

        $cut_in_metre = round($cut_in_mtr, 2);
        // dd($cut_in_metre);

        $Final_Cost =  $Final_Cost_Per_Metre_b * $cut_in_metre;

        // dd($Final_Cost);
        $Final_Cost_Per_Piece = round($Final_Cost, 2);
        // dd($Final_Cost_Per_Piece);



        $calculationList = [];
        $calculationList[] = array('name' => 'Yarn Cost', 'value' => $yarn_cost_r);
        // if ($adv == 1) {
        //     $calculationList[] = array('name' => $general->final_ppi . ' Production Cost on Total Final PPI', 'value' => $Production_Cost_on_Total_Final_PPI_advance_r);
        // } else {
        //     $calculationList[] = array('name' => $general->final_ppi . ' Production Cost on Total Final PPI', 'value' => $Production_Cost_on_Total_Final_PPI);
        // }
        $calculationList[] = array('name' => $general->final_ppi . ' Production Cost on Total Final PPI', 'value' => $Production_Cost_on_Total_Final_PPI);

        $calculationList[] = array('name' => $general->warp_wastage . ' % Wastage on Warp Amount', 'value' => $Wastage_on_Warp_Amount);
        $calculationList[] = array('name' => $general->weft_wastage . ' % Wastage on Weft Amount', 'value' => $Wastage_on_Weft_Amount);
        $calculationList[] = array('name' => 'Butta Cutting Cost', 'value' => $Butta_Cutting_Cost);
        $calculationList[] = array('name' => 'Any Additional Cost', 'value' => $general->additional_cost);
        if ($adv == 1) {
            $calculationList[] = array('name' => 'Final Cost Per Metre', 'value' => $Final_Cost_advance_per_meter);
            $calculationList[] = array('name' => 'Final Cost Per Piece', 'value' => $final_Cost_Per_Piece_advance_r);
        } else {
            $calculationList[] = array('name' => 'Final Cost Per Metre', 'value' => $Final_Cost_Per_Metre_b);
            $calculationList[] = array('name' => 'Final Cost Per Piece', 'value' => $Final_Cost_Per_Piece);
        }


        $weightDetails = [];
        $weightDetails[] = array('name' => 'Warp Weight (kg)', 'value' => number_format(round($totalWarp, 4), 4));
        $weightDetails[] = array('name' => 'Weft Weight (kg)', 'value' => number_format(round($totalWeft, 4), 4));
        $weightDetails[] = array('name' => 'Total Weight', 'value' => number_format(round($totals, 4), 4));

        $otherDetails = [];
        $otherDetails[] = array('name' => 'Total Ends (Taar)', 'value' => $ends);
        $otherDetails[] = array('name' => 'Width (Inch)', 'value' => $general->width);
        // if ($adv == 1) {
        //     $otherDetails[] = array('name' => 'Total Final PPI', 'value' => $total_final_ppi_round);
        // } else {
        //     $otherDetails[] = array('name' => 'Total Final PPI', 'value' => $final_ppi);
        // }
        $otherDetails[] = array('name' => 'Total Final PPI', 'value' => $final_ppi);
        $otherDetails[] = array('name' => 'Cut in Inch', 'value' => $cut_in_inch);
        $otherDetails[] = array('name' => 'Cut in Metre', 'value' =>  $cut_in_metre);

        return response()->json([

            'success' => true, 'message' => 'Warp List', 'data' => [],
            'warplist' => $warplist,
            'weftlist' => $weftlist,
            'general' => $general,
            'calculation' => $calculationList,
            'weightDetails' => $weightDetails,
            'otherDetails' => $otherDetails,

        ]);
    }




   public function getFabricCost(Request $request)
    {

        $user_id = $request->user_id;
        $fabricCostList = FabricCost::select('fabric_costs.*', 'fabric_categories.fabric_category as category_name')
            ->leftJoin('fabric_categories', 'fabric_costs.fabric_category_id', '=', 'fabric_categories.id')
            ->where('fabric_costs.user_id', $user_id);

        if ($request->search) {
            $fabricCostList->where('fabric_name', 'LIKE', '%' . $request->search . '%');
        }
        if ($request->atoz) {
            if ($request->atoz === 'asc') {
                $fabricCostList->orderBy('fabric_name', 'asc');
            } elseif ($request->dateSort === 'desc') {
                $fabricCostList->orderBy('fabric_name', 'desc');
            }
        }

        if ($request->category) {

            $fabricCostList->whereHas('category', function ($query) use ($request) {
                $categaries =  $request->category;
                $jsonString = str_replace("'", '"',  $categaries);
                $array = json_decode($jsonString, true);
                $query->whereIn('fabric_category_id', $array);
            });
        }


        if ($request->dateSort) {
            if ($request->dateSort === 'asc') {
                $fabricCostList->orderBy('created_at', 'asc');
            } elseif ($request->dateSort === 'desc') {
                $fabricCostList->orderBy('created_at', 'desc');
            }
        }



        $fabricCostList =  $fabricCostList->orderBy('id', 'DESC')->get();

        foreach ($fabricCostList as $fabiteam) {

            $warplist = Warp::select('warps.id', 
                                     'warps.fabric_cost_id',
                                     'warps.ends',
                                     'warps.tpm_cost',
                                     'yarns.yarn_name as yarn_name', 
                                     'yarns.id as yarn_id', 
                                     'yarns.yarn_denier', 
                                     'yarns.yarn_rate')
                ->leftjoin('yarns', 'warps.yarn_name', '=', 'yarns.id')
                ->where('fabric_cost_id', $fabiteam->id)
                ->get();



            $weftlist = Weft::select(
                'wefts.id',
                'wefts.fabric_cost_id',
                'wefts.final_ppi',
                'wefts.is_advance',
                'wefts.lenght',
                'wefts.repeat',
                'wefts.rate',
                'wefts.tpm_cost',
                'yarns.yarn_denier',
                'yarns.yarn_rate',
                'yarns.yarn_name as yarn_name',
                'yarns.id as yarn_id',
            )
                ->leftjoin('yarns', 'wefts.yarn_id', '=', 'yarns.id')
                ->where('fabric_cost_id', $fabiteam->id)
                ->get();



            // $general = FabricCost::find($id);

            $general = $fabiteam;


            // dd($result);

            $totalWarp   = 0;
            $totalWeft   = 0;
            $amount_warp = 0;
            $amount_weft = 0;
            $final_ppi   = 0;
            $ends        = 0;
            $lenght      = 0;
            $cut_in_inch_advance = 0;
            $repeat      = 0;
            $adv         = 0;

            foreach ($warplist as $list) {
                $id = $list->id;
                $list->denier =  $list->yarn_denier;
                $list->weight = round($list->ends * $list->yarn_denier * 110 / 900000000, 4);
                // $list->amount = round($list->yarn_rate * $list->weight, 2);
                $totalWarp += $list->weight;
                $ends += $list->ends;
                $list->tpm_cost = $list->tpm_cost;
                $list->tpm = $list->yarn_rate + $list->tpm_cost;
                $list->amount = round($list->tpm * $list->weight, 2);
                $amount_warp += $list->amount;

            }


            foreach ($weftlist as $list) {
                // dd($list->final_ppi);
                $list->yarn_denier = $list->yarn_denier;
                $final_ppi   += $list->final_ppi;
                $ppi          = $list->final_ppi;
                if ($adv == 1) {
                    $list->weight = round($list->yarn_denier  * $ppi *  $general->width * 100 / 900000000, 4);
                    $list->rate   = round($list->yarn_rate * $list->weight, 2);
                } else {
                    $list->weight = round($list->yarn_denier  * $list->final_ppi *  $general->width * 100 / 900000000, 4);
                    $list->rate   = round($list->yarn_rate * $list->weight, 2);
                }

                $list->repeat =  $list->repeat;
                $totalWeft    += $list->weight;
                // dd($list->final_ppi);
                if ($adv == 1) {
                    if ($list->final_ppi == 0) {
                        $cut_in_inch_advance = 0;
                    } else {
                        $cut_in_inch_advance += $list->repeat / $list->final_ppi;
                    }
                }

                $lenght += $list->lenght;
                $repeat += $list->repeat;
                $adv     = $list->is_advance;
                $list->lenght = round($list->lenght, 2);
                if ($adv == 1) {
                    if ($list->lenght == 0) {
                        $list->ppi = 0;
                    } else {
                        $list->ppi = $list->repeat / $list->lenght;
                    }
                }
                $list->rate = round($list->rate, 2);
                // $amount_weft +=  $list->rate;
                $list->repeat = round($list->repeat, 2);
                $list->final_ppi = round($list->final_ppi, 2);
                $list->row_ppi = round($list->row_ppi, 2);
                $list->tpm = $list->yarn_rate + $list->tpm_cost;
                $list->amount = round($list->tpm * $list->weight, 2);
                $amount_weft += $list->amount;
            }




            $cut_in_inch = round($lenght, 2);
            // $list->fabric_cost = $cut_in_inch;


            if ($adv == 1) {

                if ($cut_in_inch == 0) {
                    $total_final_ppi_advance = 0;
                } else {

                    $total_final_ppi_advance = $repeat / $cut_in_inch;
                }
                $total_final_ppi_round = round($total_final_ppi_advance, 2);
            }


            // final cost per meter
            if ($adv == 1) {
                $Production_Cost_on_Total_Final_PPI_advance = $total_final_ppi_round * $general->final_ppi;
                // dd($Production_Cost_on_Total_Final_PPI_advance);
                $Production_Cost_on_Total_Final_PPI_advance_r = round($Production_Cost_on_Total_Final_PPI_advance, 2);
            }


            $yarn_cost =  $amount_warp + $amount_weft;

            $yarn_cost_r = round($yarn_cost, 2);


            $total = $totalWarp + $totalWeft;



            $totals = round($total, 4);

            $Production_Cost_on_Total_Final_PPI = round($general->final_ppi * $final_ppi , 2);
            

            $Wastage_on_Warpt = $general->warp_wastage * $amount_warp / 100;
            $Wastage_on_Warp_Amount = round($Wastage_on_Warpt, 2);

            $Wastage_on_Weft = $general->weft_wastage * $amount_weft / 100;
            $Wastage_on_Weft_Amount = round($Wastage_on_Weft, 2);

            $Butta_Cutting_Cost = $general->butta_cutting_cost;

            // Advance

            if ($adv == 1) {
                $Final_Cost_advance =  $yarn_cost_r  + $Production_Cost_on_Total_Final_PPI_advance +  $Wastage_on_Warp_Amount + $Wastage_on_Weft_Amount + $Butta_Cutting_Cost
                    + $general->additional_cost;
                    
                $Final_Cost_advance_per_meter = round($Final_Cost_advance, 2);

                // dd($Final_Cost_advance_per_meter);
                $final_Cost_Per_Piece_advance = $Final_Cost_advance_per_meter * $cut_in_inch / 39.37;

                // $final_Cost_Per_Piece_advance_r = round($final_Cost_Per_Piece_advance, 2);
                // dd($final_Cost_Per_Piece_advance_r);
            }


            $Final_Cost_basic = $yarn_cost  + $Production_Cost_on_Total_Final_PPI +  $Wastage_on_Warp_Amount + $Wastage_on_Weft_Amount + $Butta_Cutting_Cost
                + $general->additional_cost;

            $Final_Cost_Per_Metre_b = round($Final_Cost_basic, 2);
            // dd($Final_Cost_Per_Metre_b);


            // dd($cut_in_inch);


            $cut_in_mtr =     $cut_in_inch / 39.37;

            $cut_in_metre = round($cut_in_mtr, 2);

            $Final_Cost =  $Final_Cost_Per_Metre_b * $cut_in_metre;

            $Final_Cost_Per_Piece = round($Final_Cost, 2);


            if ($adv == 1) {
                $fabiteam->fabric_cost = $Final_Cost_advance_per_meter;
            } else {
                $fabiteam->fabric_cost =  $Final_Cost_Per_Metre_b;
            }

            /* ------------------------------ */
        }

        if ($request->priceSort) {

            if ($request->priceSort === 'asc') {
                $fabricCostList = collect($fabricCostList)->sortBy('fabric_cost', SORT_REGULAR, false)->values();
            } elseif ($request->priceSort === 'desc') {
                $fabricCostList = collect($fabricCostList)->sortBy('fabric_cost', SORT_REGULAR, true)->values();
            }
        }

        return response()->json(['success' => true, 'message' => 'Fabric Cost List',  'fabric_cost_list' => $fabricCostList]);
    }
    public function validationFabricDetails(Request $request)
    {  
        $user_id = $request->user_id;

        $validator = Validator::make($request->all(), [
            // 'fabric_name'          => 'required',
            // 'fabric_name'          => 'required|unique:fabric_costs',
             'fabric_name' => Rule::unique('fabric_costs')->where(function ($query) use ($user_id) {
                return $query->where('user_id', $user_id);
            }),

        ]);

        if ($validator->fails()) {
            return json_encode(['success' => false,  'message' => ('This Fabric cost is already saved.'),]);
        } else {
            return json_encode(['success' => true,  'message' => ('This Fabric Cost has been  saved.'),]);
        }
    }

    public function fabricCostDelete($id)
    {

        $fabriccost = FabricCost::find($id);
        $fabricCost_warp = Warp::where('fabric_cost_id', $id)->get();
        $fabricCost_weft = Weft::where('fabric_cost_id', $id)->get();


        if (!$fabriccost && !$fabricCost_warp &&  !$fabricCost_weft) {
            return json_encode(['success' => false,  'message' => ('Delete opration failed.'),]);
        } else {

            $fabriccost->delete();

            foreach ($fabricCost_warp as $warp) {
                $warp->delete();
            }

            foreach ($fabricCost_weft as $weft) {
                $weft->delete();
            }

            return json_encode(['success' => true,  'message' => ('Deleted Successfully'),]);
        }
    }

    // PAckages
    public function userlist(Request $request)
    {

        $user = User::where('is_customer', 1)->get();
        //    dd($user);
        return json_encode(['success' => true, 'message' => 'User List',  'user_list' => $user]);
    }

    public function userpackage(Request $request)
    {
        $startDate = Carbon::now();

        $user_id = $request->user_id;
        $usersPackage = Package::where('user_id', $user_id)
            ->where('starting_date', '<=', $startDate)
            ->where('ending_date', '>', $startDate)->first();
           
         if(!$usersPackage){
            $usersPackage = Package::where('user_id', $user_id)->latest()->first();
        }


        $endDate = $usersPackage->ending_date;

        $currentDate = Carbon::now();

        $daysRemaining = $currentDate->diffInDays($endDate);    

      
        $packages = Package::join('users','packages.user_id', '=', 'users.id')
                    ->join('packagelist', 'packages.package_id', '=', 'packagelist.id')
                    ->where('packages.user_id', $user_id)
                    ->select('packages.starting_date',
                             'packages.ending_date',
                             'users.name',
                             'packagelist.name as package_name', 
                             'packagelist.amount as package_amount',
                             'packagelist.amount as package_amount')->get();


        return response()->json(['success' => true,
                                 'message' => 'User List',
                                 'User Packages' => $usersPackage,
                                 'Remaning Days' => $daysRemaining,
                                 'packages' => $packages,
                                'Package_visible' => false]);
                   
    }

    public function AddPackage(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'user_id'  => 'required',
            'package_id'  => 'required',
            'amount'  => 'required',
            'starting_date'  => 'required',
            'ending_date'  => 'required',
            'notes'  => 'required',
            'payment_method'  => 'required',

        ]);

        if ($validator->failed()) {
            return json_encode([
                'success' => false,
                'message' => $validator->messages()->first()
            ], 422);
        } else {
            $user_id = $request->user_id;
            $current_time = Carbon::now();


            $current_time = Carbon::now();
            $time_after_100_days = $current_time->addDays(100);

            $package = new Package();
            $package->user_id = $user_id;
            $package->package_id = 1;
            $package->amount = 0;
            $package->starting_date = $current_time->toDateString();
            $package->ending_date = $time_after_100_days->toDateString();
            $package->notes = "free package";
            $package->payment_method = "free package";
            $package->save();
        }

        return json_encode(['success' => true,  'message' => ('Saved Successfully'), 'Package id' => $package->id]);
    }

    public function packagelist()
    {
        $package = Packagelist::where('amount', '>', 0)->get();
        $paymentDetails = PaymentDetail::all();

        return json_encode(['success' => true, 'package' =>  $package, 'paymentDetails' =>  $paymentDetails,]);
    }

    public function uploadPhoto(Request $request)
    {
        if($request->hasFile('photo')) {
            $file = $request->file('photo');
            $product_photo = time() . $file->getClientOriginalName();
            $file->move(public_path() .  "/assets/fabric/", $product_photo);
        }

        $fabric = FabricCost::find($request->id);
        $fabric->photo = $product_photo;
        $fabric->save();

        return json_encode(['success' => true,  'message' => ('Image Uploaded Successfully')]);
    }

    public function deletePhoto(Request $request)
    {
        $product_photo = 'default.jpg';

        $fabric = FabricCost::find($request->id);

        if ($fabric) {
            $imagePath = "/assets/fabric/{$fabric->photo}";

            if (File::exists($imagePath)) {
                // Attempt to delete the image file
                try {
                    File::delete($imagePath);
                } catch (\Exception $e) {
                    return json_encode(['error' => 'Error deleting image file'], 500);
                }
            }

            $fabric->photo = $product_photo;
            $fabric->save();

            return json_encode(['success' => true,  'message' => ('image deleted successfully')]);
        }
    }

    public function deleteUser(Request $request)
    {
        $user_id = $request->id;
        $current_time = Carbon::now();
        
        $delete = User::find($user_id);
        $delete->is_delete = 0;
        $delete->updated_at = $current_time;
        $delete->save();

        return json_encode(['success' => true,  'message' => ('User deleted successfully')]);
    }

    // public function getDetails(Request $request)
    // {

    //     $user_id   = $request->id;
    //     $device_id = $request->device_id;
    //     // dd($device_id);

    //     $query = User::where('id', $user_id)->first();

    //     if ($query->device_id == $device_id) {
    //         return json_encode([
    //             'message'       => 'Login Successful',
    //             'id'            => $query->id,
    //             'name'          => $query->name,
    //             'email'         => $query->email,
    //             'is_customer'   => $query->is_customer,
    //             'is_active'     => $query->is_active,
    //             'is_delete'     => $query->is_delete,
    //             'mobile_number' => $query->mobile_number,
    //         ], 200);
    //     } else {

    //         return json_encode(['success' => false,  'message' => ('Your session has expired. Please login')]);
    //     }
    // }
    
    public function getDetails(Request $request)
    {
           $user_id   = $request->id;
           $current_time = Carbon::now();

        
            $user = User::find($user_id);
            $user->updated_at = $current_time;
            $user->save();

        $device_id = $request->device_id;

        $query = User::where('id', $user_id)->first();

        $packagies = Package::where('user_id', $query->id)->get();

        foreach ($packagies as $package) {
            $endingDate = Carbon::parse($package->ending_date);

            $is_expired = $endingDate->isPast() ? 0 : 1;
        }

        if ($query->device_id == $device_id) {
            return response()->json([
                'message'       => 'Login Successfully',
                'id'            => $query->id,
                'name'          => $query->name,
                'email'         => $query->email,
                'is_customer'   => $query->is_customer,
                'is_active'     => $query->is_active,
                'is_delete'     => $query->is_delete,
                'mobile_number' => $query->mobile_number,
                'is_expired'    => $is_expired
            ], 200);
        } else {

            return response()->json(['success' => false,  'message' => ('Your session has expired. Please login again')]);
        }
    }


    public function deleteUserData(Request $request)
    {
        $user_id = $request->user_id;

        $yarns = Yarn::where('user_id', $user_id)->get();
        foreach ($yarns as $yarn) {
            $yarn->delete();
        }

        $fabric_c = FabricCategory::where('user_id', $user_id)->get();
        foreach ($fabric_c as $fabric) {
            $fabric->each->delete();
        }

        $yarn_c = YarnCategory::where('user_id', $user_id)->get();
        foreach ($yarn_c as $yarn) {
            $yarn->delete();
        }

        $fabric_id = FabricCost::where('user_id', $user_id)->get();
        // dd($fabric_id);

        foreach ($fabric_id  as $id) {
            $warp = Warp::where('fabric_cost_id', $id->id)->get();
            $warp->each->delete();

            $weft = Weft::where('fabric_cost_id', $id->id)->get();
            $weft->each->delete();
        }

        $fabrciCosts = FabricCost::where('user_id', $user_id)->get();

        foreach ($fabrciCosts as $fabric) {
            $fabric->delete();
        }

        return json_encode(['success' => false,  'message' => ('Your Account deleted')]);
    }
}
