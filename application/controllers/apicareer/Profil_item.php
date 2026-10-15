<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Item profil kandidat — pendidikan/pengalaman/pelatihan/prestasi/referensi/portofolio.
 * item_type harus salah satu nilai CHECK skema (education, experience,
 * training, achievement, reference, portfolio). Semua query scoped ke
 * id_candidate token (anti IDOR).
 */

class Profil_item extends Career_base {

	protected $_types = array('education', 'experience', 'training', 'achievement', 'reference', 'portfolio');

	public function index_get()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$type = $this->get('type');
		if ($type !== NULL AND ! in_array($type, $this->_types, TRUE))
		{
return $this->_err('type tidak valid.', 422);
		}

		$this->db->select('id_candidate_item, item_type, title, institution, start_date, end_date, description, extra_json, display_order')
			->where('id_candidate', $candidate->id_candidate)
			->order_by('item_type, display_order, id_candidate_item');
		if ($type !== NULL) $this->db->where('item_type', $type);
		$rows = $this->db->get('career_candidate_items')->result_array();

		$this->_ok($rows, 'OK');
	}

	public function index_post()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$item = $this->_build_item_input();
		if ( ! is_array($item))
		{
return $this->_err($item, 422);
		}
		$item['id_candidate'] = $candidate->id_candidate;

		$this->db->insert('career_candidate_items', $item);
		$this->_ok(array('id_candidate_item' => (int) $this->db->insert_id()), 'Item ditambahkan.', 201);
	}

	public function detail_put()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id = (int) $this->put('id_candidate_item');
		$row = $this->_own_item($id);
		if ($row === NULL) return;

		$in = array();
		foreach ($this->_input_fields() as $field => $dummy)
		{
			$val = $this->put($field);
			if ($val === NULL) continue;
			$in[$field] = $val;
		}
		if (isset($in['item_type']) AND ! in_array($in['item_type'], $this->_types, TRUE))
		{
return $this->_err('item_type tidak valid.', 422);
		}
		if (isset($in['title']) AND ( ! is_string($in['title']) OR strlen($in['title']) < 1 OR strlen($in['title']) > 200))
		{
return $this->_err('title wajib 1-200 karakter.', 422);
		}
		if (isset($in['institution']) AND strlen((string) $in['institution']) > 200)
		{
return $this->_err('institution maksimal 200 karakter.', 422);
		}
		if (empty($in))
		{
return $this->_err('Tidak ada field yang di-update.', 400);
		}

		$this->db->where('id_candidate_item', $id)->update('career_candidate_items', $in);
		$this->_ok(NULL, 'Item diperbarui.');
	}

	public function detail_delete()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id = (int) $this->delete('id_candidate_item');
		$row = $this->_own_item($id);
		if ($row === NULL) return;

		$this->db->delete('career_candidate_items', array('id_candidate_item' => $id));
		$this->_ok(NULL, 'Item dihapus.');
	}

	// ------------------------------------------------------------------
	protected function _own_item($id)
	{
		$row = $this->db->where('id_candidate_item', (int) $id)
			->where('id_candidate', (int) $this->career_candidate_id)
			->get('career_candidate_items')->row();
		if ($row === NULL)
		{
return $this->_err('Item tidak ditemukan.', 404);
			return NULL;
		}
		return $row;
	}

	protected function _input_fields()
	{
		return array(
			'item_type' => TRUE, 'title' => TRUE, 'institution' => TRUE,
			'start_date' => TRUE, 'end_date' => TRUE, 'description' => TRUE,
			'extra_json' => TRUE, 'display_order' => TRUE,
		);
	}

	/** Baik bila data POST tidak valid (string pesan), atau array data insert. */
	protected function _build_item_input()
	{
		$type = $this->post('item_type');
		$title = $this->post('title');

		if ( ! in_array($type, $this->_types, TRUE))
		{
			return 'item_type wajib salah satu: ' . implode(', ', $this->_types) . '.';
		}
		if ( ! is_string($title) OR strlen($title) < 1)
		{
			return 'title wajib diisi (maks 200 karakter).';
		}
		if (strlen($title) > 200)
		{
			return 'title wajib diisi (maks 200 karakter).';
		}

		$data = array(
			'item_type'   => $type,
			'title'       => $title,
		);

		$inst = $this->post('institution');
		if ($inst !== NULL AND $inst !== '')
		{
			if ( ! is_string($inst) OR strlen($inst) > 200) return 'institution maksimal 200 karakter.';
			$data['institution'] = $inst;
		}
		foreach (array('start_date', 'end_date') as $df)
		{
			$v = $this->post($df);
			if ($v !== NULL AND $v !== '')
			{
				if ( ! is_string($v) OR ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $df . ' format YYYY-MM-DD.';
				$data[$df] = $v;
			}
		}
		$desc = $this->post('description');
		if ($desc !== NULL AND $desc !== '')
		{
			if ( ! is_string($desc) OR strlen($desc) > 5000) return 'description maksimal 5000 karakter.';
			$data['description'] = $desc;
		}
		$extra = $this->post('extra_json');
		if ($extra !== NULL AND $extra !== '')
		{
			if (is_array($extra) OR is_object($extra))
			{
				$data['extra_json'] = json_encode($extra);
			}
		}
		$order = $this->post('display_order');
		if ($order !== NULL AND is_numeric($order))
		{
			$data['display_order'] = (int) $order;
		}
		return $data;
	}
}
