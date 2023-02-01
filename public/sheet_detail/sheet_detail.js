$(".display_input").click(function () {
    const height = $(this).parent().height();
    if($(this).next().hasClass('text_area')){
        $(this).next().css('min-height', String(height) + "px");
    }
    $(this).addClass('hidden');
    $(this).next().removeClass('hidden');
    $(this).next().focus();
    const val = $(this).next().val();
    $(this).next().val('');
    $(this).next().val(val);
});

$(".hidden_input").blur(function () {
    $(this).addClass('hidden');
    $(this).prev().removeClass('hidden');
});

$(".input_name").click(function(){
    if(!$(this).val()){
        $(this).val($("#input_user_name").val());
    }
})
$(".check_box").click(function(){
    const checkbox = $(this).next().find('.check');
    if(checkbox.prop('checked')){
        checkbox.prop('checked', false);
    }else{
        checkbox.prop('checked', true);
    }
})

$(".hidden_input, .status").change(function () {
    const element = $(this);
    const val = element.val();
    const data_id = element.data('id');
    const sheet_id = $(".sheet_id").val();

    element.prev().html(val.replaceAll("\n", "<br>"));
    $.ajax({
        type: "POST",
        url: "ajax.php",
        cache: false,
        dataType: 'json',
        data: {
            sheet_id: sheet_id,
            data_id: data_id,
            value: val,
            act: 'update_sheet_detail'
        }
    }).done(function (result) {
        if (result['response'] === 'OK') {
            element.parent().parent().find('.updated_at').text(result['date']);
            $(".sheet_update_at").text(result['date2']);
        } else {
            alert('保存に失敗しました。');
        }

    }).fail(function (xhr) {
        console.log(xhr)

    }).always(function (xhr, msg) {

    });
});

$(".status").change(function(){
    if($(this).val() === "500"){
        $(this).parent().parent().addClass('gray');
        $(this).parent().parent().removeClass('red');
    }
    if($(this).val() === "400"){
        $(this).parent().parent().addClass('red');
        $(this).parent().parent().removeClass('gray');
    }
    if($(this).val() !== "400" && $(this).val() !== "500"){
        $(this).parent().parent().removeClass('gray');
        $(this).parent().parent().removeClass('red');
    }
    
})

$("#all_check").change(function () {
    if ($(this).prop('checked')) {
        $(".check").prop('checked', true);
    } else {
        $(".check").prop('checked', false);
    }
});

function all_update() {

    const sheet_id = $(".sheet_id").val();
    const status = $("#all_status option:selected").val();
    const register_name = $("#all_register_name").val();
    const amender_name = $("#all_amender_name").val();
    const inspector_name = $("#all_inspector_name").val();

    let id = '';
    let arr = new Array;

    if (status || register_name || amender_name || inspector_name) {

        $(".check").each(function (index, element) {
            if ($(element).prop('checked')) {
                id = $(element).attr('id');
                id = id.replace('check_', '');
                arr.push(id);
            }
        });

        if (arr.length > 0) {

            const id_json = JSON.stringify(arr)
            $.ajax({
                type: "POST",
                url: "ajax.php",
                cache: false,
                dataType: 'json',
                data: {
                    id: id_json,
                    sheet_id: sheet_id,
                    status: status,
                    register_name: register_name,
                    amender_name: amender_name,
                    inspector_name: inspector_name,
                    act: 'all_update_sheet_detail'
                }
            }).done(function (result) {
                if (result['response'] === 'OK') {
                    const search_status = $("#search_status option:selected").val();
                    const search_title = $("#search_title").val();
                    const search_created_at = $("#search_created_at").val();
                    const search_updated_at = $("#search_updated_at").val();
                    alert('更新しました。');
                    window.location.href = './?id=' + sheet_id + "&search_status=" + search_status + "&search_title=" + search_title + "&search_created_at=" + search_created_at + "&search_updated_at=" + search_updated_at;
                } else {
                    alert('保存に失敗しました。');
                }

            }).fail(function (xhr) {
                console.log(xhr)

            }).always(function (xhr, msg) {

            });

        } else {
            alert('チェックを入れてください。');
        }
    } else {
        alert('入力してください。');
    }
}

function row_delete() {

    let id = '';
    let arr = new Array;
    const sheet_id = $(".sheet_id").val();

    $(".check").each(function (index, element) {
        if ($(element).prop('checked')) {
            id = $(element).attr('id');
            id = id.replace('check_', '');
            arr.push(id);
        }
    });

    if (arr.length > 0) {

        const id_json = JSON.stringify(arr)
        $.ajax({
            type: "POST",
            url: "ajax.php",
            cache: false,
            dataType: 'json',
            data: {
                id: id_json,
                sheet_id: sheet_id,
                act: 'row_delete'
            }
        }).done(function (result) {
            if (result['response'] === 'OK') {
                const search_status = $("#search_status option:selected").val();
                const search_title = $("#search_title").val();
                const search_created_at = $("#search_created_at").val();
                const search_updated_at = $("#search_updated_at").val();
                alert('選択した行を削除しました。');
                window.location.href = './?id=' + sheet_id + "&search_status=" + search_status + "&search_title=" + search_title + "&search_created_at=" + search_created_at + "&search_updated_at=" + search_updated_at;
            } else {
                alert('処理に失敗しました。');
            }

        }).fail(function (xhr) {
            console.log(xhr)

        }).always(function (xhr, msg) {

        });

    } else {
        alert('チェックを入れてください。');
    }
}

function create_csv() {

    let id = '';
    let arr = new Array;
    const sheet_id = $(".sheet_id").val();

    $(".check").each(function (index, element) {
        if ($(element).prop('checked')) {
            id = $(element).attr('id');
            id = id.replace('check_', '');
            arr.push(id);
        }
    });

    if (arr.length > 0) {

        const id_json = JSON.stringify(arr)
        const response = $.ajax({
            type: "POST",
            url: "ajax.php",
            cache: false,
            dataType: 'String',
            async: false,
            data: {
                id: id_json,
                sheet_id: sheet_id,
                act: 'create_csv'
            }
        }).responseText;

        if (response === 'NG') {
            alert('チェックを入れてください。');
        } else {
            const url = "./csv/" + sheet_id + "/" + response;
            const a = document.createElement("a");
            document.body.append(a);
            a.download = formatDate(new Date()) + "_" + $("#sheet_name").val() + ".csv";
            a.href = url;
            a.click();
            a.remove();
        }
    }
}

function formatDate(dt) {
    const y = dt.getFullYear();
    const m = ('00' + (dt.getMonth() + 1)).slice(-2);
    const d = ('00' + dt.getDate()).slice(-2);
    const h = ('00' + dt.getHours()).slice(-2);
    const i = ('00' + dt.getMinutes()).slice(-2);
    const s = ('00' + dt.getSeconds()).slice(-2);
    return (y + m + d + "_" + h + i + s);
}

function create_report(){
    let id = '';
    let arr = new Array;
    const sheet_id = $(".sheet_id").val();

    $(".check").each(function (index, element) {
        if ($(element).prop('checked')) {
            id = $(element).attr('id');
            id = id.replace('check_', '');
            arr.push(id);
        }
    });

    const id_json = JSON.stringify(arr);
    window.location.href="../report/?mode=create&id=" + id_json;
}

$("#import_csv").click(function(){

    const id = $("#id").val();
    window.location.href="../sheet_detail_import/?id="+id;
})



