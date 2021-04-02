<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}
// echo "<pre>";print_r($mbrstr);exit;
if($mbrstr['ewallet']>='1000.00' && $mbrstr['ewallet']<'5000.00'){

    $level_amount= (1000/100)*1.1;
}
if($mbrstr['ewallet']>='5000.00' && $mbrstr['ewallet']<'10000.00'){

    $level_amount= (5000/100)*2.1;
}
if($mbrstr['ewallet']>='10000.00' && $mbrstr['ewallet']<'20000.00'){

    $level_amount= (10000/100)*3.1;
}
if($mbrstr['ewallet']>='20000.00'){

    $level_amount= (20000/100)*4.1;
}

$wallet= $mbrstr['ewallet']+$level_amount;

$getstartstr = base64_decode($cfgrow['getstart']);

?>
<style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Roboto:300,400);
.button-wrap {
  position: relative;
  text-align: center;
  .btn {
    font-family: 'Roboto', sans-serif;
    box-shadow: 0 0 15px 5px rgba(0, 0, 0, 0.5);
    border-radius: 0px;
    border-color: #222;
    cursor: pointer;
    text-transform: uppercase;
    font-size: 1.1em;
    font-weight: 400;
    letter-spacing: 1px;
    small {
      font-size: 0.8rem;
      letter-spacing: normal;
      text-transform: none;
    }
  }
}


/** SPINNER CREATION **/

.loader {
  position: relative;
  text-align: center;
  margin: 15px auto 35px auto;
  z-index: 9999;
  display: block;
  width: 80px;
  height: 80px;
  border: 10px solid rgba(0, 0, 0, .3);
  border-radius: 50%;
  border-top-color: #000;
  animation: spin 1s ease-in-out infinite;
  -webkit-animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to {
    -webkit-transform: rotate(360deg);
  }
}

@-webkit-keyframes spin {
  to {
    -webkit-transform: rotate(360deg);
  }
}


/** MODAL STYLING **/

.modal-content {
  border-radius: 0px;
  box-shadow: 0 0 20px 8px rgba(0, 0, 0, 0.7);
}

.modal-backdrop.show {
  opacity: 0.2;
}

.loader-txt {
  p {
    font-size: 13px;
    color: #666;
    small {
      font-size: 11.5px;
      color: #999;
    }
  }
}

#output {
  padding: 25px 15px;
  background: #222;
  border: 1px solid #222;
  max-width: 350px;
  margin: 35px auto;
  font-family: 'Roboto', sans-serif !important;
  p.subtle {
    color: #555;
    font-style: italic;
    font-family: 'Roboto', sans-serif !important;
  }
  h4 {
    font-weight: 300 !important;
    font-size: 1.1em;
    font-family: 'Roboto', sans-serif !important;
  }
  p {
    font-family: 'Roboto', sans-serif !important;
    font-size: 0.9em;
    b {
      text-transform: uppercase;
      text-decoration: underline;
    }
  }
}
</style>

<div class="section-header">
    <h1><i class="fa fa-fw fa-flag-checkered"></i> Click & Earn</h1>
</div>

<div class="section-body">
            <div class="card">
              <div id="success"></div>
                <div class="card-body">
                    <div class="row">
                      
                    <div class="col-sm-6">
                    <div class="button-wrap">
                    <button type="button" class="btn btn-success btn-lg btn-block btn-icon-split" id="load_me_baby">
                    <i class="far fa-money-bill-alt"></i> Click & Earn
                  </button>
                    </div>


                  </div>
                  <div class="col-sm-6 pt-2 text-center">
                    <input type="hidden" name="" id="counter" value="<?php echo $mbrstr['counter'] ?>">
                      <h4><?php echo $mbrstr['counter'] ? $mbrstr['counter'] : '0'?>/20</h4>
                  </div>

                </div>

            </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade pt-5" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body text-center">
        <div class="loader"></div>
        <div clas="loader-txt">
          <p>Please Wait and Watch for the moment. <br><br><small>We are mining money and will add to your account... #love</small></p>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
  $("#just_load_please").on("click", function(e) {

    e.preventDefault();
    $("#loadMe").modal({
      backdrop: "static", //remove ability to close modal with click
      keyboard: false, //remove option to close with keyboard
      show: true //Display loader!
    });
    setTimeout(function() {
      $("#loadMe").modal("hide");
    }, 3500);
  });
  //ajax code here (example for $.post) using test page from https://reqres.in
  //Adding a delay so we can see the functionality of the loader while request processes
  $("#load_me_baby").on("click", function(e) {
   var counterNumber= $('#counter').val();
    if(counterNumber>=20){
      alert("Today Earning Quota is End");
      return false;
      exit();
    }
    e.preventDefault();
    $("#loadMe").modal({
      backdrop: "static", //remove ability to close modal with click
      keyboard: false, //remove option to close with keyboard
      show: true //Display loader!
    });
    var testUrl = "<?php echo SURL;?>/member/bonuspay.php";
    var user_id= '<?php echo $mbrstr['id'];?>';
    var counter= '<?php echo $mbrstr['counter']+1;?>';
    var package= '<?php echo $mbrstr['package'];?>';
    var earnedAmount= '<?php echo $level_amount;?>';
    var wallet= '<?php echo $wallet;?>';
    var mbrstr= '<?php echo json_encode($mbrstr);?>';
/*Wait fundtion start here*/
setTimeout(
  function() 
  {
    $.post(
      testUrl,
       {
        user_id: user_id,
        counter: counter,
        earnedAmount:earnedAmount,
        wallet:wallet,
        mbrstr:mbrstr,
        },
      function(response) {
        if (response=='1') {
          console.log(response);
          
          $("#loadMe").modal("hide");
              $('#success').html('<div class="alert alert-info"><strong>Success!</strong> wahoooo! Bonus Amount Earned</div>');
           setTimeout(
  function() 
  {
               window.location = '<?php echo SURL?>/member/index.php?hal=getstarted';

             },3000)

        } else {
             
        }
      },
      "json"
    );

      }, 30000);
    /*Wait funtion end here*/

  });
});

</script>
