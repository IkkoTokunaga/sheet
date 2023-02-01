$('.message a').click(function() {
    $('form').animate({
        height: "toggle",
        opacity: "toggle"
    }, "slow");
});

$("#login_btn").click(function () {
    const email = $("#email").val();
    const user_name =$("#user_name").val();
    const password =$("#password").val();
    const token =$("#token").val();

    var result = $.ajax({
        type: "POST",
        url: "/auth/login.php",
        cache: false,
        async: false,
        data: {
            email: email,
            user_name: user_name,
            password: password,
            token: token
        }
    }).responseText;

    if (result === 'OK') {
        window.location.assign("/");

    } else {
        alert('ログインできませんでした。');
    }

});

