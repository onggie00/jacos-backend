<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Item profil kandidat — pendidikan/pengalaman/pelatihan/prestasi/referensi/portofolio.
 * item_type harus salah satu nilai CHECK skema (education, experience,
 * training, achievement, reference, portfolio). Semua query scoped ke
 * id_candidate token (anti IDOR).
 *
 * M8: PUT/DELETE membaca id_candidate_item dari PATH (:id), bukan body.
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

		$item = $this->_validate_item($this->post('item_type'), $this->_post_fields());
		if (is_string($item))
		{
return $this->_err($item, 422);
		}
		$pair_err = $this->_check_date_order(
			isset($item['start_date']) ? $item['start_date'] : NULL,
			isset($item['end_date']) ? $item['end_date'] : NULL);
		if ($pair_err !== TRUE)
		{
return $this->_err($pair_err, 422);
		}
		$item['id_candidate'] = $candidate->id_candidate;

		$this->db->insert('career_candidate_items', $item);
		$this->_ok(array('id_candidate_item' => (int) $this->db->insert_id()), 'Item ditambahkan.', 201);
	}

	public function detail_put($id = 0)
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id = (int) $id;
		$row = $this->_own_item($id);
		if ($row === NULL) return;

		$in = $this->_validate_item($row->item_type, $this->_put_fields(), TRUE);
		if (is_string($in))
		{
return $this->_err($in, 422);
		}
		if (empty($in))
		{
return $this->_err('Tidak ada field yang di-update.', 400);
		}
		// M7: end >= start — nilai efektif = yang dikirim ATAU yang ada di DB.
		$pair_err = $this->_check_date_order(
			isset($in['start_date']) ? $in['start_date'] : $row->start_date,
			isset($in['end_date']) ? $in['end_date'] : $row->end_date);
		if ($pair_err !== TRUE)
		{
return $this->_err($pair_err, 422);
		}

		$this->db->where('id_candidate_item', $id)->update('career_candidate_items', $in);
		$this->_ok(NULL, 'Item diperbarui.');
	}

	public function detail_delete($id = 0)
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$id = (int) $id;
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
		}
		return $row;
	}

	protected function _post_fields()
	{
		return array(
			'item_type'     => $this->post('item_type'),
			'title'         => $this->post('title'),
			'institution'   => $this->post('institution'),
			'start_date'    => $this->post('start_date'),
			'end_date'      => $this->post('end_date'),
			'description'   => $this->post('description'),
			'extra_json'    => $this->post('extra_json'),
			'display_order' => $this->post('display_order'),
		);
	}

	protected function _put_fields()
	{
		return array(
			'item_type'     => $this->put('item_type'),
			'title'         => $this->put('title'),
			'institution'   => $this->put('institution'),
			'start_date'    => $this->put('start_date'),
			'end_date'      => $this->put('end_date'),
			'description'   => $this->put('description'),
			'extra_json'    => $this->put('extra_json'),
			'display_order' => $this->put('display_order'),
		);
	}

	/**
	 * M7: SATU fungsi validasi untuk POST dan PUT.
	 * POST ($is_partial = FALSE): semua wajib lengkap + item_type wajib.
	 * PUT ($is_partial = TRUE): hanya field yang dikirim divalidasi.
	 * @return array field hasil validasi, atau string pesan error.
	 */
	protected function _validate_item($current_type, $fields, $is_partial = FALSE)
	{
		$out = array();

		$item_type = $is_partial
			? (isset($fields['item_type']) ? $fields['item_type'] : $current_type)
			: $fields['item_type'];
		if ($is_partial AND isset($fields['item_type']))
		{
			if ( ! is_string($fields['item_type']) OR ! in_array($fields['item_type'], $this->_types, TRUE))
			{
				return 'item_type tidak valid.';
			}
		}
		if ( ! is_string($item_type) OR ! in_array($item_type, $this->_types, TRUE))
		{
			return 'item_type wajib salah satu: ' . implode(', ', $this->_types) . '.';
		}
		if ( ! $is_partial) $out['item_type'] = $item_type;

		// title: wajib POST; kosong/spasi-after-trim ditolak; mb_strlen.
		$title = isset($fields['title']) ? $fields['title'] : NULL;
		if ( ! $is_partial OR isset($fields['title']))
		{
			if ( ! is_string($title)) return 'title wajib diisi (maks 200 karakter).';
			$t = trim($title);
			if (mb_strlen($t) < 1 OR mb_strlen($t) > 200) return 'title wajib diisi (maks 200 karakter).';
			$out['title'] = $t;
		}

		if ( ! $is_partial OR isset($fields['institution']))
		{
			$inst = isset($fields['institution']) ? $fields['institution'] : NULL;
			if ($inst !== NULL AND $inst !== '')
			{
				if ( ! is_string($inst) OR mb_strlen($inst) > 200) return 'institution maksimal 200 karakter.';
				$out['institution'] = trim($inst);
			}
		}

		// tanggal: format + validitas checkdate; end >= start (M7).
		// (pemeriksaan end-versus-start lintas PUT partial dilakukan di detail_put
		// dengan nilai existing row, fungsi ini cukup validasi per field)
		foreach (array('start_date' => 'start_date', 'end_date' => 'end_date') as $key => $df)
		{
			if ( ! $is_partial OR isset($fields[$df]))
			{
				$v = isset($fields[$df]) ? $fields[$df] : NULL;
				if ($v === NULL OR $v === '') { $out[$df] = NULL; continue; }
				$err = $this->_validate_date($df, $v);
				if ($err !== TRUE) return $err;
				$out[$df] = $v;
			}
		}

		if ( ! $is_partial OR isset($fields['description']))
		{
			$desc = isset($fields['description']) ? $fields['description'] : NULL;
			if ($desc !== NULL AND $desc !== '')
			{
				if ( ! is_string($desc) OR mb_strlen($desc) > 5000) return 'description maksimal 5000 karakter.';
				$out['description'] = $desc;
			}
		}

		// extra_json: object/array valid — POST & PUT sama perlakuannya.
		if ( ! $is_partial OR isset($fields['extra_json']))
		{
			$extra = isset($fields['extra_json']) ? $fields['extra_json'] : NULL;
			if ($extra !== NULL AND $extra !== '' AND $extra !== array())
			{
				if (is_array($extra) OR is_object($extra))
				{
					$out['extra_json'] = json_encode($extra);
				}
				elseif (is_string($extra))
				{
					$dec = json_decode($extra);
					if ($dec === NULL AND trim($extra) !== 'null') return 'extra_json harus JSON object/array valid.';
					if ( ! is_array($dec) AND ! is_object($dec)) return 'extra_json harus JSON object/array valid.';
					$out['extra_json'] = $extra;
				}
				else
				{
					return 'extra_json harus JSON object/array valid.';
				}
			}
			if ($extra === '') { $out['extra_json'] = NULL; }
		}

		// display_order: >= 0 (kolom UNSIGNED).
		if ( ! $is_partial OR isset($fields['display_order']))
		{
			$order = isset($fields['display_order']) ? $fields['display_order'] : NULL;
			if ($order !== NULL AND $order !== '' )
			{
				if ( ! is_numeric($order) OR (int) $order !== (float) $order OR (int) $order < 0)
				{
					return 'display_order harus bilangan >= 0.';
				}
				$out['display_order'] = (int) $order;
			}
		}

		return $out;
	}

	/** TRUE bila valid, string pesan bila tidak. */
	protected function _validate_date($field, $v)
	{
		if ( ! is_string($v) OR ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $v))
		{
			return $field . ' format YYYY-MM-DD.';
		}
		$y = (int) substr($v, 0, 4);
		$m = (int) substr($v, 5, 2);
		$d = (int) substr($v, 8, 2);
		if ( ! checkdate($m, $d, $y))
		{
			return $field . ' tanggal tidak valid.';
		}
		return TRUE;
	}

	/** end_date dilarang lebih awal dari start_date (NULL = tidak di-set). */
	protected function _check_date_order($start, $end)
	{
		if ($start !== NULL AND $end !== NULL AND (string) $end !== '' AND (string) $start !== ''
			AND strcmp((string) $end, (string) $start) < 0)
		{
			return 'end_date tidak boleh lebih awal dari start_date.';
		}
		return TRUE;
	}
}
