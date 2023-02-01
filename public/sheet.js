function input_user_name(){
    const user_name = $("#user_name").val();
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            user_name: user_name,
        }
    }).done(function (result) {

    })
}