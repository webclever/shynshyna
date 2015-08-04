<?php
class ControllerModulePriceImport extends Controller {
	private $error = array(); 
	
	private function getSheetFromFile($name) {
		$inputFileType = PHPExcel_IOFactory::identify('../price/'.$name);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load('../price/'.$name);
		return $objPHPExcel->getActiveSheet()->toArray();
	}

	public function parseAvailPrice($name) {
        $models = array();
        $products = array();
        $errors = array();

        $rows = $this->getSheetFromFile($name);
        if (count($rows[0]) == 9) {
            unset($rows[0]);
            unset($rows[1]);
            $counter = 3;

            $this->load->model('catalog/markup');
            $params = $this->model_catalog_markup->getAllMarkups();
            $markup = array();
            foreach ($params as $param) {
                $markup[$param['title']] = $param['id'];
            }
            foreach ($rows as $row) {
                $brand = $row[1];
                $model = $row[2];
                $size = $row[3];
                $index = $row[4];
                $price = $row[5];
                $count = $row[8];
                if (isset($brand) && isset($model) && isset($size) && isset($index) && isset($price) && isset($count)) {
                    $error = '';
                    $product['brand'] = trim($brand);
                    $arr_model = explode(' ', trim($model));
                    $model = $arr_model[0] . ' ';
                    unset($arr_model[0]);
                    $product_markup = array();
                    foreach ($arr_model as $piece) {
                        $f = true;
                        if (isset($markup[$piece]) and $f) {
                            $product_markup[] = $markup[$piece];
                            $f = false;
                        } else {
                            $model .= $piece . ' ';
                        }
                    }

                    $product['model'] = trim($model);
                    if (!isset($models[$product['model']])) {
                        $models[$product['model']] = 1;
                    }
                    $product['markup'] = $product_markup;

                    $arr_size = explode('/', trim($size));
                    if ((count($arr_size) == 3) && !empty($arr_size[0]) && !empty($arr_size[1]) && !empty($arr_size[2])) {
                        $product['width'] = $arr_size[0];
                        $product['profile'] = $arr_size[1];
                        $product['radius'] = 'R' . $arr_size[2];
                    } else {
                        $error = 'Помилка в рядку ' . $counter . '. Розміри "' . $size . '" не відповідають шаблону. ';
                    }
                    $arr_index = explode(' ', trim($index));
                    if (count($arr_index) == 2) {
                        $product['index_n'] = $arr_index[0];
                        $product['index_v'] = $arr_index[1];
                    } else {
                        if ($error != '') {
                            $error .= 'Індекси "' . $index . '" не відповідають шаблону. ';
                        } else {
                            $error = 'Помилка в рядку ' . $counter . '. Індекси "' . $index . '" не відповідають шаблону. ';
                        }
                    }
                    $product['price'] = trim($price);
                    $product['quantity'] = trim($count);
                    if ($error == "") {
                        $products[] = $product;
                    } else {
                        $errors[] = $error;
                    }
                }
                $counter++;
            }
            $data['products'] = $products;
            $data['models'] = $models;
            if (count($errors) > 0) {
                $data['errors'] = $errors;
            }
        } else {
            $error = 'Неправильний формат прайсу';
            $data['errors'] = $error;
        }
		return $data;
	}

    public function parseContractPrice($name) {
        $models = array();
        $products = array();
        $errors = array();

        $rows = $this->getSheetFromFile($name);
        if (count($rows[0]) == 4) {
            unset($rows[0]);
            $counter = 2;

            $this->load->model('catalog/markup');
            $params = $this->model_catalog_markup->getAllMarkups();
            $markup = array();
            foreach ($params as $param) {
                $markup[$param['title']] = $param['id'];
            }

            foreach ($rows as $row) {
                $brand = $row[0];
                $model = $row[1];
                $count = $row[2];
                $price = $row[3];
                if (isset($brand) && isset($model) && isset($count) && isset($price)) {
                    $error = false;
                    $product['brand'] = trim($brand);
                    $arr_model = explode(' ', trim($model));


                    $arr_size = explode('/', trim($arr_model[0]));
                    if ((count($arr_size) == 2) && !empty($arr_size[0]) && !empty($arr_size[1])) {
                        $product['width'] = $arr_size[0];
                        $product['profile'] = $arr_size[1];
                    } else {
                        $error = true;
                        $text_error = 'Помилка в рядку ' . $counter . '. Розміри "' . $arr_model[0] . '" не відповідають шаблону.';
                        $errors[] = $text_error;
                    }
                    $product['radius'] = $arr_model[1];

                    $model_counter = 2;
                    $model = '';
                    $pos = strpos($arr_model[$model_counter], "[");
                    while (isset($arr_model[$model_counter + 1]) && $pos === false) {
                        $model .= $arr_model[$model_counter] . ' ';
                        $model_counter++;
                        $pos = strpos($arr_model[$model_counter], "[");
                    }
                    $product['model'] = trim($model);

                    if (isset($arr_model[$model_counter])) {
                        $product['index_n'] = substr(trim($arr_model[$model_counter]), 1, strlen($arr_model[$model_counter]) - 2);
                    } else {
                        $error = true;
                        $errors[] = "Помилка індексу у рядку " . $counter;
                    }
                    $model_counter++;
                    if (isset($arr_model[$model_counter])) {
                        $product['index_v'] = $arr_model[$model_counter];
                    } else {
                        $error = true;
                        $errors[] = "Помилка індексу у рядку " . $counter;
                    }

                    $model_counter++;
                    $product_markup = array();
                    $count_params = count($arr_model);
                    for ($i = $model_counter; $i < $count_params && isset($arr_model[$i]); $i++) {
                        if (isset($markup[$arr_model[$i]])) {
                            $product_markup[] = $markup[$arr_model[$i]];
                        } else {
                            $error = true;
                            $errors[] = 'Невідомий параметр "' . $arr_model[$i] . '" у рядку ' . $counter;
                        }
                    }
                    if (!isset($models[$product['model']])) {
                        $models[$product['model']] = 1;
                    }
                    $product['markup'] = $product_markup;
                    $product['price'] = trim($price);
                    $product['quantity'] = trim($count);
                    if (!$error) {
                        $products[] = $product;
                    }
                }
                $counter++;
            }
            $data['products'] = $products;
            $data['models'] = $models;
            if (count($errors) > 0) {
                $data['errors'] = $errors;
            }
        } else {
            $errors[] = 'Неправильний формат прайсу';
            $data['errors'] = $errors;
        }
        return $data;
    }

