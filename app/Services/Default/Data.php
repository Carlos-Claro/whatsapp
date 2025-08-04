<?php
namespace App\Services\Default;

trait Data {
    protected $type = 'list';
    protected $validate = false;
    protected $id = false;
    protected bool $keys = false;
    protected $info = [];
    protected $fields = [];
    protected function init_data()
    {
        $this->type = isset($this->info['action']) ? $this->info['action'] : 'list';
        $this->validate = isset($this->info['validate']) ? $this->info['validate'] : false;
        $this->id = isset($this->info['id']) ? $this->info['id'] : false;
        $this->keys = isset($this->info['keys']) ? $this->info['keys'] : false;
        $this->fields = $this->fields();
    }

    public function get_data($info)
    {
        $this->info = $info;
        $this->init_data();
        if ( $this->type === 'item' && $this->id){
            if ( $this->validate ){
                return [$this->id => $this->fields[$this->id]['validate']];
            }
            return $this->fields[$this->id];
        }
        $return = [];
        foreach($this->fields as $key => $value){
            if ( in_array($this->type, $value['actions']) )
            {
                if ( $this->keys )
                {
                    $return[] = $key;
                }
                elseif($this->validate)
                {
                    $return[$key] = $value['validate'];
                }
                elseif ( $value['items'] )
                {
                    $return[$key] = $value;
                    $return[$key]['items'] = $this->defaults($value['items']);
                } else {
                    $return[$key] = $value;
                }

            }
        }
        return $return;
    }
}
