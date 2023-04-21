<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    public static function getMenus($menu_id = 0)
    {
        $user = \Auth::user();
        $menus = static::where("status", 1)->where("pid", $menu_id)->get();
        foreach ($menus as $key => $row) {
            // 管理员的系统菜单写死在前台了  不显示再展示一次
            if ($user->user_type == 'admin' && $row->id == 99) {
                unset($menus[$key]);
                continue;
            }

            // 验证未开通设置项
            if (!empty($row->setting_name) && !get_setting($row->setting_name)) {
                // 子级菜单 ，admin也不需要显示
                if ($row->route) {
                    unset($menus[$key]);
                    continue;
                } else {
                    if ($user->user_type != 'admin') {
                        unset($menus[$key]);
                        continue;
                    }
                }
            }

            $children = static::getMenus($row->id);
            if (count($children)) {
                $row->children = $children;
                $menus[$key] = $row;
            }
        }

        return $menus;
    }

    /**
     * 供jsTree使用的JSON
     * author: Sym
     * time: 2023-04-21 20:26
     * @param $menu_id
     * @return array
     */
    public static function getMenuJsTree($menu_id) {
        $jsTree = [];
        $menus = static::where("status", 1)->where("pid", $menu_id)->get();
        foreach ($menus as $key => $menu) {
            $jsTree[] = [
                "id" => $menu->id,
                "text" => translate($menu->name),
                "state" => [
                    "opened" => false,
                    "selected" => true,
                ],
                "children" => static::getMenuJsTree($menu->id)
            ];
        }

        return array_values($jsTree);
    }

    public static function getMenuJsTreeJson() {
        return json_encode(static::getMenuJsTree(0));
    }
}
