<?php

namespace App\Http\Controllers\Api;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Resources\NoteResource;
use App\Http\Requests\NoteCreateRequest;
use App\Http\Requests\NoteUpdateRequest;
use App\Http\Controllers\ResponseWithHttpStatusController;

class NoteController extends ResponseWithHttpStatusController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)->latest()->get();

        return NoteResource::collection($notes)->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NoteCreateRequest $request)
    {
        $note = Note::create($request->only(['title', 'description']));
        //save the file on the collection
        //To upload in S3 bucket use the disks but for now let's use the local
        $note->addMedia($request->file('note_file'))->toMediaCollection();
        //$note->addMedia($request->file('note_file'), 's3')->toMediaCollection();

        return $this->responseCreated($note->toArray(), 'Note has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param  Note  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        return (new NoteResource($note))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Note  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NoteUpdateRequest $request, Note $note)
    {
        $note->update($request->only(['title', 'description']));

        return $this->responseOk('Note has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return $this->responseNoContent();
    }
}
