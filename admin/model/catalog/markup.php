<?php
class ModelCatalogMarkup extends Model {
    public function addMarkup($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "tire_markup(title, description)  VALUES('" . $this->db->escape($data['title']) . "', '" . $this->db->escape($data['description']) . "')");

        $markup_id = $this->db->getLastId();

        return $markup_id;
    }

    public function editMarkup($id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "tire_markup SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "' WHERE id=" . (int)$id);

        $markup_id = $this->db->getLastId();

        return $markup_id;
    }

    public function getMarkup($markup_id) {
        $sql = "SELECT id, title, description FROM " . DB_PREFIX . "tire_markup WHERE id=" . (int)$markup_id;
        $query = $this->db->query($sql);
        return $query->row;
    }

	public function getAllMarkups() {
		$sql = "SELECT id, title, description FROM " . DB_PREFIX . "tire_markup";
		$query = $this->db->query($sql);
		return $query->rows;
	}

    public function getTotalMarkups() {
        $sql = "SELECT COUNT(*) as 'Total' FROM " . DB_PREFIX . "tire_markup";
        $query = $this->db->query($sql);
        return $query->row['Total'];
    }

    public function deleteMarkup($markup_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "tire_markup WHERE id = '" . (int)$markup_id . "'");
    }

    public function getProductMarkup($product_id) {
        $sql = "SELECT id, title FROM " . DB_PREFIX . "product_markup WHERE product_id = " . (int)$product_id;
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function addProductMarkup($product_id, $data) {
        foreach ($data as $markup) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_markup SET product_id = " . (int)$product_id . ', markup_id = ' . $markup);
        }
    }

    public function editProductMarkup($product_id, $data) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "product_markup WHERE product_id = " . (int)$product_id);
        foreach ($data as $markup) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_markup SET product_id = " . (int)$product_id . ', markup_id = ' . (int)$markup);
        }
    }
}