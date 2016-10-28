<?php
/** 
 * Administrative section for the calculator settings
**/
require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit your configuration</title>
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery.betterTooltip.js"></script>
<script src="js/modal.js"></script>
<script>
$(document).ready(function(){
        $('.tTip').betterTooltip({speed: 150, delay: 300});
    });

// Reset the form to original values
    $(document).on('click', '.reset', function(e) {
        $('.formA')[0].reset();
        return false;
    });

    // submit the form
    $(document).on('click', '.submit', function(e) {
        var dPrice = $('.dPrice').val();
        var dInt = $('.dInt').val();
        var ddp = $('.ddp').val();
        var term = $('.term').val();
        var hir = $('.hir').val();
        var pmi = $('.pmi').val();
        var layout = $('.layout').val();
        var website = $('.website').val();
        var url = $('.url').val();
        var email = $('.email').val();
        var fromEmail = $('.fromEmail').val();
        var allowEmail = $('.allowEmail').val();
        var blurb = $('.blurb').val();
        var disclaimer = $('.disclaimer').val();

        var type = 'adminSubmit';
        var dataString = "type=" + type + "&dPrice=" + dPrice + "&dInt=" + dInt 
                       + "&ddp=" + ddp + "&term=" + term + "&hir=" + hir
                       + "&pmi=" + pmi + "&layout=" + layout + "&website=" + website
                       + "&url=" + url + "&email=" + email + "&allowEmail=" + allowEmail
                       + "&fromEmail=" + fromEmail + "&blurb=" + blurb + "&disclaimer=" + disclaimer;
        $.ajax({
            type: "GET",
            dataType: 'jsonp',             
            data: dataString,
            url: "calc-ws.php",  
            success: function(response) {
                if(response) {
                    if(response.error == 1) {  // handle errors
                        modal.open({content: response.message}); //need to size for error messages

                    } else {
                        modal.open({content: 'Settings Saved!'});
                        return false;
                    }
                }
            }
        });
        return false;
    });

</script>

</head>
<body>
<div id="adminWrapper">
    
  <h1>Administrative settings for the Mortgage Calculator</h1>
  <div style="height: 40px;"></div>

  <div class="section">
  <p class="sep">Defaults</p>
  <form class="formA">
    <table id="admin">
      <tr>
        <td>
          Default Home Price:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="The Default Home price to set. (format: 225000.00)" />
        </td>
        <td>
          <input class="dPrice  adminTxt" value="<?php echo DPRICE; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Default Interest Rate:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="The Default Interest Rate to set. (format: 5.5)" />
        </td>
        <td>
          <input class="dInt adminTxt" value="<?php echo DINT; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Default Down Payment:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="The Default Down Payment to set. (format: 10)" />
        </td>
        <td>
          <input class="ddp adminTxt" value="<?php echo DDP; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Default Term of Loan:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="The Default Term of the loan to set." />
        </td>
        <td>
          <select class="term adminSel">
          <?php
            for ($i = 5; $i<=50; $i+=5) {
                echo '<option value="' . $i . '"';

                if ($i == DTERM) {
                    echo ' selected="selected" ';
                }
                echo '>' . $i . '</option>' . "\n";
            }
          ?>
          </select>
        </td>
      </tr>
    </table>

    <p class="sep">Other loan Factors</p>

    <table id="admin1">
      <tr>
        <td>
          Homeowners Insurance Rate:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="The Homeowners Insurance monthly rate average. (format: 55)" />
        </td>
        <td>
          <input class="hir adminTxt" value="<?php echo HIR; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          PMI Amount:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="The Private Mortgage Insurance rate per 100,000.00. (format: 55)" />
        </td>
        <td>
          <input class="pmi adminTxt" value="<?php echo PMI; ?>" />
        </td>
      </tr>
    </table>
    </div>
    
    <div class="section2">
    <p class="sep">Site Settings</p>
    
    <table id="admin2">
      <tr>
        <td>
          Layout:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Choose the way the Mortgage schedule is presented on submit (div, modal, popup)." />
        </td>
        <td>
          <select class="layout adminSel">
            <option value="div" <?php echo LAYOUT == 'div' ? 'selected="selected"' : ''; ?>>div</option>
            <option value="modal" <?php echo LAYOUT == 'modal' ? 'selected="selected"' : ''; ?>>Modal</option>
            <option value="popup" <?php echo LAYOUT == 'popup' ? 'selected="selected"' : ''; ?>>Popup Window</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Website Address:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Used in pdf emails to remind your customers where they came from. (format: www.yoursite.com)" />
        </td>
        <td>
          <input class="website adminTxt" value="<?php echo WEBSITE; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          URL to Script location:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Folder url for these files, no trailing slashes. format: http://www.website.com/calc" />
        </td>
        <td>
          <input class="url adminTxt" value="<?php echo URL; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          Allow Email?:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Used to let customers select if they would like to recieve an email with PDF of Schedule." />
        </td>
        <td>
          <select class="allowEmail">
            <option value="yes" <?php echo ALLOWEMAIL == 'yes' ? 'selected="selected"' : ''; ?>>Yes</option>
            <option value="no" <?php echo ALLOWEMAIL == 'no' ? 'selected="selected"' : ''; ?>>No</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          Your Email address:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Used to bcc you, so you can contact/track your customers. (format: you@somewhere.com)" />
        </td>
        <td>
          <input class="email adminTxt" value="<?php echo EMAIL; ?>" />
        </td>
      </tr>
      <tr>
        <td>
          From Email address:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title= "Used for the FROM email address field in your messages going to customers. (format: you@somewhere.com" />
        </td>
        <td>
          <input class="fromEmail adminTxt" value="<?php echo FROMEMAIL; ?>" />
        </td>
      </tr>
      <!-- Maybe in a future release
      <tr>
        <td>
          Show Blurb:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Option to give information about other costs before schedule." />
        </td>
        <td>
          <select class="blurb">
            <option value="yes" <?php echo SHOWBLURB == 'yes' ? 'selected="selected"' : ''; ?>>Yes</option>
            <option value="no" <?php echo SHOWBLURB == 'no' ? 'selected="selected"' : ''; ?>>No</option>
          </select>
        </td>
      </tr>
      -->
      <tr>
        <td>
          Disclaimer:
        </td>
        <td>
          <img src="css/images/information.png" class="tTip" id="info1" 
          title="Calculator Disclaimer." />
        </td>
        <td>
          <textarea class="disclaimer" rows="6" cols="40"><?php echo DISCLAIMER; ?></textarea>
        </td>
      </tr>
      <tr>
        <td>
        </td>
        <td>
        </td>
        <td class="buttonRow" colspan="2">
          <button class="submit buttons greenButton">Submit</button>
          <button class="reset buttons blueButton">Reset</button>
       </td>
      </tr>
    </table>
    </div>
</body>
</html>

