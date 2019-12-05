// START FORM CHECKER
var input = $('.validate-input .inputText');

$('.inputText').each(function(){
    $(this).on('blur', function(){
        if($(this).val().trim() != "") {
            $(this).addClass('validated');
        }
        else {
            $(this).removeClass('validated');
        }
    })
})

function checkall() {
    var check = true;
    for(var i=0; i<input.length; i++) {
        if(validate(input[i]) == false){
            showValidate(input[i]);
            check=false;
        }
    }
    submitData(); // Call function to submit form
    grecaptcha.reset(); // If there is an error, reset recaptcha
}

$('.formCheck .inputText').each(function(){
    $(this).focus(function(){
        hideValidate(this);
    });
});

function validate (input) {
    if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') { // Check email format
        if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
            return false;
        }
    }
    else {
        if($(input).val().trim() == ''){
            return false;
        }
    }
}

function showValidate(input) {
    var thisAlert = $(input).parent();
    $(thisAlert).addClass('alert-validate');
}

function hideValidate(input) {
    var thisAlert = $(input).parent();
    $(thisAlert).removeClass('alert-validate');
}

// END FORM CHECKER

// SUBMIT FORM
function submitData(token) {
    event.preventDefault();
    $.ajax({
        type: "POST",
        url: "sendmail.php",
        data: $('#contactSend').serialize(),
        beforeSend: function (xhr) {
            $('.mt_load').show();
        },
        success: function(response){
            var jsonParse = response;
            objDone = JSON.parse(jsonParse);
            console.log(objDone);
            if (response) { // Get response from sendmail.php
                if (objDone['signal'] == 'ok') { // If signal is ok
                    $('#msg').hide();
                    $('input, textarea').val(function () {
                        return this.defaultValue;
                    });
                    $('.formCheck').hide();
                    $('.sendOK').show();
                }
                else {
                    $('#msg').show();
                    $('#msg').html('<div class="mt_error">'+ objDone['msg'] +'</div>'); // Get error form sendemail.php
                }
            }
        },
        error: function () {
            $('#msg').show();
            $('#msg').html('<div class="mt_error">Errors occur. Please try again later.</div>');
        },
        complete: function () {
            $('.mt_load').hide();
        }
    });
}