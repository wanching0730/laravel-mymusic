<?php

namespace App\Http\Controllers;

use DB;
use Bouncer;
use App\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return UserCollection
     */
    public function index()
    {
        $users = User::with('albums')->get();

        return new UserCollection($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $password = $request->password;
            $bcryptPassword = bcrypt($password);
            $request->merge([ 'password' => $bcryptPassword ]);
            $user = User::create($request->all());

            // Assign role for new user
            $role = $request->role;
            if ($role != 'admin' && $role != 'member' && $role != 'guest') {
                throw new HttpResponseException(response()->json([
                    'errors' => 'Only Admin, Member, Staff roles are allowed'
                ], 500));
            }
            Bouncer::assign($role)->to($user);

            return response()->json([
                'id' => $user->id,
                'created_at' => $user->created_at,
            ], 201);  

        } catch (ValidationException $ex) {
            return response()->json(['errors' => $ex->errors()], 422);
        } catch (\Exception $ex) {
            return response()->json(['errors' => $ex->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show($id)
    {
        try {
            // load both authors and publisher attributes
            $user = User::with('albums')->find($id);
            
            if(!$user) throw new ModelNotFoundException;

            return new UserResource($user);
        } catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        try {
            $user = User::find($id);

            if(!$user) throw new ModelNotFoundException; 

            $password = $request->password;
            $bcryptPassword = bcrypt($password);
            $request->merge([ 'password' => $bcryptPassword ]);
            $user->update($request->all());

            $role = $request->role;
            if ($role != 'admin' && $role != 'member' && $role != 'guest') {
                throw new HttpResponseException(response()->json([
                    'errors' => 'only Admin, Member, Guest roles are allowed'
                ], 500));
            }

            $user->roles()->detach();
            Bouncer::assign($role)->to($user);

            return response()->json('Data updated successfully', 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage()], 404);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);

            if (!$user) throw new ModelNotFoundException;

            if($user->albums) {
                foreach ($user->albums as $album) {
                    $album->user()->dissociate();
                    $album->save();
                }
            }
            
            $user->delete();

            return response()->json('Data deleted successfully', 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage() ], 404);
        }
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $albumName = $request->input('albumName');

        $users = User::with('albums')
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'LIKE', "%$name%");
            })
            ->whereHas('albums', function($query) use ($albumName) {
                $query->where('name', 'LIKE', "%$albumName%");
            })
            ->get();

        return new UserCollection($users);
    }
}
