<?php
/*
Template Name: AUTOKWT Add Car Form
*/
if(!is_user_logged_in()) {
	if ( wp_redirect(site_url().'/?action=login') ) {
            exit;
    }  
}
 get_header(); 
 // echo "<pre>";
 // print_r($_POST);

?>
<script type="text/javascript" src="<?php echo get_template_directory_uri().'/assets/css/colour_picker/colors.js' ?>"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri().'/assets/css/colour_picker/jqColorPicker.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri().'/assets/css/alert/alert.js' ?>"></script>
<div class="stm_add_car_form">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<form method="post" action="#" id="stm_sell_a_car_form" enctype="multipart/form-data">
				<div class="single-listing-car-inner listing-view">
					<!-- Image Upload section -->
					<div class="stm-form-3-photos clearfix">
						<div class="stm-add-media-car">
	                        <div class="stm-media-car-main-input">
	                            <input type="file" name="stm_car_gallery_add[]" multiple/>

	                            <div class="stm-placeholder">
	                                <i class="stm-service-icon-photos"></i>
	                                <a href="#" class="button stm_fake_button"><?php esc_html_e('Choose files', 'motors'); ?></a>
	                            </div>
                                
                                <div class="ancc">
                                	<ul>
                                    	<li class="liGear"><a href="#" class="gear">A</a></li>
                                        <li class="liHandShake"><a href="#" class="handshake">N</a></li>
                                        <li class="liCertified"><a href="#" class="certified">C</a></li>
                                        <li class="liCondition"><a href="#" class="condition">C</a></li>
                                    </ul>
                                </div>
	                        </div>
	                       
							<div class="anc-car-title listng-block b_b listng-block-edit carMake">
								<span class="eyeIcon"><i class="fa fa-eye" ></i></span>
								<input type="checkbox" value="0" name="isShowMake" class="isShowFields" />
								<div class="container">
								 	<div class="col-md-4 col-sm-4 col-xs-4">
									
									</div>
									 <div class="col-md-4 col-sm-4 col-xs-4 c_m">
										<div class="form-group">
											<input type="text" class="form-control cust_form make-vehicle" id="VehicleMake" name="VehicleMake" placeholder="Vehicle Make" value="<?php echo (isset($_POST['auto_make'])) ? ucwords($_POST['auto_make']) : '' ?>">
										</div>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4">
									 
									 </div>
								</div>
							</div>
						
							<div class="anc-car-type listng-block b_w listng-block-edit">
								<span class="eyeIcon"><i class="fa fa-eye" ></i></span>
								<input type="checkbox" value="0" name="isShowVMMBLY" class="isShowFields" />
								<div class="container">
									<div class="col-md-4 col-sm-4 col-xs-4">
										<div class="form-group">
											<input type="text" class="form-control cust_form make-vehicle-model" id="VehicleModel" name="VehicleModel" placeholder=" Vehicle Model" value="<?php echo (isset($_POST['auto_series'])) ? ucwords($_POST['auto_series']) : '' ?>" onkeyup="loadModelAutoCompleteMake()">
										</div>
								 	</div>
									 <div class="col-md-4 col-sm-4 col-xs-4 c_m">
										<div class="form-group">
											<?php
												global $wpdb;
												$optionModelBodyStyles = "";
												if (isset($_POST['auto_series'])) {
													$getModelBodyStyles = $wpdb->get_results("SELECT DISTINCT `model_body_styles` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
													if(sizeof($getModelBodyStyles) > 0) {
														$optionModelBodyStyles .= $getModelBodyStyles[0]->model_body_styles;
													} else {
														$optionModelBodyStyles .= "Model Body Styles not avalilable";
													}
												}
											?>
											<input type="text" class="form-control cust_form make-vehicle-model text-center" id="vehicleModelBodyStyles" name="vehicleModelBodyStyles" placeholder="Model Body Styles" value="<?php echo ucwords($optionModelBodyStyles); ?>" onkeyup="loadModelBodyStylesAutoComplete()">
										</div>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 c_l">
										<div class="form-group">
											<?php
												global $wpdb;
												$optionYear = "";
												if(isset($_POST['auto_series'])) {
													$sqlGetLaunchYear =  $wpdb->get_results("SELECT DISTINCT `launch_year` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
													if (sizeof($sqlGetLaunchYear) > 0) {
														$optionYear .= $sqlGetLaunchYear[0]->launch_year;
													} else {
														$optionYear .= "Launch year is not available";
													}
												}
											?>
											<input type="text" class="form-control cust_form make-vehicle-model text-right" id="VehicleLaunchYear" name="VehicleLaunchYear" placeholder=" Launch Year" value="<?php echo ucwords($optionYear); ?>" onkeyup="loadLaunchYearAutoComplete()">
										</div>
								 	</div>
								</div> 
							</div>     
				
							<div class="anc-car-color listng-block b_b listng-block-edit colors">
								<span class="eyeIcon" style="margin:1px 0px 8px 34px !important"><i class="fa fa-eye"></i></span>
								<input type="checkbox" value="0" name="isShowColors" class="isShowFields" />
								<div class="container">
							 		<div class="col-md-4 col-sm-4 col-xs-4">
										<div class="">
											<label for="">Interior Color</label>
											<input type="text" name="vehicleInteriorColor" class="color-form"  placeholder="" id="vehicleInteriorColor">	
										</div>
						 			</div>
						 			<div class="col-md-4 col-sm-4 col-xs-4 c_m">Color</div>
									<div class="col-md-4 col-sm-4 col-xs-4 c_l">
								 		<div class="">
											<input type="text" name="vehicleExteriorColor" class="color-form" placeholder=" "  id="vehicleExteriorColor">	
											<label for="">Exterior Color</label>
										</div>
									</div>
								</div>
							</div>  
			
							<div class="anc-car-speed listng-block b_w">
			 					<div class="">
								 	<ul class="list-unstyled list-inline list-first">
								 	 	<li>Engine</li>
								 		<li>Power</li>
								 		<li>Gearbox</li>
								 		<li>Top Speed</li>
								 		<li>0 - 100Kph</li>
								 		<li>Fuel Econ</li>
										<li>Torque</li>
						 			</ul>
								</div>
							</div>       
				
							<div class="anc-car-speed listng-block b_b listng-block-edit">
								<span class="eyeIcon" style="margin:1px 0px 8px 34px !important"><i class="fa fa-eye"></i></span>
								<input type="checkbox" value="0" name="isShowMisc" class="isShowFields" />
								<div class="">
					 				<ul class="list-unstyled list-inline list-second"> 
					 					<li>
					 						<div class="form-group">
												<?php
													global $wpdb;
													$optionEngine = "";
													if (isset($_POST['auto_series'])) {
														$getEngine = $wpdb->get_results("SELECT DISTINCT `engine` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getEngine) > 0) {
															$optionEngine .= $getEngine[0]->engine;
														} else {
															$optionEngine .= "Engine not avalilable";
														}
													}
												?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehicleEngine" name="vehicleEngine" placeholder="Engine" value="<?php echo ucwords($optionEngine); ?>" onkeyup="loadEngineAutoComplete()" />
											</div>
										</li>
						 				
						 				<li>	
						 					<div class="form-group">
												<?php
													global $wpdb;
													$optionPower = "";
													if (isset($_POST['auto_series'])) {
														$getPower = $wpdb->get_results("SELECT DISTINCT `power` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getPower) > 0) {
															$optionPower .= $getPower[0]->power;
														} else {
															$optionPower .= "Power not avalilable";
														}
													}
												?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehiclePower" name="vehiclePower" placeholder="Power" value="<?php echo ucwords($optionPower); ?>" onkeyup="loadPowerAutoComplete()" />
											</div>
										</li>
						 				<li>
						 					<div class="form-group">
												<?php
													global $wpdb;
													$optionGearBox = "";
													if (isset($_POST['auto_series'])) {
														$getGearBox = $wpdb->get_results("SELECT DISTINCT `gearbox` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getGearBox) > 0) {
															$optionGearBox .= $getGearBox[0]->gearbox;
														} else {
															$optionGearBox .= "Gearbox not avalilable";
														}
													}
												 ?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehicleGearBox" name="vehicleGearBox" placeholder=" Gearbox" value="<?php echo ucwords($optionGearBox); ?>" onkeyup="loadGearBoxAutoComplete()" />
											</div>
										</li>
						 				<li>
						 					<div class="form-group">
												<?php
													global $wpdb;
													$topSpeedOption = "";
													if (isset($_POST['auto_series'])) {
														$getTopSpeed = $wpdb->get_results("SELECT DISTINCT `top_speed` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getTopSpeed) > 0) {
															$topSpeedOption .= $getTopSpeed[0]->top_speed;
														} else {
															$topSpeedOption .= "Top Speed not avalilable";
														}
													}
												?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehicleTopSpeed" name="vehicleTopSpeed" placeholder="Top Speed" value="<?php echo ucwords($topSpeedOption); ?>" onkeyup="loadTopSpeedAutoComplete()" />
											</div>
										</li>
						 				<li>
						 					<div class="form-group">
												<?php
													global $wpdb;
													$speedOptions = "";
													if (isset($_POST['auto_series'])) {
														$getSpeed = $wpdb->get_results("SELECT DISTINCT `speed_kph_sec` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getSpeed) > 0) {
															$speedOptions .= $getSpeed[0]->speed_kph_sec;
														} else {
															$speedOptions .= "Speed not avalilable";
														}
													}
												?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehicle0-100KPH" name="vehicle0-100KPH" placeholder="0-100 KPH" value="<?php echo ucwords($speedOptions); ?>" onkeyup="loadSpeedKPHAutoComplete()" />
											</div>
										</li>
						 				<li>
						 					<div class="form-group">
												<?php
													global $wpdb;
													$optionFuelEconomy = "";
													if (isset($_POST['auto_series'])) {
														$getFuelEconomy = $wpdb->get_results("SELECT DISTINCT `fuel_economy` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getFuelEconomy) > 0) {
															$optionFuelEconomy .= $getFuelEconomy[0]->fuel_economy;
														} else {
															$optionFuelEconomy .= "Fuel Economy not avalilable";
														}
													}
												?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehicleFuelEconomy" name="vehicleFuelEconomy" placeholder="Fuel Economy" value="<?php echo ucwords($optionFuelEconomy); ?>" onkeyup="loadFuelEconomyAutoComplete()" />
											</div>
										</li>
										<li>
											<div class="form-group">
												<?php
													global $wpdb;
													$optionTorque = "";
													if (isset($_POST['auto_series'])) {
														$getTorque = $wpdb->get_results("SELECT DISTINCT `torque` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
														if(sizeof($getTorque) > 0) {
															$optionTorque .= $getTorque[0]->torque;
														} else {
															$optionTorque .= "Torque not avalilable";
														}
													} 
												?>
												<input type="text" class="form-control cust_form lising-details text-center" id="vehicleTorque" name="vehicleTorque" placeholder="Torque" value="<?php echo ucwords($optionTorque); ?>" onkeyup="loadTorqueAutoComplete()" />
											</div>
										</li>
						    		</ul>
								</div> 
			 				</div> 
					<br/>
				<div class="location acrd-wrap b_w">
					<span class="eyeIcon" style="margin:1px 0px 8px 34px !important"><i class="fa fa-eye"></i></span>
					<input type="checkbox" value="0" name="isShowLocation" class="isShowFields" />
					<div class="accordn title heading-font c_m">
						<div class="container"><?php esc_html_e('Location', 'motors'); ?></div>
					</div>
					<div class="accordn-inner">
						<div class="listng-block-edit">
							<div class="container">
								<div class="col-md-9 col-sm-12 col-xs-12">
									<div class="form-group">
										<input type="text" class="form-control cust_form make-vehicle-model" id="stm-add-car-location" name="vehicleLocation" placeholder="Location" value="">
									</div>
								</div>
								<div class="col-md-3 col-sm-12 col-xs-12">
									<div class="form-group">
										<input type="button" name="submit" class="btn btn-primary loactionSubmitBtn" value="Add Location">
									</div>
								</div>
							</div>
						</div>
						<div class="">
							<iframe 
								id="gMapIframe"
								width="100%" 
								height="370" 
								frameborder="0" 
								scrolling="no" 
								marginheight="0" 
								marginwidth="0" 
								src="https://maps.google.com/maps?q=<?php  echo get_post_meta( get_the_ID(), 'stm_lat_car_admin', true );?>,<?php echo get_post_meta( get_the_ID(), 'stm_lng_car_admin', true );?>&hl=es;z=14&amp;output=embed"
							 >
							 </iframe>
						</div>
					</div>
				</div>
				<div class="date-reg acrd-wrap">
					<div class="b_b title heading-font accordn">
						<span class="eyeIcon" style="margin:-4px 0px 8px 34px !important"><i class="fa fa-eye"></i></span>
						<input type="checkbox" value="0" name="isShowFeatures" class="isShowFields" />
						<div class="container">
							<div class="col-md-12 col-sm-12 col-xs-12 c_m">
								Features
							</div>
						</div>
					</div>
					<div class="accordn-inner features-inner" >
						<div class="container">
							
							<?php
								$features =  get_terms( 'vehicle-features', array( 'hide_empty' => false, 'parent' => 0 ));
								foreach ($features as $parentKey => $parentValue){
							?>
								<div class="col-md-3 col-sm-12 col-xs-12 c_m">
										<p><?php echo $parentValue->name; ?></p>
										<?php
											$featureChild =  get_terms( 'vehicle-features', array( 'hide_empty' => false, 'parent' => $parentValue->term_id ));
											foreach ($featureChild as $childKey => $childValue) {
										?>
											<ul>
												<li>
													<div class="feature-single">
					                                    <label>
					                                        <input class="carFeatures" type="checkbox" value="<?php echo $childValue->name."^".$parentValue->name; ?>" name="stm_car_features_labels[]" />
					                                        <span><?php echo $childValue->name; ?></span>
					                                    </label>
		                                			</div>
												</li>
											</ul>
										<?php } ?>
								</div>
							<?php } ?>
							<div class="clearfix"></div>
							<br/>
							<div class="listng-block-edit">
								<div class="container">
									<div class="col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
										<div class="form-group">
											<input type="text" class="form-control cust_form make-vehicle-model" id="" name="vehicleOtherFeatures" placeholder="Others Features" value="">
											<span class="help-block text-danger">Add your vehicles extra features separated by comma.</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			 
				<div class="slr-note acrd-wrap">
					<div class="heding-cr b_b title heading-font accordn">
						<span class="eyeIcon" style="margin:-4px 0px 8px -641px !important"><i class="fa fa-eye" style="color:#fff !important;"></i></span>
						<input type="checkbox" value="0" name="isShowSellerNotes" class="isShowFields" />
						<div class="container">
							Seller's Notes
						</div>
					</div>
					<div class="accordn-inner">	
						<div class="container">
							<textarea name="sellerNotes" id="sellerNotes" placeholder="Add Notes..." cols="30" rows="10"></textarea>
						</div>	
					</div>		
				</div>	

				<div class="slr-note acrd-wrap listng-block-edit">
					<div class="heding-cr b_b title heading-font accordn">
						<span class="eyeIcon" style="margin:-4px 0px 8px -641px !important"><i class="fa fa-eye" style="color:#fff !important;"></i></span>
						<input type="checkbox" value="0" name="isShowOtherDtls" class="isShowFields" />
						<div class="container">
							Other Details
						</div>
					</div>
					
					<div class="accordn-inner">	
						<div class="container">
							<div class="col-md-4 col-sm-4 col-xs-4">
								<div class="form-group">
									<input type="text" class="form-control cust_form make-vehicle-model"  name="vehicleTravelled" placeholder="Car Travelled (KM)">
								</div>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-4 c_m">
								<div class="form-group">
									<input type="text" class="form-control cust_form make-vehicle-model" name="vehicleCustomTitle" placeholder="Custom Title" />
								</div>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-4 c_l">
								<div class="form-group">
									<input type="text" class="form-control cust_form make-vehicle-model" name="vehiclePrice" placeholder="Asking Price">
								</div>

							 </div>
						</div>	
					</div>		
				</div>	
				<div class="contact-det acrd-wrap">
					<div class="heding-cr b_b title heading-font accordn">
						<span class="eyeIcon" style="margin:-4px 0px 8px -641px !important"><i class="fa fa-eye" style="color:#fff !important;"></i></span>
						<input type="checkbox" value="0" name="isShowContactDetails" class="isShowFields" />
						<div class="container">
							Contact Details
						</div>
					</div>
					
					<div class="accordn-inner">	
						<div class="container">
							<?php
								$userId = get_current_user_id(); 
								$user_info = get_userdata($userId);
							?>
							<div class="col-md-4 col-sm-12 col-xs-12 text-left">
								<h5>
									<?php 
										if(empty($user_info->stm_phone) || $user_info->stm_phone == ''){
											echo "Contact no has not been added";
										} else {
											echo $user_info->stm_phone;
										}
									?>
								</h5>
							</div>
							
							<div class="col-md-4 col-sm-12 col-xs-12 text-center">
								<h5><?php echo $user_info->nickname; ?></h5>
							</div>

							<div class="col-md-4 col-sm-12 col-xs-12 text-right">
								<h5><?php echo $user_info->user_email; ?></h5>
							</div>
						</div>
						<div class="container">
							<div class="text-center">

							  <?php $userId = get_current_user_id();?>
								<ul class="social_icn">
									<li>
										<a href="<?php echo get_user_meta($userId,'facebook',true); ?>"><i class="fa fa-facebook"></i></a>
									</li>
									<li>
										<a href="<?php echo get_user_meta($userId,'twitter',true);?>"><i class="fa fa-twitter"></i></a>
									</li>
									<li>
										<a href="<?php echo get_user_meta($userId,'linkdin',true); ?>"><i class="fa fa-linkedin"></i></a>
									</li>
									<li>
										<a href="<?php  echo  get_user_meta($userId,'youtube',true); ?>"><i class="fa fa-youtube-play"></i></a>
									</li>
									<li>
										<a href="<?php  echo  get_user_meta($userId,'instagram',true); ?>"><i class="fa fa-instagram"></i></a>
									</li>
									<li>
										<a href="<?php  echo get_user_meta($userId,'pintrest',true); ?>"><i class="fa fa-pinterest-p"></i></a>
									</li>
									<li>
										<a href="<?php  echo  get_user_meta($userId,'tumbler',true); ?>"><i class="fa fa-tumblr"></i></a>
									</li>
								</ul>
							</div>
						</div>
				 	</div>
				</div>
			</div> 
				<div class="nonShowingFields">
					<?php
						global $wpdb;
						$optionYear = "";
						if(isset($_POST['auto_series'])) {
							$sqlGetLaunchYear =  $wpdb->get_results("SELECT DISTINCT `launch_year` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
							if (sizeof($sqlGetLaunchYear) > 0) {
								$optionYear .= $sqlGetLaunchYear[0]->launch_year;
							} else {
								$optionYear .= "Launch year is not available";
							}
						}
					?>
					<input type="hidden" value="<?php echo $optionYear; ?>" name="VehicleLaunchYear">
					<?php
						global $wpdb;
						$optionCountry = "";
						if (isset($_POST['auto_series'])) {
							$getCountryOrigin = $wpdb->get_results("SELECT DISTINCT `country_origin` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
							if($getCountryOrigin > 0) {
								$optionCountry .= $getCountryOrigin[0]->country_origin;
							} else {
								$optionCountry .= "Select Country of Origin";
							}
						}
					?>
					<input type="hidden" name="VehicleCountryOrigin" value="<?php echo $optionCountry; ?>">	
					<?php
						global $wpdb;
						$optionProdYears = "";
						if(isset($_POST['auto_series'])) {
							$getProdYears = $wpdb->get_results("SELECT DISTINCT `model_production_year` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
							if(sizeof($getProdYears) > 0) {
								$optionProdYears .= $getProdYears[0]->model_production_year;
							} else {
								$optionProdYears .= "Model Production Years not available";
							}
						}
					?>
					<input type="hidden" name="vehicleProdYears" value="<?php echo $optionProdYears; ?>">
					<?php
						global $wpdb;
						$optionModelClass = "";
						if (isset($_POST['auto_series'])) {
							$getModelClass = $wpdb->get_results("SELECT DISTINCT `model_class` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
							if(sizeof($getModelClass) > 0) {
								$optionModelClass .= $getModelClass[0]->model_class;
							} else {
								$optionModelClass .= "Model Class Not Available";
							}
						}
					?>
					<input type="hidden" name="vehicleModelClass" value="<?php echo $optionModelClass; ?>">
					
					<?php
						global $wpdb;
						$optionModelWeight = "";
						if (isset($_POST['auto_series'])) {
							$getModelWeight = $wpdb->get_results("SELECT DISTINCT `model_weight` FROM `vehicle_predifined_data` WHERE `model` = '".$_POST['auto_series']."'");
							if(sizeof($getModelWeight) > 0) {
								$optionModelWeight .= $getModelWeight[0]->model_weight;
							} else {
								$optionModelWeight .= "Model weight Not Available";
							}
						}
					?>
					<input type="hidden" name="vehicleModelWeight" value="<?php echo $optionModelWeight; ?>">
				

				</div> 
					<div class="listng-block b_w mainProgressBar" style="display:none">
						<div class="container">
							<div class="progress">
							  <div class="progress-bar" role="progressbar" aria-valuemax="100">
							    <span class="sr-only"></span>
							  </div>
							</div>
						</div>
					</div>

					<!-- Submit button -->	
					<div class="listng-block b_w">
						<div class="container">
							<input type="hidden" name="vehicleGearType" value="<?php echo (isset($_POST['auto_transmission'])) ? $_POST['auto_transmission'] : '' ?>">
							<input type="hidden" name="vehicleType" value="<?php echo (isset($_POST['vehicleCategoryName'])) ? $_POST['vehicleCategoryName'] : '' ?>">
							<input type="hidden" name="vehicleShape" value="<?php echo (isset($_POST['vechicleSubCatName'])) ? $_POST['vechicleSubCatName'] : '' ?>">
							<input type="hidden" name="vehicleVinNo" value="<?php echo (isset($_POST['auto_registration_number'])) ? $_POST['auto_registration_number'] : '' ?>">
							<input type="hidden" name="vehicleMainFeatures" id="vehicleMainFeatures">
							<input type="submit" name="submit" class="btn btn-primary addCarSubmitBtn" value="Submit">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script> 
jQuery(document).ready(function($) {
	$('.accordn .container').append('<span class="show_hide"><i class="fa fa-minus" aria-hidden="true"></i></span>');
	$('.accordn .show_hide').click(function(){
		$(this).parent().parent().next('.accordn-inner').slideToggle();
		$(this).children().toggleClass('fa-minus');
		$(this).children().toggleClass('fa-plus');
					
	});
});
</script>

<script type="text/javascript">
	var addFormUrl;
	var carFeaturesArr = new Array();
	var flag = true;
	var flagGear = true;
	$(document).ready(function(){

		$('div.ancc li.liGear').click(function(){
			$(this).find('a').toggleClass('gear');
			$(this).find('a').toggleClass('acitveAnccGear');
		});

		$('div.ancc li.liHandShake').click(function(){
			$(this).find('a').toggleClass('handshake');
			$(this).find('a').toggleClass('activeHandShake');
		});

		$('div.ancc li.liCertified').click(function(){
			$(this).find('a').toggleClass('certified');
			$(this).find('a').toggleClass('activeCertified');
		});

		$('div.ancc li.liCondition').click(function(){
			$(this).find('a').toggleClass('condition');
			$(this).find('a').toggleClass('activeCondition');
		});

		$('span.eyeIcon').click(function(){
			if(flag == true){
				$(this).find('.fa').removeClass('fa-eye');
				$(this).find('.fa').addClass('fa-eye-slash');
				$(this).next().prop('checked',true);
				$(this).next().find('.isShowFields').prop('checked',true);
				flag = false;	
			} else {
				$(this).find('.fa').addClass('fa-eye');
				$(this).find('.fa').removeClass('fa-eye-slash');
				$(this).next().find('.isShowFields').prop('checked',false);
				flag = true;	
			}
		});
		
		$('.location .loactionSubmitBtn').click(function(){
			var location = $('.location #stm-add-car-location').val();
			var locationFiltered = location.replace(/\s/g,''); 
			var replaceWithPlus = locationFiltered.replace(/,/g,"+"); 
			var iframeSrc = "https://maps.google.com/maps?q="+replaceWithPlus+"&hl=es;z=14&amp&output=embed";
			$('#gMapIframe').attr('src',iframeSrc);
		});

		$('#vehicleInteriorColor').colorPicker();
		$('#vehicleExteriorColor').colorPicker();
		
		$('.stm_add_car_form #VehicleMake').autocomplete({
    		source: '<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php?db_coloumn=make',
    		minLength:1,
    	});

    	$('.stm_add_car_form #VehicleModel').autocomplete({
    		minLength:1,
    	});

    	$('.carFeatures').change(function(){
    		var featureVal = $(this).val();
    		var parentName = featureVal.split("^");
    		var parent = {};
    		$('.carFeatures:checked').each(function(){    			
    			par = $(this).val().split("^")[1];
    			if(parent[par] === undefined){
    				parent[par]=[];
    			}
    			parent[par].push($(this).val().split("^")[0]);
    			
    		})
    		carFeaturesArr = parent;
    		var jsonArrCarFeatures = JSON.stringify(carFeaturesArr);
    		$('#vehicleMainFeatures').val(jsonArrCarFeatures);
    		   		
    	})
	});

	function loadModelByMakeAjax(){
		var make = $('#VehicleMake').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByMake:make,
				db_coloumn:'model'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;
	}

	function loadModelAutoCompleteMake(){
		var ajax = loadModelByMakeAjax();
		$('.stm_add_car_form #VehicleModel').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadLaunchYearByModelAjax() {
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'launch_year'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadLaunchYearAutoComplete(){
		var ajax = loadLaunchYearByModelAjax();
		$('.stm_add_car_form #VehicleLaunchYear').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadCountryOriginByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'country_origin'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadCountryOriginAutoComplete() {
		var ajax = loadCountryOriginByModelAjax();
		$('.stm_add_car_form #VehicleCountryOrigin').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadModelProdYearsByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'model_production_year'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadModelProdYearsAutoComplete(){
		var ajax = loadModelProdYearsByModelAjax();
		$('.stm_add_car_form #vehicleProdYears').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadModelClassByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'model_class'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadModelClassAutoComplete(){
		var ajax = loadModelClassByModelAjax();
		$('.stm_add_car_form #vehicleModelClass').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadModelBodyStylesByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'model_body_styles'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadModelBodyStylesAutoComplete(){
		var ajax = loadModelBodyStylesByModelAjax();
		$('.stm_add_car_form #vehicleModelBodyStyles').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadModelWeightByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url");?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'model_weight'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadModelWeightAutoComplete(){
		var ajax = loadModelWeightByModelAjax();
		$('.stm_add_car_form #vehicleModelWeight').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadEngineByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'engine'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadEngineAutoComplete(){
		var ajax = loadEngineByModelAjax();
		$('.stm_add_car_form #vehicleEngine').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadGearBoxByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'gearbox'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadGearBoxAutoComplete(){
		var ajax = loadGearBoxByModelAjax();
		$('.stm_add_car_form #vehicleGearBox').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadPowerByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'power'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadPowerAutoComplete(){
		var ajax = loadPowerByModelAjax();
		$('.stm_add_car_form #vehiclePower').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadTorqueByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'torque'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadTorqueAutoComplete(){
		var ajax = loadTorqueByModelAjax();
		$('.stm_add_car_form #vehicleTorque').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadFuelEconomyByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'fuel_economy'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadFuelEconomyAutoComplete(){
		var ajax = loadFuelEconomyByModelAjax();
		$('.stm_add_car_form #vehicleFuelEconomy').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function load0100KPHByModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'speed_kph_sec'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadSpeedKPHAutoComplete(){
		var ajax = load0100KPHByModelAjax();
		$('.stm_add_car_form #vehicle0-100KPH').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadTopSpeedModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'top_speed'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadTopSpeedAutoComplete(){
		var ajax = loadTopSpeedModelAjax();
		$('.stm_add_car_form #vehicleTopSpeed').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

	function loadPriceModelAjax(){
		var model = $('#VehicleModel').val();
		var result = "";
		$.ajax({
			type:'GET',
			url:'<?php echo get_bloginfo("template_url"); ?>/add_car_form_ajax.php',
			async:false,
			dataType:'json',
			data:{
				findByModel:model,
				db_coloumn:'price'
			}
		}).done(function(rows){
			result = rows;
		});
		return result;	
	}

	function loadPriceAutoComplete(){
		var ajax = loadPriceModelAjax();
		$('.stm_add_car_form #vehiclePrice').autocomplete({
    		source: ajax,
    		minLength:1,
    	});
	}

</script>	
<?php  get_footer(); ?>
