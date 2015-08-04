<?php
class ControllerModulemegnorcategory extends Controller {
	public function index($setting) {
		$this->load->language('module/megnorcategory');

		$data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		if (isset($parts[0])) {
			$data['category_id'] = $parts[0];
		} else {
			$data['category_id'] = 0;
		}

		if (isset($parts[1])) {
			$data['child_id'] = $parts[1];
		} else {
			$data['child_id'] = 0;
		}

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			
			if ($category['top']) {
			
			$children_data = array();

			$children = $this->model_catalog_category->getCategories($category['category_id']);

			///////  Megnor extra code for 3 level categories start  ///////
				
				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);					
					// Level 2
					$children_level2 = $this->model_catalog_category->getCategories($child['category_id']);
					$children_data_level2 = array();
					foreach ($children_level2 as $child_level2) {
							$data_level2 = array(
									'filter_category_id'  => $child_level2['category_id'],
									'filter_sub_category' => true
							);
							$product_total_level2 = '';
							if ($this->config->get('config_product_count')) {
									$product_total_level2 = ' (' . $this->model_catalog_product->getTotalProducts($data_level2) . ')';
							}

							$children_data_level2[] = array(
									'name'  =>  $child_level2['name'],
									'href'  => $this->url->link('product/category', 'path=' . $child['category_id'] . '_' . $child_level2['category_id']),
									'id' => $category['category_id']. '_' . $child['category_id']. '_' . $child_level2['category_id']
							);
					}
					$children_data[] = array(
							'name'  => $child['name'],
							'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
							'id' => $category['category_id']. '_' . $child['category_id'],
							'children_level2' => $children_data_level2,
					);
					
				}
				
				///////  Megnor extra code for 3 level categories end  ///////

			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$data['categories'][] = array(
				'category_id' => $category['category_id'],
				'name'        => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
				'children'    => $children_data,
				'column'   => $category['column'] ? $category['column'] : 1,
				'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
			);
			
			}
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/megnorcategory.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/megnorcategory.tpl', $data);
		} else {
			return $this->load->view('default/template/module/megnorcategory.tpl', $data);
		}
	}
}