<?php
namespace App\Services\StartConversation;

use App\Models\Departments;
use App\Services\Default\Data;
use App\Services\Default\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


trait Fields {
    use Data, Filter;
    public function get_fields(Request $request, $type = 'list', $id = 'id'): Collection {
        $data = [];
        $data[$type] = $this->get_data(['action' => $type, 'id' => $id]);
        $data['filters'] = $this->get_filters($request, $type);
        $data['title'] = $this->get_title($type);
        $data['actions'] = $this->get_actions($type);
        $data['key'] = 'id';
        return collect($data);
    }
    public function get_validate(string $type): array{
        return $this->get_data(['action' => $type, 'validate' => true]);
    }
    protected function get_title($type): string {
        switch($type) {
            case 'create':
                return 'Criar Conversa Inicial';
            case 'edit':
                return 'Editar Conversa Inicial';
            case 'list':
            default:
                return 'Listar Conversas Iniciais';
        }
    }
    protected function get_actions($type): array {
        $data['main']['create'] = $this->actions('create');
        $data['main']['delete'] = $this->actions('delete');
        $data['field']['edit'] = $this->actions('edit');
        return $data;
    }
    protected function get_filters($request, $type): array {
        $data = [];
        switch($type) {
            case 'edit':
            case 'create':
                $data['fields'] = $this->get_data(['action' => 'edit']);
                $data['action'] = $type == 'edit' ? 'start_conversation.update' : 'start_conversation.store';
                $data['item'] = $request->all();
                $data['addon']['departments'] = Departments::select('id as value', 'title as text')->get();
                $data['addon']['button_fields'] = $this->defaults('button');
                $data['addon']['text_fields'] = $this->defaults('text');
                break;
            case 'list':
            default:
                $data['fields'] = $this->get_data(['action' => 'filter']);
                $data['query'] = $this->get_filter($request);
                $data['items'] = $request->all();
                $data['action'] = 'start_conversation.index';
                break;
        }
        return $data;
    }
    protected function fields() : Collection{
        return collect([
            'id'                    => ['title' => 'ID',                        'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['list','edit','filter'], 'type' => ['InputText','number'], 'where' => ['field' => 'start_conversation.id', 'type' => '=',]],
            'start_conversation_id' => ['title' => 'ID da Conversa Anterior',   'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['edit'], 'type' => ['InputText','number'], 'where' => ['field' => 'start_conversation.start_conversation_id', 'type' => '=',]],
            'related.question'      => ['title' => 'Pergunta anterior',         'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['list'], 'type' => ['InputText','number'], 'where' => ['field' => 'start_conversation.start_conversation_id', 'type' => '=',]],
            'tag'                   => ['title' => 'Tag',                       'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['list','edit','filter'], 'type' => ['InputText','number'], 'where' => ['field' => 'start_conversation.tag', 'type' => 'like',]],
            'sequence'              => ['title' => 'Sequencia',                 'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['list','edit','filter'], 'type' => ['InputText','number'], 'where' => ['field' => 'start_conversation.sequence', 'type' => '=',]],
            'question'              => ['title' => 'Pergunta',                  'validate' => 'required', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['list','edit','filter'], 'type' => ['InputText','number'], 'where' => ['field' => 'start_conversation.question', 'type' => '=',]],
            'answer'                => ['title' => 'Resposta',                  'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['edit'], 'type' => ['InputText','json'],   'where' => ['field' => 'start_conversation.answer', 'type' => '=',]],
            'department_id'         => ['title' => 'Departamento',              'validate' => '', 'default' => '', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => false, 'actions' => ['edit'], 'type' => ['Select','number'],  'where' => ['field' => 'start_conversation.department_id', 'type' => '=',]],
            'type'                  => ['title' => 'Tipo',                      'validate' => 'required', 'default' => 'button', 'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => 'types', 'actions' => ['list','edit','filter'], 'type' => ['Select','number'],  'where' => ['field' => 'start_conversation.type', 'type' => '=',]],
            'status'                => ['title' => 'Status',                    'validate' => 'required', 'default' => 1,  'attrs' => ['attr' => '', 'divClass' => 'w-1/3',  'class' => ''], 'items' => 'statuses', 'actions' => ['list','edit','filter'], 'type' => ['Select','number'], 'where' => ['field' => 'start_conversation.status', 'type' => '=',]],
        ]);
    }
    protected function defaults($item = false) : Collection {
        $data = collect([
            'statuses' => [
                ['value' => 0, 'text' => 'Inativo'],
                ['value' => 1, 'text' => 'Ativo'],
            ],
            'types' => [
                ['value' => 'button', 'text' => 'Botão'],
                ['value' => 'text', 'text' => 'Texto'],
                ['value' => 'department', 'text' => 'Departamento'],
                ['value' => 'departments', 'text' => 'Departamentos'],
            ],
            'button' => [
                'tag' => ['title' => 'Tag - única e com apenas uma palavra'],
                'title' => ['title' => 'Título'],
            ],
            'text' => [
                'action' => ['title' => 'Ação'],
                'id_error' => ['title' => 'ID de Erro'],
                'id_success' => ['title' => 'ID de Sucesso'],
            ],
        ]);
        if( $item )
        {
            return collect($data[$item]);
        }
        return $data;
    }
    protected function actions($item = false): Collection
    {
        $data = collect([
            'list' => [
                'label' => 'Listar',
                'icon' => 'pi pi-solid pi-list',
                'route' => 'start_conversation.index',
                'method' => 'get',
            ],
            'edit' => [
                'label' => 'Editar',
                'icon' => 'pi pi-solid pi-pen-to-square',
                'route' => 'start_conversation.edit',
                'method' => 'get',
            ],
            'delete' => [
                'label' => 'Deletar',
                'icon' => 'pi pi-solid pi-trash',
                'action' => 'start_conversation.destroy',
                'method' => '',
            ],
            'create' => [
                'label' => 'Novo',
                'icon' => 'pi pi-solid pi-plus',
                'url' => route('start_conversation.create'),
                'method' => 'get',
            ],
        ]);
        if( $item )
        {
            return collect($data->get($item));
        }
        return $data;

    }

}
