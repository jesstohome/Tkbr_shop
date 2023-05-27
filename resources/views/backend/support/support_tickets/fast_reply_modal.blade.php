<link href="//cdn.staticfile.org/layui/2.8.2/css/layui.css" rel="stylesheet">
<style type="text/css">
    .layui-table-test td {
        /*min-height: 100px;*/
    }
    .laytable-cell-1-0-2 {
        width: 200px!important;
    }
</style>
<div class="modal-header">
    <button type="button" class="btn btn-light" onclick="filter_by_group(0)">全部</button>
    @foreach(filter_by_bloc(\App\Models\TicketHuaShuGroup::query())->get() as $group)
    <button type="button" class="btn btn-light" onclick="filter_by_group({{$group->id}})">{{$group->name}}</button>
    @endforeach
</div>
<div class="modal-body">
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade show in active" id="home">
            <table class="layui-hide" id="test" lay-filter="test"></table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
</div>
<script type="text/html" id="barDemo" style="width: 300px">
    <a class="layui-btn layui-btn-xs" lay-event="send">发送</a>
    <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
</script>

<script type="text/html" id="toolbarDemo">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="add">{{translate('Add')}}</button>
    </div>
</script>
<script>
    var table;
    var curGroupId = 0;
    var list = [], origin_list = [];
    layui.use(['table', 'dropdown'], function(){
        table = layui.table;
        var dropdown = layui.dropdown;
        @if($list)
        list = JSON.parse("{{json_encode($list, JSON_UNESCAPED_UNICODE)}}".replace(/&quot;/g, '"'));
        origin_list = list || [];
        @endif

        // 创建渲染实例
        table.render({
            elem: '#test',
            toolbar: '#toolbarDemo',
            data: list, // 此处为静态模拟数据，实际使用时需换成真实接口
            height: 'full-35', // 最大高度减去其他容器已占有的高度差
            // lineStyle: 'height: 151px;', // 定义表格的多行样式
            className: 'layui-table-test',
            css: [ // 重设当前表格样式
                // '.layui-table-tool-temp{padding-right: 145px;}'
            ].join(''),
            cellMinWidth: 300,
            cols: [[
                {field:'abstract', width: '40%', title: '{{translate('abstract')}}', edit: 'textarea'},
                {field:'content',width: '40%', title: '{{translate('content')}}', edit: 'textarea'},
                {{--{field:'group_id',Width: '10%', title: '{{translate('Group')}}', templet: '#TPL-select-group'},--}}
                {title:'{{translate('Option')}}', Width: '200px', toolbar: '#barDemo'}
            ]],
            done: function () {
                $(".layui-table").width("100%")
            },

            error: function(res, msg){
                console.log(res, msg)
            }
        });

        // 触发单元格工具事件
        table.on('tool(test)', function(obj){ // 双击 toolDouble
            var data = obj.data; // 获得当前行数据
            // console.log(obj)
            if(obj.event === 'send'){
                $("input[name=reply]").val(data.content)
                submit_reply();
                $('#fast_reply_modal').modal('hide');
            } else if(obj.event === 'del'){
                layer.confirm("{{translate('Are you sure to delete this?')}}", function(index){
                    obj.del(); // 删除对应行（tr）的DOM结构
                    layer.close(index);
                    // 向服务端发送删除指令
                    $.ajax( {
                        url: "{{route('huashu.destroy')}}",
                        data: {
                            id: data.id,
                        },
                        type: 'POST',
                        success: function (response)
                        {
                            origin_list.forEach(function(item, index, arr) {
                                console.log(item.id, data.id, item.id === data.id);
                                if(item.id === data.id) {
                                    origin_list.splice(index, 1);    //满足条件 根据下标删除该元素
                                }
                            });

                            console.log(list.length);
                            list.forEach(function(item, index, arr) {
                                if(item.id === data.id) {
                                    list.splice(index, 1);    //满足条件 根据下标删除该元素
                                }
                            });

                            console.log(list.length);
                        }
                    } );
                });
            }
        });

        // 工具栏事件
        table.on('toolbar(test)', function(obj){
            var id = obj.config.id;
            var checkStatus = table.checkStatus(id);
            var othis = lay(this);
            switch(obj.event){
                case 'add':
                    let data = {
                        group_id: curGroupId,
                        abstract: '',
                        content: '',
                    }
                    $.ajax( {
                        url: "{{route('huashu.store')}}",
                        type: 'POST',
                        data: data,
                        success: function (response)
                        {
                            origin_list.push(response.data || data);
                            filter_by_group(curGroupId)
                        }
                    } );
                    break;
            };
        });

        // 单元格编辑事件
        table.on('edit(test)', function(obj){
            var field = obj.field; // 得到字段
            var value = obj.value; // 得到修改后的值
            var data = obj.data; // 得到所在行所有键值
            // 值的校验

            // 编辑后续操作，如提交更新请求，以完成真实的数据更新
            $.ajax( {
                url: "{{route('huashu.update')}}",
                type: 'POST',
                data: data,
                success: function (response)
                {}
            } );

            layer.msg('{{translate('Successfully edited')}}', {icon: 1});

            // 其他更新操作
            var update = {};
            update[field] = value;
            obj.update(update);
        });
    });

    function filter_by_group(group_id) {
        curGroupId = group_id;

        if (group_id > 0) {
            list = origin_list.filter(function (it) {
                return it.group_id == group_id;
            })
        } else {
            list = origin_list;
        }

        table.reload('test', {data: list});
    }

    $('.select-demo-primary').on('change', function(){
        var value = this.value; // 获取选中项 value
        var data = table.getRowData(this); // 获取当前行数据(如 id 等字段，以作为数据修改的索引)
        // 更新数据中对应的字段
        data.city = value;
        // 显示 - 仅用于演示
        layer.msg('选中值: '+ value +'<br>当前行数据：'+ JSON.stringify(data));
    });
</script>
