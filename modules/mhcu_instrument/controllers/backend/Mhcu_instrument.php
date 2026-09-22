<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mhcu_instrument extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_mhcu_instrument');
	}

	public function index($offset = 0)
	{
		$this->is_allowed('mhcu_instrument_list');
		$filter = $this->input->get('q'); $field = $this->input->get('f');
		$sort = $this->input->get('s'); $sort_type = $this->input->get('d');
		$multi_filters = $this->_parse_filters();
		$has_multi_filter = !empty($multi_filters);

		if ($has_multi_filter) {
			$this->data['mhcu_instruments'] = $this->model_mhcu_instrument->get(null, null, $this->limit_page, $offset, array(), $sort, $sort_type, $multi_filters);
			$this->data['mhcu_instrument_counts'] = $this->model_mhcu_instrument->count_all(null, null, $multi_filters);
		} else {
			$this->data['mhcu_instruments'] = $this->model_mhcu_instrument->get($filter, $field, $this->limit_page, $offset, array(), $sort, $sort_type);
			$this->data['mhcu_instrument_counts'] = $this->model_mhcu_instrument->count_all($filter, $field);
		}

		$this->data['pagination'] = $this->pagination(array(
			'base_url' => 'administrator/mhcu_instrument/index/',
			'total_rows' => $has_multi_filter ? $this->model_mhcu_instrument->count_all(null, null, $multi_filters) : $this->model_mhcu_instrument->count_all($filter, $field),
			'per_page' => $this->limit_page, 'uri_segment' => 4,
		));
		$this->data['multi_filters'] = $multi_filters;
		$this->template->title('Instrument MHCU');
		$this->render('backend/standart/administrator/mhcu_instrument/mhcu_instrument_list', $this->data);
	}

	private function _parse_filters()
	{
		$fields = $this->input->get('ff'); $operators = $this->input->get('fo'); $values = $this->input->get('fv');
		if (!is_array($fields) || !is_array($operators) || !is_array($values)) return array();
		$filters = array();
		for ($i = 0; $i < count($fields); $i++) {
			if (!empty($fields[$i]) && !empty($values[$i])) {
				$filters[] = array('field' => $fields[$i], 'operator' => isset($operators[$i]) ? $operators[$i] : 'contains', 'value' => $values[$i]);
			}
		}
		return $filters;
	}

	public function add_save()
	{
		if (!$this->is_allowed('mhcu_instrument_add', false)) {
			echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
			exit;
		}

		$this->form_validation->set_rules('nama_instrument', 'Nama Instrument', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('kode_instrument', 'Kode Instrument', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|integer');

		if ($this->form_validation->run()) {
			$save_data = array(
				'nama_instrument' => $this->input->post('nama_instrument'),
				'kode_instrument' => $this->input->post('kode_instrument'),
				'no_urut' => $this->input->post('no_urut'),
			);
		
			$save = $this->model_mhcu_instrument->store($save_data);

			if ($save) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $save;
					$this->data['message'] = cclang('success_save_data_stay', array(
						anchor('administrator/mhcu_instrument/edit/' . $save, 'Edit'),
						anchor('administrator/mhcu_instrument', ' Go back to list')
					));
				} else {
					set_message(cclang('success_save_data_redirect', array(
						anchor('administrator/mhcu_instrument/edit/' . $save, 'Edit')
					)), 'success');
            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mhcu_instrument');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	public function edit($id)
	{
		$this->is_allowed('mhcu_instrument_update');

		$this->data['mhcu_instrument'] = $this->model_mhcu_instrument->find($id);
		$this->data['instrument_items'] = $this->model_mhcu_instrument->get_items($id);
		$this->data['all_skalas'] = $this->model_mhcu_instrument->get_all_skalas();

		$this->template->title('Edit Instrument');
		$this->render('backend/standart/administrator/mhcu_instrument/mhcu_instrument_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('mhcu_instrument_update', false)) {
			echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
			exit;
		}
		
		$this->form_validation->set_rules('nama_instrument', 'Nama Instrument', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('kode_instrument', 'Kode Instrument', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('no_urut', 'No Urut', 'trim|required|integer');
		
		if ($this->form_validation->run()) {
			$save_data = array(
				'nama_instrument' => $this->input->post('nama_instrument'),
				'kode_instrument' => $this->input->post('kode_instrument'),
				'no_urut' => $this->input->post('no_urut'),
			);
		
			$save = $this->model_mhcu_instrument->change($id, $save_data);

			if ($save) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $id;
					$this->data['message'] = cclang('success_update_data_stay', array(
						anchor('administrator/mhcu_instrument', ' Go back to list')
					));
				} else {
					set_message(cclang('success_update_data_redirect', array()), 'success');
            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/mhcu_instrument');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	// ---- Nested: Instrument Item ----

	public function add_item($id_instrument)
	{
		$this->is_allowed('mhcu_instrument_update');

		$this->form_validation->set_rules('text_item', 'Teks Item', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('no_urut_item', 'No Urut', 'trim|required|integer');
		$this->form_validation->set_rules('id_skala', 'Skala', 'trim|required|integer');
		$this->form_validation->set_rules('dimensi_aspek', 'Dimensi/Aspek', 'trim|max_length[200]');
		$this->form_validation->set_rules('input_type', 'Tipe Input', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('is_reversed', 'Reversed', 'trim|integer');

		if ($this->form_validation->run()) {
			$save_data = array(
				'id_instrument' => $id_instrument,
				'text_item' => $this->input->post('text_item'),
				'no_urut_item' => $this->input->post('no_urut_item'),
				'id_skala' => $this->input->post('id_skala'),
				'dimensi_aspek' => $this->input->post('dimensi_aspek'),
				'input_type' => $this->input->post('input_type'),
				'is_reversed' => $this->input->post('is_reversed') ? 1 : 0,
			);

			$save = $this->model_mhcu_instrument->save_item($save_data);

			if ($save) {
				$this->data['success'] = true;
				$this->data['message'] = 'Item berhasil ditambahkan';
			} else {
				$this->data['success'] = false;
				$this->data['message'] = 'Gagal menambahkan item';
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Validasi gagal';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	public function edit_item($id_instrument, $id_item)
	{
		$this->is_allowed('mhcu_instrument_update');

		$this->form_validation->set_rules('text_item', 'Teks Item', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('no_urut_item', 'No Urut', 'trim|required|integer');
		$this->form_validation->set_rules('id_skala', 'Skala', 'trim|required|integer');
		$this->form_validation->set_rules('dimensi_aspek', 'Dimensi/Aspek', 'trim|max_length[200]');
		$this->form_validation->set_rules('input_type', 'Tipe Input', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('is_reversed', 'Reversed', 'trim|integer');

		if ($this->form_validation->run()) {
			$save_data = array(
				'text_item' => $this->input->post('text_item'),
				'no_urut_item' => $this->input->post('no_urut_item'),
				'id_skala' => $this->input->post('id_skala'),
				'dimensi_aspek' => $this->input->post('dimensi_aspek'),
				'input_type' => $this->input->post('input_type'),
				'is_reversed' => $this->input->post('is_reversed') ? 1 : 0,
			);

			$save = $this->model_mhcu_instrument->update_item($id_item, $save_data);

			if ($save) {
				$this->data['success'] = true;
				$this->data['message'] = 'Item berhasil diupdate';
			} else {
				$this->data['success'] = false;
				$this->data['message'] = 'Tidak ada perubahan';
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Validasi gagal';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	public function delete_item($id_item)
	{
		$this->is_allowed('mhcu_instrument_update');

		$remove = $this->model_mhcu_instrument->delete_item($id_item);

		if ($remove) {
			$this->data['success'] = true;
			$this->data['message'] = 'Item berhasil dihapus';
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Gagal menghapus item';
		}

		echo json_encode($this->data);
	}
	
	public function delete($id = null)
	{
		$this->is_allowed('mhcu_instrument_delete');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$this->model_mhcu_instrument->delete_items_by_instrument($id);
			$remove = $this->model_mhcu_instrument->remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$this->model_mhcu_instrument->delete_items_by_instrument($id);
				$remove = $this->model_mhcu_instrument->remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'Instrument MHCU'), 'success');
        } else {
            set_message(cclang('error_delete', 'Instrument MHCU'), 'error');
        }

		redirect_back();
	}

	public function export()
	{
		if (!$this->is_allowed('mhcu_instrument_list', false)) {
			set_message('Anda tidak memiliki akses', 'error');
			redirect_back();
		}

		$filter = $this->input->get('q');
		$field = $this->input->get('f');

		// Validasi: wajib ada filter/search
		if (empty($filter)) {
			set_message('Pilih filter atau ketik kata kunci terlebih dahulu sebelum export', 'warning');
			redirect_back();
		}

		$data = $this->model_mhcu_instrument->get($filter, $field, 0, 0);

		$this->load->library('excel');
		$this->excel->setActiveSheetIndex(0);
		$sheet = $this->excel->getActiveSheet();

		// Header
		$sheet->setCellValue('A1', 'No Urut');
		$sheet->setCellValue('B1', 'Kode Instrument');
		$sheet->setCellValue('C1', 'Nama Instrument');
		$sheet->setCellValue('D1', 'Jumlah Item');

		$sheet->getStyle('A1:D1')->getFont()->setBold(true);
		$sheet->getStyle('A1:D1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F8F9FA');

		// Data
		$row = 2;
		foreach ($data as $i => $item) {
			$jml_item = count($this->model_mhcu_instrument->get_items($item->id_instrument));
			$sheet->setCellValue('A' . $row, $item->no_urut);
			$sheet->setCellValue('B' . $row, $item->kode_instrument);
			$sheet->setCellValue('C' . $row, $item->nama_instrument);
			$sheet->setCellValue('D' . $row, $jml_item);
			$row++;
		}

		// Auto size
		foreach (range('A', 'D') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		$filename = 'Instrument_MHCU_' . date('Y-m-d_His');

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	public function get_detail($id)
	{
		if (!$this->is_allowed('mhcu_instrument_view', false)) {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Unauthorized')));
			return;
		}

		$instrument = $this->model_mhcu_instrument->find($id);

		if ($instrument) {
			$instrument->items = $this->model_mhcu_instrument->get_items($id);
			$this->output->set_content_type('application/json')->set_output(json_encode($instrument));
		} else {
			$this->output->set_content_type('application/json')->set_output(json_encode(array('error' => 'Data tidak ditemukan')));
		}
	}

}

/* End of file Mhcu_instrument.php */
