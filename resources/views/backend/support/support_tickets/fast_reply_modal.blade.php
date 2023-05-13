<link href="//cdn.staticfile.org/layui/2.8.2/css/layui.css" rel="stylesheet">
<style type="text/css">
    .layui-table-test td {
        /*min-height: 100px;*/
    }
</style>
<div class="modal-header">
    <button type="button" class="btn btn-light" onclick="filter_by_group(0)">全部</button>
    @foreach(\App\Models\TicketHuaShuGroup::all() as $group)
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
<script type="text/html" id="barDemo">
    <div class="layui-clear-space">
        <a class="layui-btn layui-btn-xs" lay-event="more">
            {{translate('More')}}
            <i class="layui-icon layui-icon-down"></i>
        </a>
    </div>
</script>
<script type="text/html" id="TPL-select-group">
    <select name="group_id" class="layui-border select-demo-primary" lay-ignore>
        <option value="">指定分组</option>
        @foreach(\App\Models\TicketHuaShuGroup::all() as $group)
        <option value="{{$group->id}}">{{$group->name}}{{$d}}</option>
        @endforeach
    </select>
</script>
<script type="text/html" id="toolbarDemo">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="add">{{translate('Add')}}</button>
    </div>
</script>
<script>
    var table;
    var list = [], origin_list = [];
    layui.use(['table', 'dropdown'], function(){
        table = layui.table;
        var dropdown = layui.dropdown;
        @if($list)
        list = JSON.parse("{{json_encode($list, JSON_UNESCAPED_UNICODE)}}".replace(/&quot;/g, '"'));
        origin_list = list;
        @endif

        // 创建渲染实例
        table.render({
            elem: '#test',
            toolbar: '#toolbarDemo',
            data: list, // 此处为静态模拟数据，实际使用时需换成真实接口
            height: 'full-35', // 最大高度减去其他容器已占有的高度差
            lineStyle: 'height: 151px;', // 定义表格的多行样式
            className: 'layui-table-test',
            css: [ // 重设当前表格样式
                '.layui-table-tool-temp{padding-right: 145px;}'
            ].join(''),
            cellMinWidth: 110,
            cols: [[
                {field:'abstract', Width: '40%', title: '{{translate('abstract')}}', edit: 'textarea'},
                {field:'content',Width: '40%', title: '{{translate('content')}}', edit: 'textarea'},
                {{--{field:'group_id',Width: '10%', title: '{{translate('Group')}}', templet: '#TPL-select-group'},--}}
                {title:'{{translate('Option')}}', Width: '20%', toolbar: '#barDemo'}
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

            } else if(obj.event === 'more'){
                // 更多 - 下拉菜单
                dropdown.render({
                    elem: this, // 触发事件的 DOM 对象
                    show: true, // 外部事件触发即显示
                    data: [{
                        title: '{{translate('Send')}}',
                        id: 'send'
                    },{
                        title: '{{translate('Delete')}}',
                        id: 'del'
                    }],
                    click: function(menudata){
                        if(menudata.id === 'send'){
                            console.log(data)
                            $("input[name=reply]").val(data.content)
                            submit_reply();
                            $('#fast_reply_modal').modal('hide');
                        } else if(menudata.id === 'del'){
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
                                    {}
                                } );
                            });
                        }
                    },
                    align: 'right', // 右对齐弹出
                    style: 'box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);' // 设置额外样式
                })
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
                        abstract: '',
                        content: '',
                    }
                    $.ajax( {
                        url: "{{route('huashu.store')}}",
                        type: 'POST',
                        data: data,
                        success: function (response)
                        {
                            list.push(response.data || data);
                            table.reload('test');
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
