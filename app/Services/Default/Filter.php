<?php
namespace App\Services\Default;

use Illuminate\Http\Request;


trait Filter {
    protected $filter = false;

    public function get_filter( Request $request) : array
    {
        $data = [];
        foreach ( $this->fields as $key => $value){
            if ( isset($request[$key]) &&  ! empty($request[$key]) ){
                $data[] = [$value['where']['field'], $value['where']['type'], ($value['where']['type'] == 'like' ? '%'.$request[$key].'%': $request[$key])];
            }
        }
        return $data;
    }
}
