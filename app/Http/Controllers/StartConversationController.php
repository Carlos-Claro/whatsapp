<?php

namespace App\Http\Controllers;

use App\Models\Start_conversation;
use App\Services\StartConversation\Fields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StartConversationController extends Controller
{
    use Fields;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', env('PER_PAGE', 10));
        $data = $this->get_fields($request, 'list');
        $data['items'] = Start_conversation::with(['related'])
            ->where($data['filters']['query'])
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        return Inertia::render('Default/List', $data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->get_fields(request(), 'create');
        $data['item'] = collect(request()->all());
        if ( isset($data['item']['start_conversation_id'])){
            $data['item']['related'] = Start_conversation::where('id', request()->start_conversation_id)->first();
        }
        return Inertia::render('StartConversation/Edit', $data);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validador = $this->get_validate('edit');
        $data_add = $request->validate($validador);
        DB::beginTransaction();
        $item = Start_conversation::create($data_add);
        DB::commit();
        $request->session()->flash('message', 'Start '.$item->question.' adicionado com sucesso');
        return to_route('start_conversation.edit', $item->id);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = $this->get_fields(request(), 'edit', 'id');
        $data['item'] = Start_conversation::with('related')
            ->where('id', $id)
            ->first();
        return Inertia::render('StartConversation/Edit', $data);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = Start_conversation::find($id);
        if (!$item) {
            return redirect()->route('start_conversation.index')->with('error', 'Item not found');
        }
        $validador = $this->get_validate('edit');
        if($request->validate($validador)){
            foreach ($request->all() as $key => $value) {
                $item->$key = $value;
            }
            $item->save();
            $request->session()->flash('message', 'Start '.$item->question.' atualizado com sucesso');
        }else{
            $request->session()->flash('error', 'Erro ao atualizar o item');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $item = Start_conversation::find($id);
        if (!$item) {
            return redirect()->route('start_conversation.index')->with('error', 'Item not found');
        }
        DB::beginTransaction();
        $item->delete();
        DB::commit();
        $request->session()->flash('message', 'Start '.$item->question.' deletado com sucesso');
    }
}
