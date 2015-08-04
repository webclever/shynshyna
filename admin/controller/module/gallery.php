<?php
class ControllerModuleGallery extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/gallery');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('gallery', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
    $data['text_yes'] = $this->language->get('text_yes');
    $data['text_no'] = $this->language->get('text_no');

		$data['entry_name'] = $this->language->get('entry_name');
    $data['entry_title'] = $this->language->get('entry_title');
		$data['entry_banner'] = $this->language->get('entry_banner');
		$data['entry_resize'] = $this->language->get('entry_resize');
		$data['entry_status'] = $this->language->get('entry_status');
    
    $data['help_resize'] = $this->language->get('help_resize');
    $data['help_auto'] = $this->language->get('help_auto');
    $data['help_title'] = $this->language->get('help_title');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['banner'])) {
			$data['error_banner'] = $this->error['banner'];
		} else {
			$data['error_banner'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/gallery', 'token=' . $this->session->data['token'], 'SSL')
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('module/gallery', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL')
			);			
		}
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('module/gallery', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('module/gallery', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], 'SSL');
		}
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
    
    if (isset($this->request->post['module_description'])) {
			$data['module_description'] = $this->request->post['module_description'];
		} elseif (!empty($module_info)) {
			$data['module_description'] = $module_info['module_description'];
		} else {
			$data['module_description'] = '';
		}
		
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
				
		if (isset($this->request->post['filter_banner_id'])) {
			$data['filter_banner_id'] = $this->request->post['filter_banner_id'];
		} elseif (!empty($module_info)) {
			$data['filter_banner_id'] = $module_info['filter_banner_id'];
		} else {
			$data['filter_banner_id'] = '';
		}
		
		$this->load->model('design/banner');

		$data['banners'] = $this->model_design_banner->getBanners();
		
		if (isset($this->request->post['resize'])) {
			$data['resize'] = $this->request->post['resize'];
		} elseif (!empty($module_info)) {
			$data['resize'] = $module_info['resize'];
		} else {
			$data['resize'] = 1;
		}	
			
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/gallery.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/banner')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
				
		if (!$this->request->post['filter_banner_id']) {
			$this->error['banner'] = $this->language->get('error_banner');
		}
		
		return !$this->error;
	}
}