    public function ajaxLoadPrice() {
        $PhpExcelPath = DIR_SYSTEM . 'PHPExcel/Classes/PHPExcel/';
        require_once($PhpExcelPath . 'IOFactory.php');

        if (isset($this->request->files['price_import_file']['tmp_name'])) {

            $tmp_name = $this->request->files['price_import_file']['tmp_name'];
            $name = $this->request->files['price_import_file']['name'];

            if(is_uploaded_file($tmp_name)) {
                $name = iconv('utf-8', 'windows-1251', $name);
                move_uploaded_file($tmp_name, '../price/' . $name);

                $price_type = 0;
                if (isset($this->request->post['price-type'])) {
                    $price_type = $this->request->post['price-type'];
                }
                $tire_type = 0;
                if (isset($this->request->post['truck-tire'])) {
                    $tire_type = $this->request->post['truck-tire'];
                }
                $price_season = 0;
                if(isset($this->request->post['price-season'])) {
                    $price_season = $this->request->post['price-season'];
                }
                $price_avails = 0;
                if(isset($this->request->post['price-avails'])) {
                    $price_avails = $this->request->post['price-avails'];
                }
                $stime = microtime(1);

                if ($price_type) {
                    $results = $this->parseAvailPrice($name);
                } else {
                    $results = $this->parseContractPrice($name);
                }
                if (isset($results['errors'])) {
                    $result = array(
                        'type'   => $price_type,
                        'season' => $price_season,
                        'avails' => $price_avails,
                        'error'  => $results['errors']);
                } else {
                $products = $results['products'];

                $this->load->model('catalog/filter');
                $this->load->model('catalog/category');
                $this->load->model('catalog/product');
                $this->load->model('catalog/markup');

                $category_id = $this->getCategory($price_type, $tire_type);
                $cf = $this->model_catalog_category->getCategoryFilters($category_id);
                $category_filters = array();
                foreach ($cf as $filter) {
                    $category_filters[$filter] = $filter;
                }

                //load brands
                $this->load->model('catalog/manufacturer');
                $manufacturers = $this->model_catalog_manufacturer->getManufacturers();
                $brands = array();
                foreach ($manufacturers as $manufacturer) {
                    $brands[$manufacturer['name']] = $manufacturer['manufacturer_id'];
                }
                //load filters
                $product_filter_groups = $this->model_catalog_filter->getFilterGroups(array());
                $filter_groups = array();
                $product_filters = array();
                foreach ($product_filter_groups as $filter) {
                    $filter_groups[$filter['name']] = $filter['filter_group_id'];
                    $filters = $this->model_catalog_filter->getFilterDescriptions($filter['filter_group_id']);
                    foreach ($filters as $f) {
                        if (count($f) > 0) {
                            $product_filters[$filter['name']][$f['filter_description'][3]['name']] = $f['filter_id'];
                        }
                    }
                }

                if ($tire_type) {
                    $tire_type_id = $product_filters['Тип']['Грузовая'];
                } else {
                    $tire_type_id = $product_filters['Тип']['Легковая'];
                }
                $product_season = 0;
                switch ($price_season) {
                    case 1 :
                        $product_season = $product_filters['Сезон']['Зима'];
                        break;
                    case 2 :
                        $product_season = $product_filters['Сезон']['Лето'];
                        break;
                    case 3 :
                        $product_season = $product_filters['Сезон']['Всесезонки'];
                        break;
                }
                if ($price_type) {
                    $product_avail_type = $product_filters['Наличие']['В наличии'];
                } else {
                    $product_avail_type = $product_filters['Наличие']['Под заказ'];
                }
                $products_added = 0;
                $product_modified = 0;
                foreach ($products as $product) {
                    $product_filter = array();
                    $product_filter[] = $tire_type_id;
                    $product_filter[] = $product_season;
                    $product_filter[] = $product_avail_type;

                    if (isset($brands[$product['brand']])) {
                        $brand_id = $brands[$product['brand']];
                    } else {
                        $brand_id = $this->addBrand($product['brand']);
                        $brands[$product['brand']] = $brand_id;
                    }

                    if (isset($product_filters['Ширина'][$product['width']])) {
                        $product_filter[] = $product_filters['Ширина'][$product['width']];
                    } else {
                        //echo var_dump($product['radius']) . "<br>";
                        $filter_id = $this->addFilter($filter_groups['Ширина'], $product['width']);
                        $product_filters['Ширина'][$product['width']] = $filter_id;
                        $product_filter[] = $filter_id;
                    }

                    if (isset($product_filters['Профиль'][$product['profile']])) {
                        $product_filter[] = $product_filters['Профиль'][$product['profile']];
                    } else {
                        //echo var_dump($product['radius']) . "<br>";
                        $filter_id = $this->addFilter($filter_groups['Профиль'], $product['profile']);
                        $product_filters['Профиль'][$product['profile']] = $filter_id;
                        $product_filter[] = $filter_id;
                    }

                    if (isset($product_filters['Радиус'][$product['radius']])) {
                        $product_filter[] = $product_filters['Радиус'][$product['radius']];
                    } else {
                        //echo var_dump($product['radius']) . "<br>";
                        $filter_id = $this->addFilter($filter_groups['Радиус'], $product['radius']);
                        $product_filters['Радиус'][$product['radius']] = $filter_id;
                        $product_filter[] = $filter_id;
                    }

                    if (isset($product_filters['Индекс нагрузки'][$product['index_n']])) {
                        $product_filter[] = $product_filters['Индекс нагрузки'][$product['index_n']];
                    } else {
                        //echo var_dump($product['radius']) . "<br>";
                        $filter_id = $this->addFilter($filter_groups['Индекс нагрузки'], $product['index_n']);
                        $product_filters['Индекс нагрузки'][$product['index_n']] = $filter_id;
                        $product_filter[] = $filter_id;
                    }

                    if (isset($product_filters['Индекс скорости'][$product['index_v']])) {
                        $product_filter[] = $product_filters['Индекс скорости'][$product['index_v']];
                    } else {
                        //echo var_dump($product['radius']) . "<br>";
                        $filter_id = $this->addFilter($filter_groups['Индекс скорости'], $product['index_v']);
                        $product_filters['Индекс скорости'][$product['index_v']] = $filter_id;
                        $product_filter[] = $filter_id;
                    }

                    $name = $product['brand'] . " " . $product['model'] . " (" . $product['width'] . "/" . $product['profile'] . "/" . $product['radius'] . ")";
                    $data_name['filter_name'] = $name;
                    $pr = $this->model_catalog_product->getProducts($data_name);

                    if (isset($pr[0])) {
                        $product_modified++;
                        $old_product = $pr[0];
                        $product_id = $old_product['product_id'];

                        if ($price_avails) {
                            $quantity = $product['quantity'];
                        } else {
                            $quantity = $old_product['quantity'] + $product['quantity'];
                        }
                        $old_descriptions = $this->model_catalog_product->getProductDescriptions($product_id);
                        $old_descriptions[2]['name'] = $name;
                        $old_descriptions[2]['meta_title'] = $name;
                        $old_descriptions[3]['name'] = $name;
                        $old_descriptions[3]['meta_title'] = $name;
                        $old_images = $this->model_catalog_product->getProductImages($product_id);
                        $old_filter = $this->model_catalog_product->getProductFilters($product_id);
                        foreach ($product_filter as $filter) {
                            $old_filter[] = $filter;
                        }
                        $product_filter = array_unique($old_filter);

                        $productData = array(
                            'model' => $product['model'],
                            'sku' => '',
                            'upc' => '',
                            'ean' => '',
                            'jan' => '',
                            'isbn' => '',
                            'mpn' => '',
                            'location' => '',
                            'quantity' => $quantity,
                            'stock_status_id' => 7,
                            'image' =>  'no_image.png',    //'data/shynshyna/' . $product['model'] . '.jpg',
                            'manufacturer_id' => $brand_id,
                            'shipping' => 0,
                            'price' => $product['price'],
                            'points' => 0,
                            'tax_class_id' => 0,
                            'date_available' => date('Y-m-d'),
                            'weight' => 0,
                            'weight_class_id' => 1,
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'length_class_id' => 1,
                            'subtract' => 1,
                            'minimum' => 1,
                            'sort_order' => 0,
                            'status' => 1,
                            'keyword' => $product['brand'] . "_" . $product['model'] . "_" . $product['width'] . "/" . $product['profile'] . "/" . $product['radius'],
                            'product_store' => array(0),
                            'product_description' => $old_descriptions,
                            'product_image' => $old_images,
                            'product_category' => array(
                                0 => $category_id,
                            ),
                            'product_filter' => $product_filter,
                            'product_markup' => $product['markup']
                        );
                        $this->model_catalog_product->editProduct($product_id, $productData);
                        if (count($product['markup']) > 0) {
                            $this->model_catalog_markup->editProductMarkup($product_id, $product['markup']);
                        }

                    } else {
                        $products_added++;
                        $productData = array(
                            'model' => $product['model'],
                            'sku' => '',
                            'upc' => '',
                            'ean' => '',
                            'jan' => '',
                            'isbn' => '',
                            'mpn' => '',
                            'location' => '',
                            'quantity' => $product['quantity'],
                            'stock_status_id' => 7,
                            'image' =>  'no_image.png',    //'data/shynshyna/' . $product['model'] . '.jpg',
                            'manufacturer_id' => $brand_id,
                            'shipping' => 0,
                            'price' => $product['price'],
                            'points' => 0,
                            'tax_class_id' => 0,
                            'date_available' => date('Y-m-d'),
                            'weight' => 0,
                            'weight_class_id' => 1,
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'length_class_id' => 1,
                            'subtract' => 1,
                            'minimum' => 1,
                            'sort_order' => 0,
                            'status' => 1,
                            'keyword' => $product['brand'] . "_" . $product['model'] . "_" . $product['width'] . "/" . $product['profile'] . "/" . $product['radius'],
                            'product_store' => array(0),
                            'product_description' => array(
                                2 => array(
                                    'description' => '',
                                    'name' => $name,
                                    'meta_title' => $name,
                                    'meta_keyword' => '',
                                    'meta_description' => '',
                                    'tag' => ''),
                                3 => array(
                                    'description' => '',
                                    'name' => $name,
                                    'meta_title' => $name,
                                    'meta_keyword' => '',
                                    'meta_description' => '',
                                    'tag' => ''),
                            ),
                            'product_image' => array(
                                0 => array(
                                    'image' => 'data/shynshyna/' . $product['model'] . '_2.jpg',
                                    'sort_order' => 0,
                                ),
                                1 => array(
                                    'image' => 'data/shynshyna/' . $product['model'] . '_3.jpg',
                                    'sort_order' => 0,
                                ),
                                2 => array(
                                    'image' => 'data/shynshyna/' . $product['model'] . '_4.jpg',
                                    'sort_order' => 0,
                                ),
                                3 => array(
                                    'image' => 'data/shynshyna/' . $product['model'] . '_5.jpg',
                                    'sort_order' => 0,
                                ),
                            ),
                            'product_category' => array(
                                0 => $category_id,
                            ),
                            'product_filter' => $product_filter,
                            'product_markup' => $product['markup']
                        );
                        $product_id =  $this->model_catalog_product->addProduct($productData);
                        $this->model_catalog_markup->addProductMarkup($product_id, $product['markup']);
                    }
                    foreach ($product_filter as $filter) {
                        if (!isset($category_filters[$filter])) {
                            $category_filters[$filter] = $filter;
                        }
                    }
                }
                $this->model_catalog_category->editCategoryFilters($category_id, $category_filters);
                $etime = microtime(1);

                $result = array(
                    'error'     => 'Success. Number products added: ' . $products_added . ', modified: ' . $product_modified . '. Time is: ' . ($etime - $stime));
                }
            } else {
                $result = array('error' => 'File not uploaded!');
            }
        } else {
            $result = array('error' => 'File error!');
        }

        $this->response->setOutput(json_encode($result));
    }

