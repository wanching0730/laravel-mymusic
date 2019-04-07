<?php

namespace App\Http\Controllers;

use DB;
use App\Artist;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\ArtistCollection;
use App\Http\Requests\StoreArtistRequest;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ArtistCollection
     */
    public function index()
    {
        $artists = Artist::with('songs')->get();

        return new ArtistCollection($artists);
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
    public function store(StoreArtistRequest $request)
    {
        try {
            $artist = Artist::create($request->all());

            return response()->json([
                'id' => $artist->id,
                'created_at' => $artist->created_at,
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
     * @return ArtistResource
     */
    public function show($id)
    {
        try {
            // load both authors and publisher attributes
            $artist = Artist::with('songs')->find($id);
            if(!$artist) throw new ModelNotFoundException;

            return new ArtistResource($artist);
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
            $artist = Artist::find($id);

            if(!$artist) throw new ModelNotFoundException; 

            $artist->update($request->all());

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
            $artist = Artist::find($id);

            if (!$artist) throw new ModelNotFoundException;

            $artist->delete();

            return response()->json('Data deleted successfully', 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage() ], 404);
        }
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $nationality = $request->input('nationality');
        $songName = $request->input('songName');

        $artists = Artist::with('songs')
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'LIKE', "%$name%");
            })
            ->when($nationality, function ($query) use ($nationality) {
                return $query->where('genre', 'LIKE', "%$nationality%");
            })
            ->whereHas('songs', function($query) use ($songName) {
                return $query->where('name', 'LIKE', "%$songName%");
            })
            ->get();

        return new ArtistCollection($artists);
    }
}
