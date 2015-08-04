<?php
class ModelModuleMarkup extends Model {
	public function addComingsoon($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "comingsoon SET date_added = NOW(), status = '" . (int)$data['status'] . "'");

		$comingsoon_id = $this->db->getLastId();

		foreach ($data['comingsoon'] as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX ."comingsoon_description SET comingsoon_id = '" . (int)$comingsoon_id . "', language_id = '" . (int)$key . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'comingsoon_id=" . (int)$comingsoon_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
	}

	public function editComingsoon($id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "comingsoon SET status = '" . (int)$data['status'] . "' WHERE comingsoon_id = '" . (int)$id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "comingsoon_description WHERE comingsoon_id = '" . (int)$id. "'");

		foreach ($data['comingsoon'] as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX ."comingsoon_description SET comingsoon_id = '" . (int)$id . "', language_id = '" . (int)$key . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'comingsoon_id=" . (int)$id. "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'comingsoon_id=" . (int)$id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
	}

	public function getComingsoon($id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'comingsoon_id=" . (int)$id . "') AS keyword FROM " . DB_PREFIX . "comingsoon WHERE comingsoon_id = '" . (int)$id . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getComingsoonDescription($id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "comingsoon_description WHERE comingsoon_id = '" . (int)$id . "'");

		foreach ($query->rows as $result) {
			$comingsoon_description[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}

		return $comingsoon_description;
	}

	public function getAllComingsoon($data) {
		$sql = "SELECT * FROM " . DB_PREFIX . "comingsoon n LEFT JOIN " . DB_PREFIX . "comingsoon_description nd ON n.comingsoon_id = nd.comingsoon_id WHERE nd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY date_added DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
				if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function deleteComingsoon($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "comingsoon WHERE comingsoon_id = '" . (int)$id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "comingsoon_description WHERE comingsoon_id = '" . (int)$id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'comingsoon_id=" . (int)$id. "'");
	}

	public function countComingsoon() {
		$count = $this->db->query("SELECT * FROM " . DB_PREFIX . "comingsoon");

		return $count->num_rows;
	}
}
?>