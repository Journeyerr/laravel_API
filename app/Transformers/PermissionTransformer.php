<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PermissionTransformer extends TransformerAbstract
{
    // 用户权限的Transformer
    public function transform(Permission $permission)
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
        ];
    }
}