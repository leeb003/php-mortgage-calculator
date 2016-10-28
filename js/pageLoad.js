jQuery(function ($) {  // use $ for jQuery

    $(document).ready(function(){
        $('.tTip').betterTooltip({speed: 150, delay: 300});
    });

    //loading
    function showLoading() {
        timer = setTimeout(function() {
            $('.waiting').show();
        }, 250);
    };
    function hideLoading() {
        $('.waiting').hide();
        clearTimeout(timer);
    };

    // field focus 
    $(document).on('focus', '.inTxt', function(e) {
        $(this).addClass('focus');
    });
    $(document).on('blur', '.inTxt', function(e) {
        $(this).removeClass('focus');
    });
   
    // Reset the form to original values
    $(document).on('click', '.reset', function(e) {
        $('.inputF')[0].reset();
        $('#schedule').empty();
        $('.sendEmail').prop('checked', false);
        $('.emailTD').hide();
        return false;
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

    // show the email box
    $(document).on("click", '.sendEmail', function() {
        var checked = false;
        if ($(this).is(':checked')) {
            checked = true;
            $('.emailTD').show();
        } else {
            $('.emailTD').hide();
        }
    });

    // submit the form
    $(document).on('click', '.submit', function(e) {
        var priceHome = $('.priceHome').val();
        var interest = $('.interest').val();
        var downPay = $('.downPay').val();
        var term = $('.term').val();
        var type = 'getCalc';
        var email = 'no';
        var eAddress = $('.email').val();
        if ($('.sendEmail').is(':checked')) {
            email = 'yes';
        }
        var dataString = "type=" + type + "&priceHome=" + priceHome + "&interest=" + interest 
                       + "&downPay=" + downPay + "&term=" + term + "&email=" + email
                       + "&eAddress=" + eAddress;
        $.ajax({
            type: "GET",
            url: siteUrl + '/calc-ws.php',  
            dataType: 'jsonp',                 // Using jsonp to avoid crossdomain problems e.g. www.site.com vs site.com
            data: dataString,  
            success: function(response) {
                if(response) {
                    if(response.error == 1) {  // handle errors
                        modal.open({content: response.message});
                        hideLoading();
                        //alert(response.message);

                    } else {
                        var data = '<div class="innerContent"><h1>Mortgage Information</h1>'
                                 + '<p>Amortization Schedule (P & I)</p>'
                                 + '<table class="schedule scroll">'
                                 + '<thead>'
                                 + '<tr><th>Payment</th><th>Interest</th><th>Principal</th><th>New Mortgage</th></tr>'
                                 + '</thead><tbody>';
                        $.each(response.payment, function(key, val) { 
                            if (val.value % 2 == 0) {
                                data += '<tr class="evenRow" name="evenRow">';
                            } else {
                                data += '<tr class="oddRow" name="oddRow">';
                            }
                            data += '<td>' + val.value + '</td><td>$' + val.interest 
                                  + '</td><td>$' + val.principal + '</td><td>$' + val.newMortgage + '</td></tr>';
                        });
                        data += '</tbody></table>';
                        data += response.blurb;
                        data += '</div>';
                        if (layout == 'modal') {
                            modal.open({content: data});
                        } else if (layout == 'popup') {
                            popup(data);
                        } else {
                            $('#schedule').html(data);
                        }
                        /* make the table scrollable with a fixed header */
                        $("table.scroll").createScrollableTable({
                            width: '600px',
                            height: '400px'
                        });
                        $('.sendEmail').prop('checked', false);
                        $('.emailTD').hide();
                        hideLoading();
                        return false;
                    }
                }
            }
        });
        showLoading();
        return false;
    });

/*  // Alternate to popup, new window/tab
    function popup(data) {
        var w = window.open();
        var content = '<!DOCTYPE html><html><head><title>Mortgage Information</title>'
                    + '<link href="' + siteUrl + 'css/style.css" rel="stylesheet" type="text/css" media="all" />'
                    + unescape("%3Cscript src='"
                    + siteUrl + "/js/jquery-1.9.1.min.js' type='text/javascript'%3E%3C/script%3E")
                    + unescape("%3Cscript src='"  
                    + siteUrl + "/js/scrolltable.js' type='text/javascript'%3E%3C/script%3E")
                    + unescape("%3Cscript src='"
                    + siteUrl + "/js/popupScripts.js' type='text/javascript'%3E%3C/script%3E")
                    + '</head><body><div id="wrapper">' + data + '</div></body></html>';
                    
        w.document.write(content);
        w.document.close();
    };
*/

    function popup(data) {
        var content = '<!DOCTYPE html><html><head><title>Mortgage Information</title>'
                    + '<link href="' + siteUrl + '/css/style.css" rel="stylesheet" type="text/css" media="all" />'
                    + unescape("%3Cscript src='"
                    + siteUrl + "/js/jquery-1.9.1.min.js' type='text/javascript'%3E%3C/script%3E")
                    + unescape("%3Cscript src='"
                    + siteUrl + "/js/scrolltable.js' type='text/javascript'%3E%3C/script%3E")
                    + unescape("%3Cscript src='"
                    + siteUrl + "/js/popupScripts.js' type='text/javascript'%3E%3C/script%3E")
                    + '</head><body><div id="calcWrapper">' + data + '</div></body></html>';


        var generator= open('','results', 'height=700, width=750, scrollbars=1');
        generator.document.write(content);
        generator.document.close();
   };

});
