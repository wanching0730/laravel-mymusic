<?php

namespace App\Http\Controllers;

use DB;
use App\Song;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;
use App\Http\Resources\SongCollection;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\ArtistCollection;
use App\Http\Requests\StoreSongRequest;
use Illuminate\Validation\ValidationException;


class SongController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $genre = $request->input('genre');
        $origin = $request->input('origin');
        $artistName = $request->input('artistName');
        $albumName = $request->input('albumName');

       $songs = Song::with(['artist','albums'])
           ->when($name, function ($query) use ($name) {
               return $query->where('name', 'LIKE', "%$name%");
           })
           ->when($genre, function ($query) use ($genre) {
               return $query->where('genre', 'LIKE', "%$gender%");
           })
           ->when($origin, function ($query) use ($origin) {
               return $query->where('origin', 'LIKE', "%$origin%");
           })
           ->whereHas('artist', function($query) use ($artistName) {
               $query->where('name', 'LIKE', "%$artistName%");
           })
           ->whereHas('albums', function($query) use ($albumName) {
               $query->where('name', 'LIKE', "%$albumName%");
           })
           ->get();

       return new SongCollection($songs);
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
    public function store(StoreSongRequest $request)
    {
        try {
            $song = Song::create($request->all());
            $song->artist_id = $request->artist_id;

            // DB::transaction(function() use($song, $request) {
            //     $song->saveOrFail();
            //     $song->albums()->sync($request->albums);
            // });

            return response()->json([
                'id' => $song->id,
                'created_at' => $song->created_at
            ], 201);
        } catch (ValidationException $ex) {
            return response()->json(['errors' => $ex->errors()], 422);
        } catch(\Exception $ex) {
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
            $song = Song::with('artist')->with('albums')->find($id);

            if(!$song) throw new ModelNotFoundException;

            return new SongResource($song);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage()], 404);
        } catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
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
            $song = Song::find($id);

            if (!$song) throw new ModelNotFoundException;

            $song->update($request->all());
            $song->albums()->sync($request->albums);

            return response()->json('Data updated successfully', 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage()], 404);
        } catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
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
            $song = Song::find($id);

            if (!$song) throw new ModelNotFoundException;

            $song->delete();

            return response()->json('Data deleted successfully', 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage() ], 404);
        }
    }
}
