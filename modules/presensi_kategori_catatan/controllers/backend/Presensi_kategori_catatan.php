<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Kategori Catatan Controller
*| --------------------------------------------------------------------------
*| Master kategori catatan pelanggaran pelajaran (referensi oleh presensi_catatan_detail).
*/
class Presensi_kategori_catatan extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_presensi_kategori_catatan');
        $this->lang->load('web_lang', $this->current_lang);
    }

    public function index($offset = 0)
    {
        $this->is_allowed('presensi_kategori_catatan_list');

        $filter = $this->input->get('q');
        $field  = $this->input->get('f');

        $this->data['presensi_kategori_catatans'] = $this->model_presensi_kategori_catatan->get($filter, $field, $this->limit_page, $offset);
        $this->data['presensi_kategori_catatan_counts'] = $this->model_presensi_kategori_catatan->count_all($filter, $field);

        $config = array(
            'base_url'     => 'administrator/presensi_kategori_catatan/index/',
            'total_rows'   => $this->data['presensi_kategori_catatan_counts'],
            'per_page'     => $this->limit_page,
            'uri_segment'  => 4,
        );
        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Kategori Catatan Pelajaran List');
        $this->render('backend/standart/administrator/presensi_kategori_catatan/presensi_kategori_catatan_list', $this->data);
    }

    public function add()
    {
        $this->is_allowed('presensi_kategori_catatan_add');
        $this->template->title('Kategori Catatan Pelajaran New');
        $this->render('backend/standart/administrator/presensi_kategori_catatan/presensi_kategori_catatan_add', $this->data);
    }

    public function add_save()
    {
        if (!$this->is_allowed('presensi_kategori_catatan_add', false)) {
            echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
            exit;
        }

        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('skor', 'Skor', 'trim|required|numeric|greater_equal[0]');
        $this->form_validation->set_rules('is_custom', 'Custom', 'trim|numeric');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[aktif,nonaktif]');

        if ($this->form_validation->run()) {
            $save_data = array(
                'nama_kategori' => $this->input->post('nama_kategori'),
                'skor'          => (int)$this->input->post('skor'),
                'is_custom'     => (int)($this->input->post('is_custom') ? 1 : 0),
                'status'        => $this->input->post('status'),
                'updated_by'    => $this->aauth->get_user()->username ?? null,
            );

            $save_id = $this->model_presensi_kategori_catatan->store($save_data);

            if ($save_id) {
                if ($this->input->post('save_type') == 'stay') {
                    $this->data['success'] = true;
                    $this->data['id']      = $save_id;
                    $this->data['message'] = cclang('success_save_data_stay', array(
                        anchor('administrator/presensi_kategori_catatan/edit/' . $save_id, 'Edit Kategori'),
                        anchor('administrator/presensi_kategori_catatan', ' Go back to list')
                    ));
                } else {
                    set_message(cclang('success_save_data_redirect', array(
                        anchor('administrator/presensi_kategori_catatan/edit/' . $save_id, 'Edit Kategori')
                    )), 'success');
                    $this->data['success']  = true;
                    $this->data['redirect'] = base_url('administrator/presensi_kategori_catatan');
                }
            } else {
                $this->data['success'] = false;
                $this->data['message'] = cclang('data_not_change');
            }
        } else {
            $this->data['success'] = false;
            $this->data['message'] = 'Opss validation failed';
            $this->data['errors']  = $this->form_validation->error_array();
        }

        echo json_encode($this->data);
    }

    public function edit($id)
    {
        $this->is_allowed('presensi_kategori_catatan_update');
        $this->data['presensi_kategori_catatan'] = $this->model_presensi_kategori_catatan->find($id);
        $this->template->title('Kategori Catatan Pelajaran Update');
        $this->render('backend/standart/administrator/presensi_kategori_catatan/presensi_kategori_catatan_update', $this->data);
    }

    public function edit_save($id)
    {
        if (!$this->is_allowed('presensi_kategori_catatan_update', false)) {
            echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
            exit;
        }

        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('skor', 'Skor', 'trim|required|numeric|greater_equal[0]');
        $this->form_validation->set_rules('is_custom', 'Custom', 'trim|numeric');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[aktif,nonaktif]');

        if ($this->form_validation->run()) {
            $save_data = array(
                'nama_kategori' => $this->input->post('nama_kategori'),
                'skor'          => (int)$this->input->post('skor'),
                'is_custom'     => (int)($this->input->post('is_custom') ? 1 : 0),
                'status'        => $this->input->post('status'),
                'updated_at'    => date('Y-m-d H:i:s'),
                'updated_by'    => $this->aauth->get_user()->username ?? null,
            );

            $save_ok = $this->model_presensi_kategori_catatan->change($id, $save_data);

            if ($save_ok) {
                if ($this->input->post('save_type') == 'stay') {
                    $this->data['success'] = true;
                    $this->data['id']      = $id;
                    $this->data['message'] = cclang('success_save_data_stay', array(
                        anchor('administrator/presensi_kategori_catatan/edit/' . $id, 'Edit Kategori'),
                        anchor('administrator/presensi_kategori_catatan', ' Go back to list')
                    ));
                } else {
                    set_message(cclang('success_save_data_redirect', array(
                        anchor('administrator/presensi_kategori_catatan/edit/' . $id, 'Edit Kategori')
                    )), 'success');
                    $this->data['success']  = true;
                    $this->data['redirect'] = base_url('administrator/presensi_kategori_catatan');
                }
            } else {
                $this->data['success'] = false;
                $this->data['message'] = cclang('data_not_change');
            }
        } else {
            $this->data['success'] = false;
            $this->data['message'] = 'Opss validation failed';
            $this->data['errors']  = $this->form_validation->error_array();
        }

        echo json_encode($this->data);
    }

    public function view($id)
    {
        $this->is_allowed('presensi_kategori_catatan_view');
        $this->data['presensi_kategori_catatan'] = $this->model_presensi_kategori_catatan->join_avaiable()->filter_avaiable()->find($id);
        $this->template->title('Kategori Catatan Pelajaran Detail');
        $this->render('backend/standart/administrator/presensi_kategori_catatan/presensi_kategori_catatan_view', $this->data);
    }

    /**
     * Soft-disable: set status='nonaktif' (data histori tetap aman via FK RESTRICT).
     * Bukan row delete.
     */
    public function remove($id = null)
    {
        $this->is_allowed('presensi_kategori_catatan_delete');

        $row = $this->model_presensi_kategori_catatan->find($id);
        if (!$row) {
            set_message(cclang('error_delete', 'presensi_kategori_catatan'), 'error');
            redirect_back();
            return;
        }

        $save_data = array(
            'status'     => 'nonaktif',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->aauth->get_user()->username ?? null,
        );
        $ok = $this->model_presensi_kategori_catatan->change($id, $save_data);
        if ($ok) {
            set_message(cclang('has_been_disabled', 'kategori'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_kategori_catatan'), 'error');
        }
        redirect_back();
    }

    public function export()
    {
        $this->is_allowed('presensi_kategori_catatan_export');
        $this->model_presensi_kategori_catatan->export('presensi_kategori_catatan', 'presensi_kategori_catatan');
    }

    public function export_pdf()
    {
        $this->is_allowed('presensi_kategori_catatan_export');
        $this->model_presensi_kategori_catatan->pdf('presensi_kategori_catatan', 'presensi_kategori_catatan');
    }
}
