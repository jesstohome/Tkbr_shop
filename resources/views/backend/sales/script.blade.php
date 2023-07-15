<script>
    // 表单提交前，判断是否选择了id checkbox
    $("form").on("submit", function () {
        try {
            let ids = [];
            $("input.item-row-id").each((k, it) => {
                if ($(it).is(":checked")) {
                    ids.push($(it).val());
                }
            });
            console.log(ids);
            if (!$(this).find("#ids").length) {
                $(this).append("<input type='hidden' id='ids' name='ids' value='' />")
            }

            $(this).find("#ids").val(ids.join(","));
        } catch(e) {console.log(e)}
    })
</script>
