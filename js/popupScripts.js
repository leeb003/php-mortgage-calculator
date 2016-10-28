    // field focus 
    $(document).on('focus', '.inTxt', function(e) {
        $(this).addClass('focus');
    });
    $(document).on('blur', '.inTxt', function(e) {
        $(this).removeClass('focus');
    });
   
    // highlight
    $(document).on('mouseenter', '.schedule td', function() {
        var existing = $(this).parent("tr").attr("name");
        $(this).parent("tr").removeClass(existing);
        $(this).parent("tr").addClass('highlight');
    });

    $(document).on('mouseleave', '.schedule td', function() {
        $(this).parent("tr").removeClass('highlight');
        var previous = $(this).parent("tr").attr("name");
        $(this).parent("tr").addClass(previous);
    });
   
    $(document).ready(function(){
        $("table.scroll").createScrollableTable({
            width: '600px',
            height: '400px'
        });
    });

