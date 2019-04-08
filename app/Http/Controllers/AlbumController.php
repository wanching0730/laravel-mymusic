<?php

namespace App\Http\Controllers;

use DB;
use App\Album;
use Illuminate\Support\Facades\Gate;
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
    public function index()
    {
        $albums = Album::with('songs', 'songs.artist', 'user')
            ->orderBy('creationDate', 'desc')
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

            DB::transaction(function() use($album, $request) {
                $album->saveOrFail();
                $album->songs()->sync($request->songs);
            });

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
            $album = Album::with('songs', 'songs.artist', 'user')->find($id);
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
    public function update(StoreAlbumRequest $request, $id)
    {
        try {
            $album = Album::find($id);

            if (Gate::allows('update-album', $album) || $album->user_id == NULL) {

                if(!$album) throw new ModelNotFoundException; 
    
                $album->update($request->all());
                $album->songs()->sync($request->songs);
    
                return response()->json('Data updated successfully', 200);
            } else {
                return response()->json('Data only can be updated by the album owner', 500);
            }
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

            if (Gate::allows('delete-album', $album) || $album->user_id == NULL) {

                if (!$album) throw new ModelNotFoundException;

                $album->songs()->detach();
                $album->delete();

                return response()->json('Data deleted successfully', 200);
            } else {
                return response()->json('Data only can be deleted by the album owner', 500);
            }
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage() ], 404);
        }
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $songName = $request->input('songName');

        $albums = Album::with('songs', 'songs.artist', 'user')
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'LIKE', "%$name%");
            })
            ->whereHas('songs', function($query) use ($songName) {
                $query->where('name', 'LIKE', "%$songName%");
            })
            ->orderBy('creationDate', 'desc')
            ->get();

        return new AlbumCollection($albums);
    }
}
