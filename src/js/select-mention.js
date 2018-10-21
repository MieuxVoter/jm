$(document).ready(function() {

    const $body = $('body');
    $body.on('click','.jsSelectMention',function(){

        const $input=$(this).closest(".card-body").find(".jsInputMention");
        $input.val($(this).attr("data-value"));

        $(this).closest(".card-body").find(".jsSelectMention").removeClass("mention-selected");
        $(this).addClass("mention-selected");
    });
});

