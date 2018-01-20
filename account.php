<?php
/*
Template Name: AUTOKWT Account page 
*/
if(is_user_logged_in()) {
    $totalPostAsPerPlan = getMembershipCustomFields('no_of_cars');
    $totalPosts = getAllUserListingTypePost();
    $haveMembership = pmpro_hasMembershipLevel();
    if (count($totalPosts) <= 0 || !$haveMembership) {
        if ( wp_redirect(site_url().'/membership-account/membership-levels') ) {
            exit;
        }
    }
} else {
    if ( wp_redirect(site_url().'/?action=login') ) {
            exit;
    }  
}
?>
<?php if(stm_is_rental()) {
    if(is_checkout() or is_cart()) {
        get_template_part('partials/rental/reservation', 'archive');
        return false;
    }
} ?>
<?php  get_header(); ?>

<div class="listing-wrapper">
    <div class="listing_page">
        <?php echo do_shortcode('[user_icon]'); ?>
        <?php echo do_shortcode('[user_sidebar]'); ?>
        <div class="profile_content">
            <div class="pr_content_wrap">
                <div class="icon-menu">
                    <i class="fa fa-sliders" aria-hidden="true"></i>
                </div>
         
                <div class="vehicle_listing">
	               <div class="profil-bil">
                        <div class="listng-block b_w mainProgressBar" style="display:none">
                            <div class="container">
                                <div class="progress">
                                  <div class="progress-bar" role="progressbar" aria-valuemax="100">
                                    <span class="sr-only"></span>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#Profile">Profile</a></li>
                            <li><a href="#billing">Billing</a></li>
                            <li><a href="#security">Security</a></li> 
                            <li><a href="#socialmedia">Social Media</a></li>     
                        </ul>

                        <div class="tab-content">
                            <div id="Profile" class="tab-pane fade in active">
                                <?php echo do_shortcode('[profile]'); ?> 
                            </div>

                            <div id="billing" class="tab-pane fade"> 
                                <?php echo do_shortcode('[pmpro_account sections="membership,profile,invoices,links"]'); ?> 
                            </div>
    
                            <div id="security" class="tab-pane fade">
                                <div class="form-profile-dec">
                                    <form method="POST" id="formChangePswd">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label>Old Password:</label>
                                                <span>
                                                    <input type="password" name="oldPswd" id="oldPswd" placeholder="Enter old password" />
                                                </span>
                                            </div>

                                            <div class="form-group">
                                                <label>New Password:</label>
                                                <span>
                                                    <input type="password" name="oldPswd" id="newPswd" placeholder="Enter new password" />
                                                </span>
                                            </div>

                                            <div class="form-group">
                                                <label>Confirm New Password:</label>
                                                <span>
                                                    <input type="password" name="cPswd" id="cPswd" placeholder="Enter new password agian" />
                                                </span>
                                            </div>

                                            <input type="submit" name="sbmt" value="Change password" class="btn btn-primary" style="width:165px !important">
                                        </div>
                                    </form>
                                </div> 
                            </div> 
                           <div id="socialmedia" class="tab-pane fade"> 
                                <?php echo do_shortcode('[socialmedia]'); ?> 
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php // get_template_part('partials/page_bg'); ?>
<?php // get_template_part('partials/title_box'); ?>
<script type="text/javascript" src="<?php echo get_template_directory_uri().'/assets/css/alert/alert.js' ?>"></script>
<script type="text/javascript">
    function printVehicleDetails(url) {
       var printWindow = window.open( url, 'Print', 'left=200, top=200, width=950, height=500, toolbar=0, resizable=0');
        printWindow.addEventListener('load', function(){
            printWindow.print();
            printWindow.close();
        }, true);
    }

    $(document).ready(function(){
        $(".nav-tabs a").click(function(){
            $(this).tab('show');
        });

        $('form#editProfile').submit(function(e){
            e.preventDefault();
            var first_name =   $("input[name=user_firstname]").val();
            var user_lastname =   $("input[name=user_lastname]").val();
            var user_email =   $("input[name=user_email]").val();
            var stm_phone =   $("input[name=stm_phone]").val();
            var description =   $("textarea[name=description]").val();
            $(this).ajaxSubmit({
               url:ajaxurl,
               beforeSubmit:function(){
                    $('.progress-bar').css('width','0%');
                    $('div.mainProgressBar').show();
                },
                data:{
                    action:'updateUserProfile',
                    first_name:first_name,
                    user_lastname:user_lastname,
                    user_email:user_email,
                    stm_phone:stm_phone,
                    description:description,
                },
                uploadProgress:function(event,position,total,percentComplete){
                    var width = percentComplete+'%';
                    $('.progress-bar').css('width',width);
                },
                success:function(response){
                    $('div.mainProgressBar').hide();
                    data = JSON.parse(response);
                    $.alert("Successfully updated.",{
                        autoClose: true,
                        type: 'success',
                        position: ['top-right'],
                        isOnly: true,
                    });
                    if(data.url){
                        $('div.profile_area > img').attr('src',data.url);
                    }     
                } 
            });

        });

        $('form#editsocialmedia').submit(function(e){
            e.preventDefault();
            var facebook =  $("input[name=facebook]").val();
            var twitter =   $("input[name=twitter]").val();
            var linkdin =   $("input[name=linkdin]").val();
            var youtube =   $("input[name=youtube]").val();
            var instagram = $("input[name=instagram]").val();
            var pintrest =  $("input[name=pintrest]").val();
            var tumbler =   $("input[name=tumbler]").val();
   
            $.ajax({
                type:'POST',
                dataType: "json",
                data:{
                    action:'addSocialMedia',
                    facebook:facebook,
                    twitter:twitter,
                    linkdin:linkdin,
                    youtube:youtube,
                    instagram:instagram,
                    pintrest:pintrest,
                    tumbler:tumbler,
                },
                url:ajaxurl,
                beforeSend:function(){
                   // $.alert("Please wait while we are updating your information",{
                       // autoClose: false,
                        //type: 'info',
                        //position: ['top-left'],
                        //isOnly: false,
                    //});
                },
                success:function(response){
                    $('div.alert-info').remove();
                    if(response == '1' || response == 1){
                        $.alert("Successfully updated.",{
                            autoClose: true,
                            type: 'success',
                            position: ['top-right'],
                            isOnly: false,
                        });
                    }     
                }
            });
        });

        $('form#formChangePswd').submit(function(e){
            e.preventDefault();
            var error = false;
            var old = $('#oldPswd').val();
            var newpswd = $('#newPswd').val();
            var confirmPswd = $('#cPswd').val();
            var msg = "";
            if (typeof old == 'undefined' || old == '') {
                error = true;
                msg += "Old password is required.<br />";
            }

            if (typeof newpswd == 'undefined' || newpswd == '') {
                error = true;
                msg += "New password is required.<br />";
            }

            if (typeof confirmPswd == 'undefined' || confirmPswd == '') {
                error = true;
                msg += "Confirm password is required.<br />";
            }

            if (confirmPswd != newpswd) {
                error = true;
                msg += "Password did not match.<br />";   
            }
            if(error == true) {
                $.alert(msg,{
                    autoClose: true,
                    type: 'danger',
                    position: ['top-right'],
                    isOnly: false,
                });
            } else {
                $.ajax({
                    type:'POST',
                    url:ajaxurl,
                    beforeSend:function(){
                        $.alert("Please wait while we update your password",{
                            autoClose: false,
                            type: 'info',
                            position: ['top-right'],
                            isOnly: false,
                        });   
                    },
                    data:{
                        action:'changePassword',
                        oldPswd:old,
                        newPswd:newpswd
                    },
                    success:function(response) {
                        $('.alert-info').remove();
                        if(response == 1 || response == '1') {
                            $.alert("Old password is wrong",{
                                autoClose: true,
                                type: 'danger',
                                position: ['top-right'],
                                isOnly: false,
                            });    
                        }

                        if(response == 2 || response == '2') {
                            $('.alert-danger').remove();
                            $.alert("Password successfully changed",{
                                autoClose: true,
                                type: 'success',
                                position: ['top-right'],
                                isOnly: false,
                            });    
                        }
                    }
                });
            }
        });
    });

</script>

<?php  get_footer(); ?>
