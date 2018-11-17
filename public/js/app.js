/**
 * Chartist.js plugin to display a "target" or "goal" line across the chart.
 * Only tested with bar charts. Works for horizontal and vertical bars.
 *
 * Copyright (c) 2015 Yorkshire Interactive (yorkshireinteractive.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function(window, document, Chartist) {
  'use strict';

  var defaultOptions = {
    // The class name so you can style the text
    className: 'ct-goal-line',
    // The axis to draw the line. y == vertical bars, x == horizontal
    axis: 'y',
    // What value the goal line should be drawn at
    value: null
  };

  Chartist.plugins = Chartist.plugins || {};

  Chartist.plugins.ctGoalLine = function(options) {
    options = Chartist.extend({}, defaultOptions, options);
    return function ctGoalLine (chart) {

      chart.on('created', function(context) {

        var projectTarget = {
          y: function (chartRect, bounds, value) {
            var targetLineY = chartRect.y1 - (chartRect.height() / bounds.max * value);

            return {
              x1: chartRect.x1,
              x2: chartRect.x2,
              y1: targetLineY,
              y2: targetLineY
            }
          },
          x: function (chartRect, bounds, value) {
            var targetLineX = chartRect.x1 + (chartRect.width() / bounds.max * value);

            return {
              x1: targetLineX,
              x2: targetLineX,
              y1: chartRect.y1,
              y2: chartRect.y2
            }
          }
        };

        var targetLine = projectTarget[options.axis](context.chartRect, context.bounds, options.value)
        context.svg.elem('line', targetLine, options.className);
      });
    }
  }

}(window, document, Chartist));
;
function copyToClipboard(selector) {
    let $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(selector).val()).select();
    toastr["success"]("Lien copié !");

    document.execCommand("copy");
    $temp.remove();
};
$(document).ready(function() {

    const $body = $('body');
    $body.on('click','.jsSelectMention',function(){

        const $input=$(this).closest(".card-body").find(".jsInputMention");
        $input.val($(this).attr("data-value"));

        $(this).closest(".card-body").find(".jsSelectMention").removeClass("mention-selected");
        $(this).addClass("mention-selected");
    });
});

;
$(document).ready(function(){

    const $body=$('body');


    $('[data-toggle="tooltip"]').tooltip();

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

});;
toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": true,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};


$(document).ready(function() {

    const $body = $('body');

    $('.toastr-msg').each(function(){

        let $obj=$(this);
        toastr[$obj.attr("data-type")]($obj.attr("data-text"), $obj.attr("data-title"))

    });

});