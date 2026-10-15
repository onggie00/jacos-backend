<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Notifikasi in-app kandidat — list + tandai dibaca (akses pemilik saja).
 */

class Notifikasi extends Career_base {

	public function index_get()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$rows = $this->db->select('id_notification, type, title, body, is_read, created_at')
			->from('career_notifications')
			->where('id_candidate', (int) $this->career_candidate_id)
			->order_by('id_notification', 'DESC')
			->get()->result();

		$data = array();
		foreach ($rows as $r)
		{
			$data[] = array(
				'id_notification' => (int) $r->id_notification,
				'type'            => $r->type,
				'title'           => $r->title,
				'body'            => $r->body,
				'is_read'         => (int) $r->is_read === 1,
				'created_at'      => $r->created_at,
			);
		}
		$this->_ok($data);
	}

	public function read_put($id = 0)
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$n = $this->db->where('id_notification', (int) $id)
			->where('id_candidate', (int) $this->career_candidate_id)
			->get('career_notifications')->row();
		if ($n === NULL)
		{
return $this->_err('Notifikasi tidak ditemukan.', 404);
		}

		$this->db->where('id_notification', (int) $id)
			->update('career_notifications', array('is_read' => 1));
		$this->_ok(NULL, 'Notifikasi ditandai sudah dibaca.', 200);
	}
}
