$(document).ready(function(){

    const $body=$('body');

    $body.on('click','#addAChoice',function(){
       const $choiceInit=$('#choice_init');
       const $inputNumberOfChoices=$('#number_of_choices');
       const nItem=parseInt($inputNumberOfChoices.val())+1;

       if(nItem<=50){
           $inputNumberOfChoices.val(nItem);
           let sItem=nItem;
           if(nItem<10){
               sItem='0'+sItem;
           }
           const $newChoice=$choiceInit.clone();
           $newChoice.find("#choice_number_init").attr("id","choice_number_"+sItem).html(sItem);
           $newChoice.find("#choice_label_init").attr("id","choice_label_"+sItem);
           $newChoice.find("[for=choice_value_init]").attr("for","choice_value_"+sItem);
           $newChoice.find("#choice_value_init").attr("id","choice_value_"+sItem).attr("name","choice_value_"+sItem);
           $newChoice.find("#choice_explanation_init").attr("id","choice_explanation_"+sItem).attr("name","choice_explanation_"+sItem);
           $newChoice.find("#remove_choice_init").attr("id","remove_choice_"+sItem).attr("name","remove_choice_"+sItem);
           $newChoice.removeClass("d-none");
           $('#choices').append($newChoice);
       }else{
           toastr["error"]("Nombre de propositions maximum atteint", "Erreur")
       }



    });

    $body.on('click','.btnRemoveChoice',function(){
        const $btn=$(this);
        const $div=$btn.parents(".divChoice");
        const $field=$div.find(".fieldRemoveChoice");

        if($field.val()==="1"){
            $field.val("0");
            $div.find("input").prop("readonly",false);
            $div.find("textarea").prop("readonly",false);
            $btn.find("i").removeClass("fa-undo").addClass("fa-trash");
            $div.removeClass("opacity50").addClass("opacity100");


        }else{
            $field.val("1");
            $div.find("input").prop("readonly",true);
            $div.find("textarea").prop("readonly",true);
            $btn.find("i").removeClass("fa-trash").addClass("fa-undo");
            $div.removeClass("opacity100").addClass("opacity50");
        }
    });

    $body.on('click','.autoselect',function(){
        $(this).select();
    });

    $body.on('click','.copy-to-clipboard',function(){

        copyToClipboard($(this).attr("data-target"));
    });

});