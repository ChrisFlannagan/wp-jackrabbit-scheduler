(function ( $ ) {
    $(".edit_js").click(function() {
        console.log("A click!" + $(this).attr("jname"));
        $("#s_name").val($(this).attr("jname"));
        $("#s_code").val($(this).attr("jcode"));
        $("#s_cat1").val($(this).attr("jcat1"));
        $("#s_cat2").val($(this).attr("jcat2"));
        $("#s_hide").val($(this).attr("jhide"));
        $("#s_show").val($(this).attr("jshow"));
        $("#editing").val($(this).attr("jid"));
        $("#save_btn").val("Save Changes")
        $("#cancel_edit").css("display", "block");
    });
    $("#cancel_edit").click(function() {
        $("#s_name").val('');
        $("#s_code").val('');
        $("#s_cat1").val('');
        $("#s_cat2").val('');
        $("#s_hide").val('');
        $("#s_show").val('');
        $("#editing").val('0');
        $("#save_btn").val("Create Shortcode");
        $("#cancel_edit").css("display", "none");
    });
    $(".del_js").click(function() {
        var confDel = confirm("Are you sure you'd like to delete this shortcode?");
        if(confDel) {
            $("#delIt").val($(this).attr("jid"));
            $("#mainFormJR").submit();
        }
    });
})( jQuery );