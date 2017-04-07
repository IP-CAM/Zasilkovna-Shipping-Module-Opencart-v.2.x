<?php

class ModelShippingZasilkovna extends Model {
	function getQuote($address) {
		$this->load->language('shipping/zasilkovna');
		$weight = $this->cart->getWeight();
		$max_weight = $this->config->get('zasilkovna_weight_max');
		$valid_weight = (!$max_weight && $max_weight !== 0) || ($max_weight > 0 && $weight <= $max_weight); // weight condition check, yay logic


		if($this->config->get('zasilkovna_status') && $valid_weight) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('zasilkovna_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

			if(!$this->config->get('zasilkovna_geo_zone_id')) {
				$status = TRUE;
			}
			elseif($query->num_rows) {
				$status = TRUE;
			}
			else {
				$status = FALSE;
			}
		}
		else {
			$status = FALSE;
		}
		$method_data = array();

		if($status) {
			$weight = $this->cart->getWeight();
			$quote_data = array();

			$text = $this->language->get('text_description') . ' : ';
			$api_key = $this->config->get('zasilkovna_api_key');

			$HELPER_JS = '<script> (function(d){ var el, id = "packetery-jsapi", head = d.getElementsByTagName("head")[0]; if(d.getElementById(id)){ return; } el = d.createElement("script"); el.id = id; el.async = true; el.src = "//www.zasilkovna.cz/api/' . $api_key . '/branch.js?callback=addHooks"; head.insertBefore(el, head.firstChild); }(document)); </script>
<script language="javascript" type="text/javascript">   ;
if(typeof window.packetery != "undefined"){
  setTimeout(function(){initBoxes()},1000)
}else{
  setTimeout(function(){setRequiredOpt();},500)
}
function initBoxes(){
   var api = window.packetery;
   divs = $(\'#zasilkovna_box\');
   $(\'.packetery-branch-list\').each(function() {
       api.initialize(api.jQuery(this));
       this.packetery.option("selected-id",0);
    });
   addHooks();  
   setRequiredOpt();
}
function overrideAQCValidate(){
  var oldValidateAllFields = validateAllFields;
  console.log(\'Packetery override: "validateAllFields" overridden!\');
  validateAllFields = function(param){
    console.log(\'Packetery override: packetery not selected.\');
    if(!$(select_branch_message).is(\':visible\')){
      oldValidateAllFields(param);
    }
    else{
      alert(\'Vyberte pobo\u010Dku z\u00E1silkovny!\')
    }
  }
}

var SubmitButtonDisabled = true;
function setRequiredOpt(){
	var setOnce = false;
	var disableButton=false;
	var zasilkovna_selected = false;
	var opts={
		connectField: \'textarea#confirm_comment\'
	}        
	$("div.packetery-branch-list").each(
		function(){
			var div = $(this).closest(\'.radio\');
			var radioButt = $(div).find(\'input[name="shipping_method"]:radio\');
			var select_branch_message = $(div).find(\'#select_branch_message\');

			if($(radioButt).is(\':checked\')){
				zasilkovna_selected = true;
			}else{//deselect branch (so when user click the radio again, he must select a branch). Made coz couldnt update connect-field if only clicked on radio with already selected branch
				if(this.packetery.option("selected-id")>0){
					this.packetery.option("selected-id",0);
				}
			}

			if($(radioButt).is(\':checked\')&&!this.packetery.option("selected-id")){
				select_branch_message.show();
				disableButton=true;
			}else{
				select_branch_message.hide();
			}
		}
	);

	$(\'#button-shipping-method\').attr(\'disabled\', disableButton);
		SubmitButtonDisabled = disableButton;
		if(!zasilkovna_selected){
			updateConnectedField(opts,0);
		}
     overrideAQCValidate();
}

function submitForm(){
	if(!SubmitButtonDisabled){
		
		$(\'#shipping\').submit();
	}
}

function updateConnectedField(opts, id){
	var branches;
	if(typeof(opts) == "undefined"){
		$(".packetery-branch-list").each(function(){
			if(this.packetery.option("selected-id")){
				opts = {
					connectField: "textarea#confirm_comment",
					selectedId: this.packetery.option("selected-id")
				};
				branches = this.packetery.option("branches");
			}
		});
	}

	if (opts.connectField){
		if (typeof(id) == "undefined"){
			id = opts.selectedId
		}
		var f = $(opts.connectField);
		var v = f.val() || "",
		re = /\[Z\u00e1silkovna\s*;\s*[0-9]+\s*;\s*[^\]]*\]/,
		newV;
		if (id > 0){
			var branch = branches[id];
			newV = "[Z\u00e1silkovna; " + branch.id + "; " + branch.name + "]"
		} else {
			newV = ""
		}
		if (v.search(re) != -1){
			v = v.replace(re, newV)
		} else {
			if (v){
				v += "\n" + newV
			} else {
				v = newV
			}
		}
		
		function trim(s){
			return s.replace(/^\s*|\s*$/, "")
		}
		f.val(trim(v))
	}
}

function addHooks(){ //called when no zasilkovna method is selected. Dunno how to call this from the branch.js

//set each radio button to call setRequiredOpt if clicked
 $(\'input[name="shipping_method"]:radio\').each(
        function(){
          $(this).click(setRequiredOpt);         
         }
      );      
      button = $(\'[onclick="$(\\\'#shipping\\\').submit();"]\');
      button.removeAttr("onclick");
      button.click(submitForm);


      $("div.packetery-branch-list").each(            
          function() {             
            var fn = function(){       
              var selected_id = this.packetery.option("selected-id");             
              var tr = $(this).closest(\'div.radio-input\');              
              var radioButt = $(tr).find(\'input[name="shipping_method"]:radio\');                   
              if(selected_id)$(radioButt).attr("checked",\'checked\');
              setTimeout(setRequiredOpt, 1);
              setTimeout(function(){ $("#confirm_comment").change(); }, 1500);
            };
            this.packetery.on("branch-change", fn);
            fn.call(this);
		}
	);
	
	$("#content").delegate("textarea#confirm_comment", "change", function (){ 
		updateConnectedField();
	});

}
</script>';

			$addedHelperJS = false;
			for($i = 0; $i <= 10; $i++) {
				$enabled = $this->config->get('zasilkovna_enabled_' . $i);
				$config_destination = $this->config->get('zasilkovna_destination_' . $i);
				$cart_destination = strtolower($this->cart->session->data["shipping_address"]["iso_code_2"]);

				if(empty($enabled) || $enabled == 0 || ($config_destination && $cart_destination && $config_destination != $cart_destination)) continue;

				$cost = 0;
				if($this->config->get('zasilkovna_freeover_' . $i) == 0 || $this->cart->getTotal() < $this->config->get('zasilkovna_freeover_' . $i)) // shipment is not free
					$cost = $this->config->get('zasilkovna_price_' . $i);

				$title = $this->config->get('zasilkovna_title_' . $i);
				$country = $this->config->get('zasilkovna_destination_' . $i);

				$JS = "";
				if($addedHelperJS == false) {
					$JS .= $HELPER_JS;
					$addedHelperJS = true;
				}
				if($this->config->get('zasilkovna_branches_enabled_' . $i)) {
					$JS .= '<script>
						var radio = $(\'input:radio[name="shipping_method"][value="zasilkovna.' . $title . $i . '"]\');
						var parent_div = radio.parent().parent(); 
						if(parent_div.find(\'#zasilkovna_box\').length == 0){
							$(parent_div).append(\'<div id="zasilkovna_box" class="packetery-branch-list list-type=3 connect-field=textarea#confirm_comment country=' . $country . '" style="border: 1px dotted black;">Načítání: seznam poboček osobního odběru</div> \');
							$(parent_div).append(\'<p id="select_branch_message" style="color:red; font-weight:bold; display:none">Vyberte pobočku</p>\');
						}
					</script>';
				}
				$quote_data[$title . $i] = array(
					'id' => 'zasilkovna.' . $title . $i,
					'code' => 'zasilkovna.' . $title . $i,
					'title' => $title,
					'cost' => $cost,
					'tax_class_id' => $this->config->get('zasilkovna_tax_class_id'),
					'text' => $JS . $this->currency->format($this->tax->calculate($cost, $this->config->get('zasilkovna_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']),
				);
			}


			$method_data = array(
				'code' => 'zasilkovna',
				'title' => 'Zásilkovna',
				'quote' => $quote_data,
				'sort_order' => $this->config->get('zasilkovna_sort_order'),
				'error' => FALSE
			);
		}

		return $method_data;
	}
}
