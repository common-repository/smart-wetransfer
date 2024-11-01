jQuery(document).ready(function(){
    jQuery("#options").click(function(){

        if(this.checked){
            jQuery(".checkBoxes").each(function(){
                this.checked= true;
            });
        }else{
            jQuery(".checkBoxes").each(function(){
                this.checked= false;
            });
        }

    });

});

jQuery(document).ready(function(){

    jQuery(".delete-single").click(function(e){
       

    var selected_id = jQuery(this).attr('id');
    var data = {
        action: 'wetransfer_delete_record',
        link_id: selected_id
    }
    jQuery("#message").html('<h3>Deleting requested record...</h3>');

})


});