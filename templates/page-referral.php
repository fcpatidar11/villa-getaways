<?php 
/*
Template Name: Referral
*/

get_header(); ?>

<style type="text/css">
    .form-inline .form-group, .navbar-form .form-group {
        display: inline-block;
        margin-bottom: 0;
        vertical-align: middle;
    }
    label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-weight: bold;
        color: black;
    }
    .row {
        width: 100%;
    }
    #Referrals form h4 {
        font-size: 16px;
        padding: 0px !important;
        margin-top: 30px;
    }
    #Referrals h3,#Referrals h4,  #Referrals p, #Referrals li {
        color: rgba(0, 0, 0, 0.7);
    }
    .col-md-10 {
        display: flex;
        justify-content: space-between;
    }
    .has-error .form-control {
        border-color: #a94442;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
        box-shadow: inset 0 1px 1px rgba(0,0,0,0.075);
    }
    
</style>
<?php
$conn = oracleDbConnection();
$refferals = [];
$client_id = 402411;
if(isset($_GET["refid"])) {
    $client_id = isset($_GET['refid']) ? $_GET['refid'] : "";
    if(isset($_GET['referral']) && $_GET['referral'] && isset($_GET['refid']) && $_GET['refid']) {
        $refferals = $_GET['referral'];
        
        $nonEmptyReferrals = array();
        foreach ($refferals as $referral) {
            if (array_filter($referral)) {
                $nonEmptyReferrals[] = $referral;
                $result[] = insertRefferals($conn, $nonEmptyReferrals, $client_id = 402411);
                $nonEmptyReferrals = array();
            }
        }
        
        $result = array_reduce($result, function ($carry, $item) {
            return $carry && $item;
        }, true);
        if ($result) { ?>
            <script type="text/javascript">
                setTimeout(function(){
                    bootbox.alert('<p>Thank you for submitting your referrals.</p>');
                }, 1000)
                
            </script>
        <?php
        } else { ?>
            <script type="text/javascript">
                setTimeout(function(){
                    bootbox.alert('<p>Something went wrong.</p>');
                }, 1000)
            </script>
        <?php
        }
    }
}
?>

<div class="static-page" id="Referrals">
    <div class="container p-0">
        <div class="row">
            <form method="get" class="form-inline" style="padding: 0 40px 40px; color: #fff;" name="referral_form" id="referral_form">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 left-aside mb-5">
                    <?php the_field('top_content', 'options'); ?>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 left-aside mb-5">
                    <input type="hidden" name="refid" value="<?php echo $client_id; ?>" />
                    <input type="hidden" value="">
                        
                    <div class="row" style="padding-bottom: 20px;">
                    	<div class="col-md-2">
                    		<h4 style="padding: 20px 0;">Referral Friend 1</h4>
                    	</div>
                    	<div class="col-md-10">
                    		<div class="form-group">
                			    <label style="font-weight: normal">Title</label><br>
	                			<select class="form-control" name="referral[1][title]">
	                				<option></option>
	                				<option value="Mr.">Mr.</option>
	                				<option value="Mrs.">Mrs.</option>
	                				<option value="Ms.">Ms.</option>
	                				<option value="Miss.">Miss.</option>
	                				<option value="Dr.">Dr.</option>
	                			</select>
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">First Name</label><br>
                    			<input type="text" class="form-control first-name" name="referral[1][firstname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Last Name</label><br>
                    			<input type="text" class="form-control" name="referral[1][lastname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Email Address</label><br>
                    			<input type="text" class="form-control email-address" name="referral[1][email]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Phone</label><br>
                    			<input type="text" class="form-control phone" name="referral[1][phone_home]">
                    		</div>
                    	</div>
    	            </div>
                	<div class="row" style="padding-bottom: 20px;">
                    	<div class="col-md-2">
                    		<h4 style="padding: 20px 0;">Referral Friend 2</h4>
                    	</div>
                    	<div class="col-md-10">
                    		<div class="form-group">
	                			<label style="font-weight: normal">Title</label><br>
                    			<select class="form-control" name="referral[2][title]">
	                				<option></option>
	                				<option value="Mr.">Mr.</option>
	                				<option value="Mrs.">Mrs.</option>
	                				<option value="Ms.">Ms.</option>
	                				<option value="Miss.">Miss.</option>
	                				<option value="Dr.">Dr.</option>
	                			</select>
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">First Name</label><br>
                    			<input type="text" class="form-control first-name" name="referral[2][firstname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Last Name</label><br>
                    			<input type="text" class="form-control" name="referral[2][lastname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Email Address</label><br>
                    			<input type="text" class="form-control email-address" name="referral[2][email]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Phone</label><br>
                    			<input type="text" class="form-control phone" name="referral[2][phone_home]">
                    		</div>
                    	</div>
                	</div>
                	<div class="row" style="padding-bottom: 20px;">
                    	<div class="col-md-2">
                    		<h4 style="padding: 20px 0;">Referral Friend 3</h4>
                    	</div>
                    	<div class="col-md-10">
                    		<div class="form-group">
	                			<label style="font-weight: normal">Title</label><br>
	                			<select class="form-control" name="referral[3][title]">
	                				<option></option>
	                				<option value="Mr.">Mr.</option>
	                				<option value="Mrs.">Mrs.</option>
	                				<option value="Ms.">Ms.</option>
	                				<option value="Miss.">Miss.</option>
	                				<option value="Dr.">Dr.</option>
	                			</select>
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">First Name</label><br>
                    			<input type="text" class="form-control first-name" name="referral[3][firstname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Last Name</label><br>
                    			<input type="text" class="form-control" name="referral[3][lastname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Email Address</label><br>
                    			<input type="text" class="form-control email-address" name="referral[3][email]">
                    		</div>
                    		<div class="form-group">
                			    <label style="font-weight: normal">Phone</label><br>
                			    <input type="text" class="form-control phone" name="referral[3][phone_home]">
                    		</div>
                    	</div>
                	</div>
                	<div class="row" style="padding-bottom: 20px;">
                    	<div class="col-md-2">
                    		<h4 style="padding: 20px 0;">Referral Friend 4</h4>
                    	</div>
                    	<div class="col-md-10">
                    		<div class="form-group">
	                			<label style="font-weight: normal">Title</label><br>
	                			<select class="form-control" name="referral[4][title]">
	                				<option></option>
	                				<option value="Mr.">Mr.</option>
	                				<option value="Mrs.">Mrs.</option>
	                				<option value="Ms.">Ms.</option>
	                				<option value="Miss.">Miss.</option>
	                				<option value="Dr.">Dr.</option>
	                			</select>
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">First Name</label><br>
                    			<input type="text" class="form-control first-name" name="referral[4][firstname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Last Name</label><br>
                    			<input type="text" class="form-control" name="referral[4][lastname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Email Address</label><br>
                    			<input type="text" class="form-control email-address" name="referral[4][email]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Phone</label><br>
                    			<input type="text" class="form-control phone" name="referral[4][phone_home]">
                    		</div>
                    	</div>
                	</div>	
                	<div class="row" style="padding-bottom: 20px;">
                    	<div class="col-md-2">
                    		<h4 style="padding: 20px 0;">Referral Friend 5</h4>
                    	</div>
                    	<div class="col-md-10">
                    		<div class="form-group">
	                			<label style="font-weight: normal">Title</label><br>
	                			<select class="form-control" name="referral[5][title]">
	                				<option></option>
	                				<option value="Mr.">Mr.</option>
	                				<option value="Mrs.">Mrs.</option>
	                				<option value="Ms.">Ms.</option>
	                				<option value="Miss.">Miss.</option>
	                				<option value="Dr.">Dr.</option>
	                			</select>
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">First Name</label><br>
                    			<input type="text" class="form-control first-name" name="referral[5][firstname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Last Name</label><br>
                    			<input type="text" class="form-control" name="referral[5][lastname]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Email Address</label><br>
                    			<input type="text" class="form-control email-address" name="referral[5][email]">
                    		</div>
                    		<div class="form-group">
	                			<label style="font-weight: normal">Phone</label><br>
                    			<input type="text" class="form-control phone" name="referral[5][phone_home]">
                    		</div>
                    	</div>
                	</div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 left-aside mb-5">
                    <?php the_field('bottom_content', 'options'); ?>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12 left-aside mb-5">
                    <a id="SendReferrals" class="btn btn-lg btn-success" style="width: 100%">Send Referrals</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php get_footer(); ?>

