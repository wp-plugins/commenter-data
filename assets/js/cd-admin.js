jQuery(document).ready(function(){
   
    jQuery('body').on('click', '.cd-csv', function(){
        
        var post = jQuery(this).data('pid');
        
        var ajaxdata = {

            action: 'commenter',
            pid: post
        };

        /* Send ajax request */
        jQuery.post(ajaxurl, ajaxdata, function(response){
           
            response = jQuery.parseJSON(response);
            if( response.error === false ){

                window.location = window.location.href+'&cddcsv=1&pid='+post;
            }
            
        });
        
    });
    
    /* Load more posts */
    jQuery('.cd-load').on('click', function(){
       
        var offset  = jQuery(this).data('offset');
        offset = parseInt(offset);
        var element = jQuery(this);

        var ajaxdata = {
            
            action: 'commenter_loadpost',
            offset: offset
        };
        
        jQuery.post( ajaxurl, ajaxdata, function(response){
            
            response = jQuery.parseJSON(response);
           
            if( response.ispost === true ){
                
                jQuery(element).before(response.data);
                jQuery(element).data( 'offset', offset+1);

            }else{

                jQuery(element).remove();
            }
            
        });
        
    });
    
    jQuery('.cd-setting').on('click', function(){
       
        var data = jQuery('#cd-setting-form').serialize();
        var ajaxdata = {
          
            action: 'commenter_setting',
            data: data
        };
        
        jQuery.post(ajaxurl, ajaxdata, function(res){

            res = jQuery.parseJSON(res);            
            jQuery('.cd-msg').html(res.data);

        });
        
    });
    
});