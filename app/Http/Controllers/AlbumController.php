<?php

namespace App\Http\Controllers;

use DB;
use App\Album;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\AlbumCollection;
use App\Http\Requests\StoreAlbumRequest;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $songName = $request->input('songName');

        $albums = Album::with('songs')
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'LIKE', "%$name%");
            })
            ->whereHas('songs', function($query) use ($songName) {
                $query->where('name', 'LIKE', "%$songName%");
            })
            ->get();

        return new AlbumCollection($albums);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlbumRequest $request)
    {
        try {
            $album = Album::create($request->all());

            return response()->json([
                'id' => $album->id,
                'created_at' => $album->created_at,
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // load both authors and publisher attributes
            $album = Album::with('songs')->find($id);
            if(!$album) throw new ModelNotFoundException;

            return new AlbumResource($album);
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
            $album = Album::find($id);

            if(!$album) throw new ModelNotFoundException; 

            $album->update($request->all());
            $album->songs()->sync($request->songs);

            return response()->json(null, 204);
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
            $album = Album::find($id);

            if (!$album) throw new ModelNotFoundException;

            $album->delete();

            return response()->json('Data deleted successfully', 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage() ], 404);
        }
    }
}
