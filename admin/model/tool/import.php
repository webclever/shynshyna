<?php
class ModelToolImport extends Model {
	public function getTiresParams() {
		$sql = "SELECT id, value FROM " . DB_PREFIX . "tire_markup";
		$query = $this->db->query($sql);
		return $query->rows;
	}
}