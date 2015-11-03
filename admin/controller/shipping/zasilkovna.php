<?php
class ControllerShippingZasilkovna extends Controller {
	public 	$countries 		= array('cz'=>'Česká republika', 'hu'=>'Maďarsko', 'pl'=>'Polsko', 'sk'=>'Slovenská republika', ''=>'vše');
	public  $_servicesCnt 	= 6;	
	public 	$inputFields 	= array('price'=>'price','js'=>'js','title'=>'title','destination'=>'destination','freeover'=>'freeover');
	private $error 			= array(); 

	public function index() {
		$this->load->language('shipping/zasilkovna');

		//$this->document->title = $this->language->get('heading_title');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('zasilkovna', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
	
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shipping/zasilkovna', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('shipping/zasilkovna', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');
		

		for($i=0;$i<$this->_servicesCnt;$i++){
			foreach ($this->inputFields as $input_field => $value) {
				$input_field_name = "zasilkovna_".$input_field."_".$i;
				if (isset($this->request->post[$input_field_name])) {
					$data[$input_field_name] = $this->request->post[$input_field_name];
				} else {
					$data[$input_field_name] = $this->config->get($input_field_name);
				}
				
				if($input_field = 'price' || $input_field = 'freeover'){
					$data[$input_field_name] = str_replace(',', '.', $data[$input_field_name]);
				}
				
				$input_field_name = "zasilkovna_enabled_".$i;
				if (isset($this->request->post[$input_field_name])) {
					$data[$input_field_name] = $this->request->post[$input_field_name];
				} else {
					$data[$input_field_name] = $this->config->get($input_field_name);
				}
				
				$input_field_name = "zasilkovna_branches_enabled_".$i;
				if (isset($this->request->post[$input_field_name])) {
					$data[$input_field_name] = $this->request->post[$input_field_name];
				} else {
					$data[$input_field_name] = $this->config->get($input_field_name);
				}
			}
		
		}

		if (isset($this->request->post['zasilkovna_api_key'])) {
			$data['zasilkovna_api_key'] = $this->request->post['zasilkovna_api_key'];
		} else {
			$data['zasilkovna_api_key'] = $this->config->get('zasilkovna_api_key');
		}

    	//save additional info
		if (isset($this->request->post['zasilkovna_tax_class_id'])) {
			$data['zasilkovna_tax_class_id'] = $this->request->post['zasilkovna_tax_class_id'];
		} else {
			$data['zasilkovna_tax_class_id'] = $this->config->get('zasilkovna_tax_class_id');
		}
		if (isset($this->request->post['zasilkovna_geo_zone_id'])) {
			$data['zasilkovna_geo_zone_id'] = $this->request->post['zasilkovna_geo_zone_id'];
		} else {
			$data['zasilkovna_geo_zone_id'] = $this->config->get('zasilkovna_geo_zone_id');
		}	
		if (isset($this->request->post['zasilkovna_weight_max'])) {
			$data['zasilkovna_weight_max'] = $this->request->post['zasilkovna_weight_max'];
		} else {
			$data['zasilkovna_weight_max'] = $this->config->get('zasilkovna_weight_max');
		}	
		if (isset($this->request->post['zasilkovna_status'])) {
			$data['zasilkovna_status'] = $this->request->post['zasilkovna_status'];
		} else {
			$data['zasilkovna_status'] = $this->config->get('zasilkovna_status');
		}
		if (isset($this->request->post['zasilkovna_sort_order'])) {
			$data['zasilkovna_sort_order'] = $this->request->post['zasilkovna_sort_order'];
		} else {
			$data['zasilkovna_sort_order'] = $this->config->get('zasilkovna_sort_order');
		}

		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		$this->template = 'shipping/zasilkovna.tpl';
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		//$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		$this->response->setOutput($this->load->view($this->template, $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/zasilkovna')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
?>
