<?php 
namespace App\Helpers;

class QueryHelper {
    static public function searchAll($query, $search, $columns) {
        return $query->where(function ($query) use ($search, $columns) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', '%' . $search . '%');
            }
        });
    }
}
