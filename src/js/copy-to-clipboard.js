function copyToClipboard(selector) {
    let $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(selector).val()).select();
    toastr["success"]("Lien copié !");

    document.execCommand("copy");
    $temp.remove();
}