# wp_db
query
function.php

function socialmedia()
{
    $userId = get_current_user_id();

  ?>

  <form method="post" id="editsocialmedia">
  <div class="row">


  <div class="col-sm-12">
  <div class="form-group">
  <label>Facebook:</label><span> <input type="url" name="facebook" value="<?php echo get_user_meta($userId,'facebook',true); ?> " class="form-control" ></span>
 </div>
  </div>

  <div class="col-sm-12">
  <div class="form-group">
          <label>Twitter:</label><span> <input type="url" name="twitter" value=" <?php echo get_user_meta($userId,'twitter',true);?>" class="form-control" ></span>
        </div>
  </div>
    <div class="col-sm-12">
  <div class="form-group">
          <label>Linkedin:</label><span> <input type="url" name="linkdin" value=" <?php echo get_user_meta($userId,'linkdin',true); ?>" class="form-control" ></span>
        </div>
  </div>
  <div class="col-sm-12">
  <div class="form-group">
          <label>YouTube:</label><span> <input type="url" name="youtube" value=" <?php  echo  get_user_meta($userId,'youtube',true); ?>" class="form-control" ></span>
        </div>
  </div>
  <div class="col-sm-12">
  <div class="form-group"><label>Instagram:</label><span> <input type="url" name="instagram" value=" <?php  echo  get_user_meta($userId,'instagram',true); ?>" class="form-control"></span>
        </div>
  </div>
<div class="col-sm-12">
  <div class="form-group">
  <label>Pinterest:</label><span> <input type="url" name="pintrest" value=" <?php  echo get_user_meta($userId,'pintrest',true); ?>" class="form-control" ></span>
        </div>
  </div>
  <div class="col-sm-12">
  <div class="form-group">
          <label>Tumblr:</label><span> <input type="url" name="tumbler" value=" <?php  echo  get_user_meta($userId,'tumbler',true); ?>" class="form-control" ></span>
        </div>
  </div>

  <div class="form-group">
        <input type="submit" name="submit" value="Submit"> 
  </div>

</div>
</form>
 
<?php
}
add_shortcode( 'socialmedia', 'socialmedia' );


function addSocialMedia(){
     global $current_user;
      get_currentuserinfo();
      $userId = get_current_user_id();
      //echo $userId;
      $facebook = $_POST['facebook'];  
      $twitter = $_POST['twitter'];
      $linkdin = $_POST['linkdin'];
      $youtube = $_POST['youtube'];
      $instagram = $_POST['instagram'];
      $pintrest = $_POST['pintrest'];
      $tumbler = $_POST['tumbler'];

    
       
    $getUserMetaFb = get_user_meta($userId,'facebook',true);
      if(empty($getUserMetaFb)){
            $facebook = add_user_meta($userId,'facebook',$facebook);
      } else {
            $facebook = update_user_meta($userId,'facebook',$facebook);
      }
   

    $getUserMetaTw = get_user_meta($userId,'twitter',true);
      if(empty($getUserMetaTw)){
            $twitter = add_user_meta($userId,'twitter',$twitter);
      } else {
            $twitter = update_user_meta($userId,'twitter',$twitter);
      }

      
    $getUserMetaLi = get_user_meta($userId,'linkdin',true);
      if(empty($getUserMetaLi)){
            $linkdin = add_user_meta($userId,'linkdin',$linkdin);
      } else {
            $linkdin = update_user_meta($userId,'linkdin',$linkdin);
      }


    $getUserMetaYou = get_user_meta($userId,'youtube',true);
      if(empty($getUserMetaYou)){
            $youtube = add_user_meta($userId,'youtube',$youtube);
      } else {
            $youtube = update_user_meta($userId,'youtube',$youtube);
      }

    $getUserMetaIn = get_user_meta($userId,'instagram',true);
      if(empty($getUserMetaIn)){
            $instagram = add_user_meta($userId,'instagram',$instagram);
      } else {
            $instagram = update_user_meta($userId,'instagram',$instagram);
      }

    $getUserMetaPin = get_user_meta($userId,'pintrest',true);
      if(empty($getUserMetaPin)){
            $pintrest = add_user_meta($userId,'pintrest',$pintrest);
      } else {
            $pintrest = update_user_meta($userId,'pintrest',$pintrest);
      }

    $getUserMetaTum = get_user_meta($userId,'tumbler',true);
      if(empty($getUserMetaTum)){
            $tumbler = add_user_meta($userId,'tumbler',$tumbler);
      } else {
            $tumbler = update_user_meta($userId,'tumbler',$tumbler);
      }
      echo 1;
      die();
     
}
add_action('wp_ajax_addSocialMedia', 'addSocialMedia');
add_action('wp_ajax_nopriv_addSocialMedia', 'addSocialMedia');


account.php
------------------
<script type="text/javascript">
$(document).ready(function(){  
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
       </script> 
