<?php
    require_once 'config.php';  // Get the configurable items
?>

  <div id="calcWrapper">
    <link href="<?php echo URL; ?>/css/style.css" rel="stylesheet" type="text/css" media="all" />
    <!--[if lte IE 8]>
    <link rel="stylesheet" type="text/css" href="<?php echo URL; ?>/css/ie7.css">
    <![endif]-->
    <script src="<?php echo URL; ?>/js/jquery-1.9.1.min.js"></script>
    <script src="<?php echo URL; ?>/js/jquery.betterTooltip.js"></script>
    <script src="<?php echo URL; ?>/js/scrolltable.js"></script>
    <script src="<?php echo URL; ?>/js/modal.js"></script>
    <script type="text/javascript">
        var layout = '<?php echo LAYOUT; ?>';   // Where is the table going to appear
        var siteUrl = '<?php echo URL; ?>';         // url for popup scripts
    </script>
    <script src="<?php echo URL; ?>/js/pageLoad.js"></script>

   <div class="formDiv">
    <h1>Mortgage and Amortization Calculator</h1>
    <p>
      This calculator will show you the amortization schedule and breakdown of your payments made towards a home loan.
    </p>

    <form class="inputF">
     <table id="selections">

     <?php if (ALLOWEMAIL == 'yes') { ?>
      <tr>
        <td>
          Send A PDF Report to your email?
        </td>
        <td>
          <input type="checkbox" class="sendEmail" />
        </td>
        <td class="emailTD" colspan="2">
          <input type="text" class="email" placeholder="Your Email"/>
        </td>
      </tr>
     <?php } ?>

      <tr>
        <td>
          Purchase Price: 
        </td>
        <td>
          <img src="<?php echo URL; ?>/css/images/information.png" class="tTip" id="info1" 
          title="The total purchase price of the home you wish to buy." />
        </td>
        <td class="entry">
          <input type="text" class="priceHome inTxt" value="<?php echo DPRICE;?>" size="10" /> 
        </td>
        <td class="symbol">
         <div class="expl">
           $
         </div>
        </td>
      </tr>
      <tr>
        <td>
          Interest Rate: 
        </td>
        <td>
          <img src="<?php echo URL; ?>/css/images/information.png" class="tTip" id="info2" 
          title="The expected percent interest rate you will get on your mortgage." />
        </td>
        <td class="entry">
          <input type="text" class="interest inTxt" value="<?php echo DINT;?>" size="5" />
        </td>
        <td class="symbol">
         <div class="expl">
           %
         </div>
        </td>
      </tr>
      <tr>
        <td>
          Down Payment: 
        </td>
        <td>
          <img src="<?php echo URL; ?>/css/images/information.png" class="tTip" id="info3" 
          title="The percent down payment you wish to put towards the home." />
        </td>
        <td class="entry">
          <input type="text" class="downPay inTxt" size="3" value="<?php echo DDP; ?>" />
        </td>
        <td class="symbol">
         <div class="expl">
           %
         </div>
        </td>
      </tr>
      <tr>
        <td>
          Term: 
        </td>
        <td>
          <img src="<?php echo URL; ?>/css/images/information.png" class="tTip" id="info4" 
          title="The number of years it will take to repay the loan amount (30 years is normal)." />
        </td>
        <td class="entry">
          <select class="term inSel">
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
        <td class="symbol">
         <div class="expl">
           Years
         </div>
        </td>
      </tr>
      <tr>
        <td>
        </td>
        <td>
        </td>
        <td class="buttonRow" colspan="2">
         <button class="submit buttons greenButton">Calculate</button> 
         <button class="reset buttons blueButton">Reset</button>
       </td>
      </tr>
     </table>
    </form>
    </div>

    <div id="schedule"></div>

    <div id="calcFooter">
      <p>
        <?php echo DISCLAIMER; ?>
      </p>
    </div>  
  
  </div>
  <div class="waiting">
    <div class="center">
      <img src="<?php echo URL; ?>/css/images/ajax-loader.gif" alt="loading" />&nbsp;&nbsp;Please Wait...
    </div>
  </div>
