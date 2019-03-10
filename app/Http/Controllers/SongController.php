<?php

namespace App\Http\Controllers;

use App\Song;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;
use App\Http\Resources\SongCollection;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\ArtistCollection;
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

       $songs = Song::with(['artists','album'])
                ->when($name, function($query) use ($name) {
                    return $query->where('name',$name);
                })
                ->when($genre, function($query) use ($genre) {
                    return $query->where('genre',$genre);
                })
                ->when($origin, function($query) use ($origin) {
                    return $query->where('origin',$origin);
                })
                ->get();

        // $songs = Song::with(['artists' => function ($query) use ($name) {
        //     $query->where('name', 'like', '%$name%');
        // }])->get();

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
    public function store(Request $request)
    {
        try {
            $song = Song::create($request->all());
            $song->artists()->sync($request->artists);

            return response()->json([
                'id' => $song->id,
                'created_at' => $song->created_at,
            ], 201);
        } catch (ValidationException $ex) {
            return response()->json(['errors' => $ex->errors()], 422);
        } catch(\Exception $ex) {
            return response()->json(['errors' => $ex->message()], 422);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