    public function getCategory($price_type, $tire_type) {
        if ($price_type && !$tire_type) {
            $category_id = 59;
        } elseif ($price_type && $tire_type) {
            $category_id = 60;
        } else {
            $category_id = 61;
        }
        return $category_id;
    }

    public function addBrand($brand) {
        $data = array(
            'name' => $brand,
            'sort_order' => 0,
            'image' => 'data/brands/' . $brand . '.jpg',
            'manufacturer_store' => array(0),
            'keyword' => strtolower($brand)
        );
        return $this->model_catalog_manufacturer->addManufacturer($data);
    }

    public function addFilter($filter_group_id, $filter) {
        $data = array(
            'sort_order' => 0,
            'filter_description' => array(
                2 => array(
                    'name' => $filter
                ),
                3 => array(
                    'name' => $filter
                ),
            )
        );
        return $this->model_catalog_filter->addFilterValue($filter_group_id, $data);
    }

//    public function restructureDescription($desc) {
//        if ($desc != '') {
//            $mas = explode("\n", $desc);
//            $desc = '<p>' . implode('</p><p>', $mas) . '</p>';
//        }
//        return $desc;
//    }

	public function index() {
        $this->document->addStyle(HTTP_SERVER . '/view/javascript/bootstrap/css/bootstrap-select.min.css');
        $this->document->addScript(HTTP_SERVER . '/view/javascript/bootstrap/js/bootstrap-filestyle.min.js');
        $this->document->addScript(HTTP_SERVER . '/view/javascript/bootstrap/js/bootstrap-select.min.js');

        $this->load->language('module/price_import');
		$this->document->setTitle($this->language->get('heading_title'));
		header('Content-Type: text/html; charset=utf-8');
		$data['alert_available'] = '';
		$data['alert_contract'] = '';
		$data['alert_image'] = '';
		$data['alert_image_products_0'] = '';
		$data['alert_image_products_1'] = '';
        $data['token'] = $this->session->data['token'];
		$this->load->model('setting/setting');
		$this->load->model('catalog/filter');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$PhpExcelPath = DIR_SYSTEM . 'PHPExcel/Classes/PHPExcel/';
		require_once($PhpExcelPath . 'IOFactory.php');


		if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if(isset($this->request->get['load_price'])) {
                if(is_uploaded_file($this->request->files['price_import_file']['tmp_name'])) {
					$tmp_name = $this->request->files['price_import_file']['tmp_name'];
					$name = $this->request->files['price_import_file']['name'];
                    $name = iconv('utf-8', 'windows-1251', $name);
					move_uploaded_file($tmp_name, '../price/'.$name);
                    $price_type = 0;
                    if (isset($this->request->post['price-type'])) {
                        $price_type = $this->request->post['price-type'];
                    }
                    $tire_type = 0;
                    if (isset($this->request->post['truck-tire'])) {
                        $tire_type = $this->request->post['truck-tire'];
                    }
                    $price_season = 0;
                    if(isset($this->request->post['price-season'])) {
                        $price_season = $this->request->post['price-season'];
                    }
                    $price_avails = 0;
					if(isset($this->request->post['price-avails'])) {
                        $price_avails = $this->request->post['price-avails'];
					}

                    $stime = microtime(1);
                    if ($price_type) {
                        $results = $this->parseAvailPrice($name);
                    } else {
                        $results = $this->parseContractPrice($name);
                    }
                    $errors = array();
                    if (isset($results['errors'])) {
                        echo var_dump($results['errors']);
                    } else {
                        $products = $results['products'];

                        $category_id = $this->getCategory($price_type, $tire_type);
                        $cf = $this->model_catalog_category->getCategoryFilters($category_id);
                        $category_filters = array();
                        foreach ($cf as $filter) {
                            $category_filters[$filter] = $filter;
                        }


                        //load brands
                        $this->load->model('catalog/manufacturer');
                        $manufacturers = $this->model_catalog_manufacturer->getManufacturers();
                        $brands = array();
                        foreach ($manufacturers as $manufacturer) {
                            $brands[$manufacturer['name']] = $manufacturer['manufacturer_id'];
                        }
                        //load filters
                        $product_filter_groups = $this->model_catalog_filter->getFilterGroups(array());
                        $filter_groups = array();
                        $product_filters = array();
                        foreach ($product_filter_groups as $filter) {
                            $filter_groups[$filter['name']] = $filter['filter_group_id'];
                            $filters = $this->model_catalog_filter->getFilterDescriptions($filter['filter_group_id']);
                            foreach ($filters as $f) {
                                if (count($f) > 0) {
                                    $product_filters[$filter['name']][$f['filter_description'][3]['name']] = $f['filter_id'];
                                }
                            }
                        }

                        if ($tire_type) {
                            $tire_type_id = $product_filters['Тип']['Грузовая'];
                        } else {
                            $tire_type_id = $product_filters['Тип']['Легковая'];
                        }
                        $product_season = 0;
                        switch ($price_season) {
                            case 1 :
                                $product_season = $product_filters['Сезон']['Зима'];
                                break;
                            case 2 :
                                $product_season = $product_filters['Сезон']['Лето'];
                                break;
                            case 3 :
                                $product_season = $product_filters['Сезон']['Всесезонки'];
                                break;
                        }
                        if ($price_type) {
                            $product_avail_type = $product_filters['Наличие']['В наличии'];
                        } else {
                            $product_avail_type = $product_filters['Наличие']['Под заказ'];
                        }

                        //echo var_dump($product_filters['Радиус']) . "<br>";
                        foreach ($products as $product) {
                            $product_filter = array();

                            $product_filter[] = $tire_type_id;
                            $product_filter[] = $product_season;
                            $product_filter[] = $product_avail_type;

                            if (isset($brands[$product['brand']])) {
                                $brand_id = $brands[$product['brand']];
                            } else {
                                $brand_id = $this->addBrand($product['brand']);
                                $brands[$product['brand']] = $brand_id;
                            }

                            if (isset($product_filters['Ширина'][$product['width']])) {
                                $product_filter[] = $product_filters['Ширина'][$product['width']];
                            } else {
                                //echo var_dump($product['radius']) . "<br>";
                                $filter_id = $this->addFilter($filter_groups['Ширина'], $product['width']);
                                $product_filters['Ширина'][$product['width']] = $filter_id;
                                $product_filter[] = $filter_id;
                            }

                            if (isset($product_filters['Профиль'][$product['profile']])) {
                                $product_filter[] = $product_filters['Профиль'][$product['profile']];
                            } else {
                                //echo var_dump($product['radius']) . "<br>";
                                $filter_id = $this->addFilter($filter_groups['Профиль'], $product['profile']);
                                $product_filters['Профиль'][$product['profile']] = $filter_id;
                                $product_filter[] = $filter_id;
                            }

                            if (isset($product_filters['Радиус'][$product['radius']])) {
                                $product_filter[] = $product_filters['Радиус'][$product['radius']];
                            } else {
                                //echo var_dump($product['radius']) . "<br>";
                                $filter_id = $this->addFilter($filter_groups['Радиус'], $product['radius']);
                                $product_filters['Радиус'][$product['radius']] = $filter_id;
                                $product_filter[] = $filter_id;
                            }

                            if (isset($product_filters['Индекс нагрузки'][$product['index_n']])) {
                                $product_filter[] = $product_filters['Индекс нагрузки'][$product['index_n']];
                            } else {
                                //echo var_dump($product['radius']) . "<br>";
                                $filter_id = $this->addFilter($filter_groups['Индекс нагрузки'], $product['index_n']);
                                $product_filters['Индекс нагрузки'][$product['index_n']] = $filter_id;
                                $product_filter[] = $filter_id;
                            }

                            if (isset($product_filters['Индекс скорости'][$product['index_v']])) {
                                $product_filter[] = $product_filters['Индекс скорости'][$product['index_v']];
                            } else {
                                //echo var_dump($product['radius']) . "<br>";
                                $filter_id = $this->addFilter($filter_groups['Индекс скорости'], $product['index_v']);
                                $product_filters['Индекс скорости'][$product['index_v']] = $filter_id;
                                $product_filter[] = $filter_id;
                            }

                            $name = $product['brand'] . " " . $product['model'] . " (" . $product['width'] . "/" . $product['profile'] . "/" . $product['radius'] . ")";
                            $data_name['filter_name'] = $name;
                            $pr = $this->model_catalog_product->getProducts($data_name);

                            if (isset($pr[0])) {
                                $old_product = $pr[0];
                                $product_id = $old_product['product_id'];

                                if ($price_avails) {
                                    $quantity = $product['quantity'];
                                } else {
                                    $quantity = $old_product['quantity'] + $product['quantity'];
                                }
                                $old_descriptions = $this->model_catalog_product->getProductDescriptions($product_id);
                                $old_descriptions[2]['name'] = $name;
                                $old_descriptions[2]['meta_title'] = $name;
                                $old_descriptions[3]['name'] = $name;
                                $old_descriptions[3]['meta_title'] = $name;
                                $old_images = $this->model_catalog_product->getProductImages($product_id);
                                $old_filter = $this->model_catalog_product->getProductFilters($product_id);
                                foreach ($product_filter as $filter) {
                                    $old_filter[] = $filter;
                                }
                                $product_filter = array_unique($old_filter);

                                $productData = array(
                                    'model' => $product['model'],
                                    'sku' => '',
                                    'upc' => '',
                                    'ean' => '',
                                    'jan' => '',
                                    'isbn' => '',
                                    'mpn' => '',
                                    'location' => '',
                                    'quantity' => $quantity,
                                    'stock_status_id' => 7,
                                    'image' =>  'no_image.png',    //'data/shynshyna/' . $product['model'] . '.jpg',
                                    'manufacturer_id' => $brand_id,
                                    'shipping' => 0,
                                    'price' => $product['price'],
                                    'points' => 0,
                                    'tax_class_id' => 0,
                                    'date_available' => date('Y-m-d'),
                                    'weight' => 0,
                                    'weight_class_id' => 1,
                                    'length' => 0,
                                    'width' => 0,
                                    'height' => 0,
                                    'length_class_id' => 1,
                                    'subtract' => 1,
                                    'minimum' => 1,
                                    'sort_order' => 0,
                                    'status' => 1,
                                    'keyword' => $product['brand'] . "_" . $product['model'] . "_" . $product['width'] . "/" . $product['profile'] . "/" . $product['radius'],
                                    'product_store' => array(0),
                                    'product_description' => $old_descriptions,
                                    'product_image' => $old_images,
                                    'product_category' => array(
                                        0 => $category_id,
                                    ),
                                    'product_filter' => $product_filter,
                                );
                                $this->model_catalog_product->editProduct($product_id, $productData);
                            } else {
                                $productData = array(
                                    'model' => $product['model'],
                                    'sku' => '',
                                    'upc' => '',
                                    'ean' => '',
                                    'jan' => '',
                                    'isbn' => '',
                                    'mpn' => '',
                                    'location' => '',
                                    'quantity' => $product['quantity'],
                                    'stock_status_id' => 7,
                                    'image' =>  'no_image.png',    //'data/shynshyna/' . $product['model'] . '.jpg',
                                    'manufacturer_id' => $brand_id,
                                    'shipping' => 0,
                                    'price' => $product['price'],
                                    'points' => 0,
                                    'tax_class_id' => 0,
                                    'date_available' => date('Y-m-d'),
                                    'weight' => 0,
                                    'weight_class_id' => 1,
                                    'length' => 0,
                                    'width' => 0,
                                    'height' => 0,
                                    'length_class_id' => 1,
                                    'subtract' => 1,
                                    'minimum' => 1,
                                    'sort_order' => 0,
                                    'status' => 1,
                                    'keyword' => $product['brand'] . "_" . $product['model'] . "_" . $product['width'] . "/" . $product['profile'] . "/" . $product['radius'],
                                    'product_store' => array(0),
                                    'product_description' => array(
                                        2 => array(
                                            'description' => '',
                                            'name' => $name,
                                            'meta_title' => $name,
                                            'meta_keyword' => '',
                                            'meta_description' => '',
                                            'tag' => ''),
                                        3 => array(
                                            'description' => '',
                                            'name' => $name,
                                            'meta_title' => $name,
                                            'meta_keyword' => '',
                                            'meta_description' => '',
                                            'tag' => ''),
                                    ),
                                    'product_image' => array(
                                            0 => array(
                                                'image' => 'data/shynshyna/' . $product['model'] . '_2.jpg',
                                                'sort_order' => 0,
                                            ),
                                            1 => array(
                                                'image' => 'data/shynshyna/' . $product['model'] . '_3.jpg',
                                                'sort_order' => 0,
                                            ),
                                            2 => array(
                                                'image' => 'data/shynshyna/' . $product['model'] . '_4.jpg',
                                                'sort_order' => 0,
                                            ),
                                            3 => array(
                                                'image' => 'data/shynshyna/' . $product['model'] . '_5.jpg',
                                                'sort_order' => 0,
                                            ),
                                        ),
                                    'product_category' => array(
                                        0 => $category_id,
                                    ),
                                    'product_filter' => $product_filter,
                                );
							    $product_id =  $this->model_catalog_product->addProduct($productData);
                            }
                            foreach ($product_filter as $filter) {
                                if (!isset($category_filters[$filter])) {
                                    $category_filters[$filter] = $filter;
                                }
                            }
                        }
                        $this->model_catalog_category->editCategoryFilters($category_id, $category_filters);
                        $etime = microtime(1);
                        echo 'Time:' . ($etime - $stime);
                    }

                    exit();


//					$rowCount = $sheet->getHighestRow();
//					for ($i = 1; $i <= $rowCount; $i++) {
//						$flag = true;
//
//						$filtr = array();
//
//						// проверка категории
//
//						$flag_category = false;
//						$cat_data['filter_name'] = $catg;
//						$arr_cat = $this->model_catalog_category->getCategories($cat_data);
//						if (isset($arr_cat[0])) {
//							$flag_category = true;
//							$id_categor = $arr_cat[0]['category_id'];
//						}
//
//
//						$flag_add = true;
//						$data_model['filter_model'] = $artk;
//						$pr = $this->model_catalog_product->getProducts($data_model);
//						if (isset($pr[0])) {
//							$flag_add = false;
//							$id_edit = $pr[0]['product_id'];
//							$k_sty += $pr[0]['quantity'];
//						}
//
//						if ($discount) {
//							$data['filter_name'] = "Розпродаж";
//							$data['language_id'] = 4;
//							$filters_desc = $this->model_catalog_filter->getFilters($data);
//
//							$filtr[] = $filters_desc[0]['filter_id'];
//						}
//
//						if($flag_add){
//							$productData = array(
//								'model' => $artk,
//								'sku' => $staty,
//								'upc' => '',
//								'ean' => '',
//								'jan' => '',
//								'isbn' => '',
//								'mpn' => '',
//								'location' => '',
//								'quantity' => $k_sty,
//								'stock_status_id' => 7,
//								'image' => 'data/wojcik/'.$artk.'.jpg',
//								'manufacturer_id' => 0,
//								'shipping' => 0,
//								'price' => $price,
//								'points' => 0,
//								'tax_class_id' => 0,
//								'date_available' => date('Y-m-d'),
//								'weight' => 0,
//								'weight_class_id' => 1,
//								'length' => 0,
//								'width' => 0,
//								'height' => 0,
//								'length_class_id' => 1,
//								'subtract' => 0,
//								'minimum' => 1,
//								'sort_order' => 0,
//								'status' => 1,
//								'keyword' => '',
//								'product_store' => array(0),
//								'product_description' => array(
//									3 => array(
//										'description' => $description_rus,
//										'name' => $name_rus,
//										'meta_keyword' => '',
//										'meta_description' => '',
//									 	'tag' => ''),
//									4 => array(
//										'description' => $description_ukr,
//										'name' => $name,
//										'meta_keyword' => '',
//										'meta_description' => '',
//									 	'tag' => ''),
//								),
//								'product_option' => array(
//						            0 => array(
//					                    'product_option_id' => '',
//					                    'option_id' => '14',
//					                    'type' => 'select',
//					                    'required' => 1,
//					                    'product_option_value' => $product_option,
//									),
//						        ),
//						        'product_image' => array(
//	        						0 => array(
//	                					'image' => 'data/wojcik/'.$artk.'_2.jpg',
//	                    				'sort_order' => 0,
//	                    			),
//	                    			1 => array(
//	                					'image' => 'data/wojcik/'.$artk.'_3.jpg',
//	                    				'sort_order' => 0,
//	                    			),
//	                    			2 => array(
//	                					'image' => 'data/wojcik/'.$artk.'_4.jpg',
//	                    				'sort_order' => 0,
//	                    			),
//	                    			3 => array(
//	                					'image' => 'data/wojcik/'.$artk.'_5.jpg',
//	                    				'sort_order' => 0,
//	                    			),
//	                    		),
//								'product_category' => array(
//								    0 => $id_categor,
//								),
//								'product_filter' => array_unique($filtr),
//							);
//							$this->model_catalog_product->addProduct($productData);
//						}else{
//							$option_edit = $this->model_catalog_product->getProductOptions($id_edit);
//
//							if(count($option_edit)>0){
//								foreach($option_edit[0]['product_option_value'] as $option_edit_key =>  $option_edit_value){
//									foreach($product_option as $product_option_key => $product_option_value){
//										if((int)$option_edit_value['option_value_id']==(int)$product_option_value['option_value_id']){
//											$option_edit[0]['product_option_value'][$option_edit_key]['quantity'] += (int)$product_option_value['quantity'];
//											unset($product_option[$product_option_key]);
//										}
//									}
//								}
//							}else{
//								$option_edit[0]['product_option_id'] = '';
//								$option_edit[0]['option_id'] = '14';
//								$option_edit[0]['type'] = 'select';
//								$option_edit[0]['required'] = 1;
//							}
//							foreach($product_option as $product_option_value){
//								$option_edit[0]['product_option_value'][] = $product_option_value;
//							}
//
//
//
//							$old_filter = $this->model_catalog_product->getProductFilters($id_edit);
//
//							if (!$discount) {
//								$data['filter_name'] = "Розпродаж";
//								$data['language_id'] = 4;
//								$filters_desc = $this->model_catalog_filter->getFilters($data);
//
//								$disc_id = $filters_desc[0]['filter_id'];
//								foreach ($old_filter as $key => $value) {
//									if ($value == $disc_id) {
//										unset($old_filter[$key]);
//										break;
//									}
//								}
//							}
//
//							$new_filter = array_merge($old_filter, $filtr);
//							$old_descriptions = $this->model_catalog_product->getProductDescriptions($id_edit);
//							$productData = array(
//								'model' => $artk,
//								'sku' => $staty,
//								'upc' => '',
//								'ean' => '',
//								'jan' => '',
//								'isbn' => '',
//								'mpn' => '',
//								'location' => '',
//								'quantity' => $k_sty,
//								'stock_status_id' => 7,
//								'image' => 'data/wojcik/'.$artk.'.jpg',
//								'manufacturer_id' => 0,
//								'shipping' => 0,
//								'price' => $price,
//								'points' => 0,
//								'tax_class_id' => 0,
//								'date_available' => date('Y-m-d'),
//								'weight' => 0,
//								'weight_class_id' => 1,
//								'length' => 0,
//								'width' => 0,
//								'height' => 0,
//								'length_class_id' => 1,
//								'subtract' => 0,
//								'minimum' => 1,
//								'sort_order' => 0,
//								'status' => 1,
//								'keyword' => '',
//								'product_store' => array(0),
//								'product_description' => array(
//									3 => array(
//										'description' => ($description_rus != '') ? $description_rus : $old_descriptions[3]['description'],
//										'name' => $name_rus,
//										'meta_keyword' => $old_descriptions[3]['meta_keyword'],
//										'meta_description' => $old_descriptions[3]['meta_description'],
//									 	'tag' => $old_descriptions[3]['tag']),
//									4 => array(
//										'description' => ($description_ukr != '') ? $description_ukr : $old_descriptions[4]['description'],
//										'name' => $name,
//										'meta_keyword' => $old_descriptions[4]['meta_keyword'],
//										'meta_description' => $old_descriptions[4]['meta_description'],
//									 	'tag' => $old_descriptions[4]['tag']),
//								),
//								'product_option' => $option_edit,
//						        'product_image' => $this->model_catalog_product->getProductImages($id_edit),
//								'product_category' => array(
//								    0 => $id_categor,
//								),
//								'product_filter' => array_unique($new_filter),
//							);
//							$this->model_catalog_product->editProduct($id_edit,$productData);
//						}
//					}
					$etime = microtime(1);
                    if (!isset($data['alert_available'])) {
                        $data['alert_available'] = 'Загрузка пройшла успешно!<br>Время исполнения: '. ($etime - $stime) .' секунд';
                    } else {
                        $data['alert_available'] .= '<br>Время исполнения: '. ($etime - $stime) .' секунд';;
                    }
				} else {
					$data['alert_available'] = 'Выберите файл!';
				}
			} elseif(isset($this->request->get['reload_image'])) {
				$this->load->model('catalog/product');
				$x = $this->model_catalog_product->getProducts();
				$k = 0;
				$kk = 0;
				foreach ($x as $y) {
					$z = $this->model_catalog_product->getProductImages($y['product_id']);
					$image = array();
					foreach ($z as $w) {
						if(($w['image']!='no_image.jpg')&&(file_exists(DIR_IMAGE.$w['image']))){
							$image[] = array('image' => $w['image'], 'sort_order' => $w['sort_order']);
						}else{$kk++;}
					}
					$this->model_catalog_product->editProductImage($y['product_id'],$image);
						if(!file_exists(DIR_IMAGE.$y['image'])){
							$data['alert_image_products_0'] .= $y['model'].' '.$y['name'].'<br>';
							$this->model_catalog_product->editProductStatus($y['product_id'],0);
						}
						if(file_exists(DIR_IMAGE.$y['image'])&&$y['status']==0){
							$data['alert_image_products_1'] .= $y['model'].' '.$y['name'].'<br>';
							$this->model_catalog_product->editProductStatus($y['product_id'],1);
						}
					$k++;
				}
				$data['alert_image'] = 'Зделано! (обработано '.$k.' товаров, произведено '.$kk.')';
			}else{
				$this->model_setting_setting->editSetting('price_import', $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
				$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');

        $data['tab_general'] = $this->language->get('tab_general');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_content_top'] = $this->language->get('text_content_top');
		$data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$data['text_column_left'] = $this->language->get('text_column_left');
		$data['text_column_right'] = $this->language->get('text_column_right');

		$data['entry_banner'] = $this->language->get('entry_banner');
		$data['entry_dimension'] = $this->language->get('entry_dimension'); 
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_position'] = $this->language->get('entry_position');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_module'] = $this->language->get('button_add_module');
		$data['button_remove'] = $this->language->get('button_remove');
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['dimension'])) {
			$data['error_dimension'] = $this->error['dimension'];
		} else {
			$data['error_dimension'] = array();
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/price_import', 'token=' . $this->session->data['token'], 'SSL'),
   		);
		
		$data['action'] = $this->url->link('module/price_import', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');
				
		$this->response->setOutput($this->load->view('module/price_import.tpl', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/price_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
						
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>