<script type="text/javascript">
    jQuery(document).ready(function($){
    	if ($(window).width() > 600){
       		$('.selectpicker').selectpicker();
      	}
      	
      	function IsEmail(email){
      	    var regex=/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      	    return regex.test(email)
      	}
      	
      	$('#SendReferrals').click(function(e){
      		console.log("Referral Form STARTED");
      		e.preventDefault();
      		var errors = false;
      		var emails = 0;
      		
      		$('.email-address').each(function(){
      			if($(this).val()) {
      				emails++;
    	  			if(!IsEmail($(this).val())) {
    	  				$(this).parent().addClass('has-error')
    	  				errors = true;
    	  			}else {
    	  			    $(this).parent().removeClass('has-error')
    	  			}
    	  			console.log($(this).closest('.col-md-10').find('.first-name')[0].value);
    	  			if($(this).closest('.col-md-10').find('.first-name')[0].value == "") {
    	  			    errors = true;
    	  			    $(this).closest('.col-md-10').find('.first-name').parent().addClass('has-error')
    	  			}else {
    	  			    $(this).closest('.col-md-10').find('.first-name').parent().removeClass('has-error')
    	  			}
    	  		}
      		});
      		
      		if(errors) {
      		    console.log("Referral Form => errors => ", errors);
      			bootbox.alert('<p>Please correct errors.</p>');
      			return false;	
      		}
      		if(!emails) {
      		    console.log("Referral Form => emails => ", emails);
      			bootbox.alert('<p>Before send please fill in at least one email address and first name.</p>');
      			return false;	
      		}
    
            console.log("Referral Form SUBMITTED");
      		$("form#referral_form").submit();
    
      		// $.post(window.location.href,$('#Referrals form').serialize(), function(data){
      		// 	if(data != 'ok') alert('<h4>Error! Something goes wrong. Please try again.</h4>');
      		// 	else {
      		// 		$('#SendReferrals').remove()	
      		// 		$('#Referrals form').replaceWith('<h2 style="color:#fff; margin: 150px 0; text-align: center">Thank you</h2>')
      		// 		window.scrollTo(0,0)
      		// 	} 
      		// })
      	});
    });
</script>