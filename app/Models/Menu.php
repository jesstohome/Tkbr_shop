<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    /**
     * 右侧菜单
     * author: Sym
     * time: 2023-04-22 09:33
     * @param int $menu_id
     * @return mixed
     */
    public static function getMenus($menu_id = 0)
    {
        $user = \Auth::user();

        // 若当前用户是集团员工
        $staffAllowPermissions = [];
        if ($user->user_type == 'staff') {
            // 判断是否有权限显示对应的权限树
            $staffAllowPermissions = json_decode($user->staffInfo->role->permissions, true);
            if (empty($staffAllowPermissions)) $staffAllowPermissions = [];
        }

        $menus = static::where("status", 1)->where("pid", $menu_id)->get();
        foreach ($menus as $key => $row) {
            // 管理员的系统菜单写死在前台了  不显示再展示一次
            if ($user->user_type == 'admin' && $row->id == 99) {
                unset($menus[$key]);
                continue;
            }

            if ($user->user_type == 'staff') {
                if (!in_array($row->id, $staffAllowPermissions)) {
                    unset($menus[$key]);
                    continue;
                }
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

            // redis 红点提示
            $show_red_tips = 0;
            if (!empty($row->red_dot_keys)) {
                foreach (explode(",", $row->red_dot_keys) as $_redis_key) {
                    if (hlen_plus($_redis_key)) {
                        $show_red_tips = 1;
                        break;
                    }
                }
            }
            $row->show_red_tips = $show_red_tips;

            $children = static::getMenus($row->id);
            if (count($children)) {
                $row->children = $children;

            }

            $menus[$key] = $row;
        }

        return $menus;
    }

    /**
     * 供jsTree使用的JSON权限配置项
     * author: Sym
     * time: 2023-04-21 20:26
     * @param $menu_id
     * @param $role_id
     * @return array
     */
    public static function getMenuJsTree($menu_id, $role_id) {
        $jsTree = [];
        $role = Role::find($role_id);

        // 若当前用户是集团员工
        $user = \Auth::user();
        $staffAllowPermissions = [];
        if ($user->user_type == 'staff') {
            // 判断是否有权限显示对应的权限树
            $staffAllowPermissions = json_decode($user->staffInfo->role->permissions, true);
            if (empty($staffAllowPermissions)) $staffAllowPermissions = [];
        }

        $menus = static::where("status", 1)->where("pid", $menu_id)->get();
        foreach ($menus as $key => $menu) {
            // 未启用的插件不显示
            if (!empty($menu->addon_name) && !addon_is_activated($menu->addon_name)) continue;

            // 员工没有的权限，不显示
            if ($user->user_type == 'staff') {
                if (!in_array($menu->id, $staffAllowPermissions)) continue;
            }

            // 未开启配置项的不显示
            if (!empty($menu->setting_name) && !get_setting($menu->setting_name)) continue;

            $children = static::getMenuJsTree($menu->id, $role_id);
            $jsTree[] = [
                "id" => $menu->id,
                "text" => translate($menu->name),
                "state" => [
                    "opened" => false,
                    // empty($children) 父级不自动选中
                    "selected" => empty($children) && in_array($menu->id, json_decode($role->permissions, true)),
                ],
                "children" => $children
            ];
        }

        return array_values($jsTree);
    }

    public static function getMenuJsTreeJson($role_id) {
        return json_encode(static::getMenuJsTree(0, $role_id));
    }
}
