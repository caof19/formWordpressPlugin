jQuery(function($){
    function addEmail(container){
        var idx = container.find('input[type="email"]').length;
        container.append('<input type="email" name="'+container.data('name')+'['+idx+']" placeholder="Email" />');
    }
    function addTopic(container){
        var idx = container.find('.cfp-topic').length;
        var html = '<div class="cfp-topic"><input type="text" name="'+container.data('name')+'['+idx+'][name]" placeholder="Topic name" />';
        html += '<div class="cfp-emails" data-name="'+container.data('name')+'['+idx+'][emails]">';
        html += '<button class="button add-email">Add Email</button></div></div>';
        container.append(html);
    }
    $('#cfp-topic-sets').on('click','.add-email',function(e){
        e.preventDefault();
        var cont = $(this).closest('.cfp-emails');
        addEmail(cont);
    });
    $('#cfp-topic-sets').on('click','.add-topic',function(e){
        e.preventDefault();
        var cont = $(this).closest('.cfp-topics');
        addTopic(cont);
    });
    $('#cfp-add-set').on('click',function(e){
        e.preventDefault();
        var idx = $('#cfp-topic-sets .cfp-set').length;
        var html = '<div class="cfp-set"><h2>Set</h2><input type="text" name="sets['+idx+'][name]" placeholder="Set name" />';
        html += '<div class="cfp-topics" data-name="sets['+idx+'][topics]"><button class="button add-topic">Add Topic</button></div>';
        html += '</div>';
        $('#cfp-topic-sets').append(html);
    });
    $('#cfp-add-agreement').on('click',function(e){
        e.preventDefault();
        var idx = $('#cfp-agreements .cfp-agreement').length;
        $('#cfp-agreements').append('<div class="cfp-agreement"><input type="text" name="agreements['+idx+'][text]" class="regular-text" placeholder="Agreement text" /> <label><input type="checkbox" name="agreements['+idx+'][required]" /> Required</label></div>');
    });
});
