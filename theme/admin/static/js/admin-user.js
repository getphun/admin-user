$(function(){
    var passwd = $('#field-new_password');
    if(passwd.get(0)){
        var level = $('<div id="password-meter"></div>');
        minLength = passwd.attr('minlength');
        
        passwd.before(level);
        
        passwd.on('keyup change focus', function(){
            var val = passwd.val();
            if(!val)
                return level.removeAttr('data-level');
            
            var score = 0;
            var scores = ['very-weak', 'weak', 'better', 'medium', 'strong', 'strongest'];
            
            if(minLength && val.length > minLength)
                score++;
            
            if((val.match(/[a-z]/)) && (val.match(/[A-Z]/)))
                score++;
            
            if(val.match(/\d+/))
                score++;
            
            if(val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))
                score++;
            
            if(val.length > 12)
                score++;
            
            level.attr('data-level', scores[score]);
        });
        
        passwd.on('blur', function(){
            level.removeAttr('data-level');
        });
    }
    
    // autoselect social account vendor
    var spage = $('#field-page');
    var svendor = $('#field-vendor');
    if(spage.get(0) && svendor.get(0)){
        spage.change(function(){
            var a = document.createElement('a');
            a.href = spage.val();
            var host = a.host.replace(/www\.|\.com|\.net/g,'');
            svendor.selectpicker('val', host);
        });
    }
    
    /**
     * User permissions
     */
    if($('.role-selector').get(0)){
        $('.role-selector').click(function(){
            var $this = $(this);
            var role  = $this.data('role');
            
            $('.item-checkbox').each(function(){
                var $this = $(this);
                var roles = $(this).data('role').split(' ');
                if( ~roles.indexOf(role) )
                    $this.prop('checked', true);
            });
        });
        
        $('.role-unselector').click(function(){
            $('.item-checkbox').prop('checked', false);
        });
        
        $('.group-selector').click(function(){
            var $this = $(this);
            var target= $($this.data('target'));
            $(target).find('input[type=checkbox]').prop('checked', true);
        });
        
        $('.group-unselector').click(function(){
            var $this = $(this);
            var target= $($this.data('target'));
            $(target).find('input[type=checkbox]').prop('checked', false);
        });
    }
});