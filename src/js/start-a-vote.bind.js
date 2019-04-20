$(document).ready(function(){

    const $body=$('body');


    $('[data-toggle="tooltip"]').tooltip();

    $body.on('click','#addAChoice',function(){
       const $choiceInit=$('#choice_init');
       const $inputNumberOfChoices=$('#number_of_choices');
       const nItem=parseInt($inputNumberOfChoices.val())+1;

       if(nItem<=100) {
           $inputNumberOfChoices.val(nItem);
           let sItem = nItem;
           if (nItem < 10) {
               sItem = '0' + sItem;
           }
           const $newChoice = $choiceInit.clone();
           $newChoice.find("#choice_number_init").attr("id", "choice_number_" + sItem).html(sItem);
           $newChoice.find("#choice_label_init").attr("id", "choice_label_" + sItem);
           $newChoice.find("[for=choice_value_init]").attr("for", "choice_value_" + sItem);
           $newChoice.find("#choice_value_init").attr("id", "choice_value_" + sItem).attr("name", "choice_value_" + sItem);
           $newChoice.find("#choice_explanation_init").attr("id", "choice_explanation_" + sItem).attr("name", "choice_explanation_" + sItem);
           $newChoice.find("#remove_choice_init").attr("id", "remove_choice_" + sItem).attr("name", "remove_choice_" + sItem);

           if ($newChoice.find("#fakevote_vote_0_init").length) {
               $newChoice.find("#fakevote_vote_0_init").attr("id", "fakevote_vote_0_" + sItem).attr("name", "fakevote_vote_0_" + sItem);
               $newChoice.find("#fakevote_vote_1_init").attr("id", "fakevote_vote_1_" + sItem).attr("name", "fakevote_vote_1_" + sItem);
               $newChoice.find("#fakevote_vote_2_init").attr("id", "fakevote_vote_2_" + sItem).attr("name", "fakevote_vote_2_" + sItem);
               $newChoice.find("#fakevote_vote_3_init").attr("id", "fakevote_vote_3_" + sItem).attr("name", "fakevote_vote_3_" + sItem);
               $newChoice.find("#fakevote_vote_4_init").attr("id", "fakevote_vote_4_" + sItem).attr("name", "fakevote_vote_4_" + sItem);
               $newChoice.find("#fakevote_vote_5_init").attr("id", "fakevote_vote_5_" + sItem).attr("name", "fakevote_vote_5_" + sItem);
            }

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