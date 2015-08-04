<?php
class ControllerModuleMarkup extends Controller {
	private $error = array();

	public function install() {
		 $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "tire_markup (
		   id int(11) NOT NULL AUTO_INCREMENT,
		   value VARCHAR(128) NOT NULL,
		   description TEXT NOT NULL,
		   PRIMARY KEY (id)
		 )");
	}	

	public function index() {
		$this->install();
		$this->load->language('module/markup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/markup');

		$this->getList();
	}

	public function add() {
		$this->load->language('module/markup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/markup');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_markup->addMarkup($this->request->post['markup']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('module/markup', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('module/markup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/markup');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_markup->editMarkup($this->request->get['markup_id'], $this->request->post['markup']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('module/markup', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('module/markup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/markup');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $markup_id) {
				$this->model_catalog_markup->deleteMarkup($markup_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('module/markup', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
        );

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/markup', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['add'] = $this->url->link('module/markup/add', 'token=' . $this->session->data['token'], 'SSL');
		$data['delete'] = $this->url->link('module/markup/delete', 'token=' . $this->session->data['token'], 'SSL');

        $data['total_markup'] = $this->language->get('text_total') . $this->model_catalog_markup->getTotalMarkups();

        $data['informations'] = array();

        $results = $this->model_catalog_markup->getAllMarkups();

		foreach ($results as $result) {
			$data['markups'][] = array(
				'markup_id' => $result['id'],
				'title'          => $result['title'],
				'description'     => $result['description'],
				'edit'           => $this->url->link('module/markup/edit', 'token=' . $this->session->data['token'] . '&markup_id=' . $result['id'], 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_description'] = $this->language->get('column_description');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_insert'] = $this->language->get('button_insert');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/markup_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['markup_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		}

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/markup', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['add'] = $this->url->link('module/markup/add', 'token=' . $this->session->data['token'], 'SSL');
        $data['delete'] = $this->url->link('module/markup/delete', 'token=' . $this->session->data['token'], 'SSL');

		if (!isset($this->request->get['markup_id'])) {
			$data['action'] = $this->url->link('module/markup/add', 'token=' . $this->session->data['token'], 'SSL');
		} else {
			$data['action'] = $this->url->link('module/markup/edit', 'token=' . $this->session->data['token'] . '&markup_id=' . $this->request->get['markup_id'], 'SSL');
		}

		$data['cancel'] = $this->url->link('module/markup', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['markup'])) {
            $data['markup'] = $this->request->post['markup'];
        } elseif (isset($this->request->get['markup_id'])) {
            $data['markup'] = $this->model_catalog_markup->getMarkup($this->request->get['markup_id']);
        }

		$data['token'] = $this->session->data['token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('module/markup_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'module/markup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        if ($this->request->post['markup']) {
            $value = $this->request->post['markup'];
            if (utf8_strlen($value['title']) < 1) {
                $this->error['title'] = $this->language->get('error_title');
            }
			if (utf8_strlen($value['description']) < 1) {
				$this->error['description'] = $this->language->get('error_description');
			}
		}
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'module/markup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}