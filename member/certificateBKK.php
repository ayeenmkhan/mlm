<?php 
include_once('../common/init.loader.php');
?>
<style type="text/css">
  /*@import url('https://fonts.googleapis.com/css?family=Saira+Condensed:700');*/

hr {
  background-color: #be2d24;
  height: 3px;
  margin: 5px;
}

div#cert-footer {
  position: absolute;
  width: 60%;
  top: 550px;
  text-align: center;
}

#cert-stamp, #cert-ceo-sign {
  width: 60%;
  display: inline-block;
}

div#cert-issued-by, div#cert-ceo-design {
  width: 40%;
  display: inline-block;
  float: left;
}

div#cert-ceo-design {
  margin-left: 10%;
}

h1 {
  font-family: 'Saira Condensed', sans-serif;
  margin: 5px 0px;
}


p {
  font-family: 'Arial', sans-serif;
  font-size: 18px;
  margin: 5px 0px;
}


h1#cert-holder {
  font-size: 50px;
  color: #1975CF;
  text-align: center;
}

p.smaller {
  font-size: 17px !important;
}

div#cert-desc {
  width: 70%;
}

p#cert-from {
  color: #1975CF;
  font-family: 'Saira Condensed', sans-serif;
}

div#cert-verify {
  opacity: 1;
  position: absolute;
  top: 680px;
  left: 60%;
  font-size: 12px;
  color: grey;

}
#cert-course{
    text-align: center;;
}
.certrow{
    border: 10px solid #1975CF;
    border-radius: 20px;
    height: 462px;
}
.upper{

    position: relative;
    margin-top: 55px;

}
</style>
<?php $cname= getbundleNameByID($_SESSION['course_id']); ?>
<?php $username= getuserNameByID($_SESSION['username']); ?>
<button class="btn btn-info" onclick="print()" style="
    margin-bottom: 7px;
    border-radius: 30px;
    position: relative;
    left: 42rem;
    background: #1975cf;
    border: #1975cf;
">Download</button>
<div id="contnet">
<div class="row certrow">
<div class="col-md-12 upper">
<p class="smaller text-center" id="cert-declaration">
  THIS IS TO CERTIFY THAT
</p>
<br>
<p class="text-center"><h1 id="cert-holder">
<?php echo $username[0]['firstname']." ".$username[0]['lastname'];?>
</h1></p>

<p class="smaller text-center" id='cert-completed-line'>
  has successfully completed the
</p>

<h2 id="cert-course">
  <?php echo $cname[0]['bundle_name'];?>
</h2>

<div
  <p class="smaller text-center" id='cert-details'>
    By continuing to learn, you have expanded your perspective, sharpened your skills, and made yourself even more in demand.
  </p>
</div>


<br>
<div class="form-inline">
<p id="cert-from" class="smaller">
 www.immortalsuccess.com
</p>
<p class="smaller" id='cert-issued' style="position: relative;left: 346px;">
 <b>Issued on:</b> <?php echo date('d/m/Y');?>
</p>
</div>
</div>
</div>
</div>

<script type="text/javascript">
    function print (){
var doc = new jsPDF({
    // orientation: 'landscape'
});

var elementHTML = $('#contnet')[0];
var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
};
doc.fromHTML(elementHTML, 15, 15, {
    // 'width': 120,
    'elementHandlers': specialElementHandlers
});

// Save the PDF
doc.save('course-completion-certificate.pdf');

   // var pdf = new jsPDF('p', 'pt', 'letter');
   //      var options = {
   //          background: '#fff' //background is transparent if you don't set it, which turns it black for some reason.
   //      };
   //      pdf.addHTML($('#content')[0], function () {
   //              pdf.save('Test.pdf');
   //      });
    }
</script>
