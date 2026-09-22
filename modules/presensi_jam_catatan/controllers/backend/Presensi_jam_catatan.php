<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Jam Catatan Controller
*| --------------------------------------------------------------------------
*| Master jam catatan presensi siswa (referensi oleh presensi_catatan_detail).
*| Jam 1-12 (is_lainnya = 0) dan "lainnya" (is_lainnya = 1).
*/
class Presensi_jam_catatan extends Admin
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_presensi_jam_catatan');
        $this->lang->load('web_lang', $this->current_lang);
    }

    public function index($offset = 0)
    {
        $this->is_allowed('presensi_jam_catatan_list');

        $filter = $this->input->get('q');
        $field  = $this->input->get('f');

        $this->data['presensi_jam_catatans'] = $this->model_presensi_jam_catatan->get($filter, $field, $this->limit_page, $offset);
        $this->data['presensi_jam_catatan_counts'] = $this->model_presensi_jam_catatan->count_all($filter, $field);

        $config = array(
            'base_url'     => 'administrator/presensi_jam_catatan/index/',
            'total_rows'   => $this->data['presensi_jam_catatan_counts'],
            'per_page'     => $this->limit_page,
            'uri_segment'  => 4,
        );
        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Jam Catatan Presensi List');
        $this->render('backend/standart/administrator/presensi_jam_catatan/presensi_jam_catatan_list', $this->data);
    }

    public function add()
    {
        $this->is_allowed('presensi_jam_catatan_add');
        $this->template->title('Jam Catatan Presensi New');
        $this->render('backend/standart/administrator/presensi_jam_catatan/presensi_jam_catatan_add', $this->data);
    }

    public function add_save()
    {
        if (!$this->is_allowed('presensi_jam_catatan_add', false)) {
            echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
            exit;
        }

        $this->form_validation->set_rules('jam', 'Jam', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('is_lainnya', 'Lainnya', 'trim|numeric');

        if ($this->form_validation->run()) {
            $save_data = array(
                'jam'        => $this->input->post('jam'),
                'is_lainnya' => (int)($this->input->post('is_lainnya') ? 1 : 0),
            );

            $save_id = $this->model_presensi_jam_catatan->store($save_data);

            if ($save_id) {
                if ($this->input->post('save_type') == 'stay') {
                    $this->data['success'] = true;
                    $this->data['id']      = $save_id;
                    $this->data['message'] = cclang('success_save_data_stay', array(
                        anchor('administrator/presensi_jam_catatan/edit/' . $save_id, 'Edit Jam'),
                        anchor('administrator/presensi_jam_catatan', ' Go back to list')
                    ));
                } else {
                    set_message(cclang('success_save_data_redirect', array(
                        anchor('administrator/presensi_jam_catatan/edit/' . $save_id, 'Edit Jam')
                    )), 'success');
                    $this->data['success']  = true;
                    $this->data['redirect'] = base_url('administrator/presensi_jam_catatan');
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
        $this->is_allowed('presensi_jam_catatan_update');
        $this->data['presensi_jam_catatan'] = $this->model_presensi_jam_catatan->find($id);
        $this->template->title('Jam Catatan Presensi Update');
        $this->render('backend/standart/administrator/presensi_jam_catatan/presensi_jam_catatan_update', $this->data);
    }

    public function edit_save($id)
    {
        if (!$this->is_allowed('presensi_jam_catatan_update', false)) {
            echo json_encode(array('success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')));
            exit;
        }

        $this->form_validation->set_rules('jam', 'Jam', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('is_lainnya', 'Lainnya', 'trim|numeric');

        if ($this->form_validation->run()) {
            $save_data = array(
                'jam'        => $this->input->post('jam'),
                'is_lainnya' => (int)($this->input->post('is_lainnya') ? 1 : 0),
            );

            $save_ok = $this->model_presensi_jam_catatan->change($id, $save_data);

            if ($save_ok) {
                if ($this->input->post('save_type') == 'stay') {
                    $this->data['success'] = true;
                    $this->data['id']      = $id;
                    $this->data['message'] = cclang('success_save_data_stay', array(
                        anchor('administrator/presensi_jam_catatan/edit/' . $id, 'Edit Jam'),
                        anchor('administrator/presensi_jam_catatan', ' Go back to list')
                    ));
                } else {
                    set_message(cclang('success_save_data_redirect', array(
                        anchor('administrator/presensi_jam_catatan/edit/' . $id, 'Edit Jam')
                    )), 'success');
                    $this->data['success']  = true;
                    $this->data['redirect'] = base_url('administrator/presensi_jam_catatan');
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
        $this->is_allowed('presensi_jam_catatan_view');
        $this->data['presensi_jam_catatan'] = $this->model_presensi_jam_catatan->join_avaiable()->filter_avaiable()->find($id);
        $this->template->title('Jam Catatan Presensi Detail');
        $this->render('backend/standart/administrator/presensi_jam_catatan/presensi_jam_catatan_view', $this->data);
    }

    /**
     * Hapus permanen (master data kecil, tidak ada kolom status).
     */
    public function remove($id = null)
    {
        $this->is_allowed('presensi_jam_catatan_delete');

        $row = $this->model_presensi_jam_catatan->find($id);
        if (!$row) {
            set_message(cclang('error_delete', 'presensi_jam_catatan'), 'error');
            redirect_back();
            return;
        }

        if ($this->model_presensi_jam_catatan->remove($id)) {
            set_message(cclang('has_been_deleted', 'jam'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_jam_catatan'), 'error');
        }
        redirect_back();
    }
}

/* End of file Presensi_jam_catatan.php */
/* Location: ./modules/presensi_jam_catatan/controllers/backend/Presensi_jam_catatan.php */
