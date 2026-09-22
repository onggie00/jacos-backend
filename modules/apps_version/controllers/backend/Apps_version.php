<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apps Version Controller
*| --------------------------------------------------------------------------
*| Apps Version site
*|
*/
class Apps_version extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apps_version');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apps Versions
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apps_version_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apps_versions'] = $this->model_apps_version->get($filter, $field, $this->limit_page, $offset);
		$this->data['apps_version_counts'] = $this->model_apps_version->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apps_version/index/',
			'total_rows'   => $this->model_apps_version->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('App Version Settings List');
		$this->render('backend/standart/administrator/apps_version/apps_version_list', $this->data);
	}
	
	/**
	* Add new apps_versions
	*
	*/
	public function add()
	{
		$this->is_allowed('apps_version_add');

		$this->template->title('App Version Settings New');
		$this->render('backend/standart/administrator/apps_version/apps_version_add', $this->data);
	}

	/**
	* Add New Apps Versions
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('apps_version_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('android_ver', 'Android', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('ios_ver', 'IOS', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('is_active_ver', 'Versi Yang Aktif?', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'android_ver' => $this->input->post('android_ver'),
				'ios_ver' => $this->input->post('ios_ver'),
				'release_note' => $this->input->post('release_note'),
				'is_active_ver' => $this->input->post('is_active_ver'),
			];

			
			$save_apps_version = $this->model_apps_version->store($save_data);
            

			if ($save_apps_version) {
				//export xml
				$this->export_xml();
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_apps_version;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/apps_version/edit/' . $save_apps_version, 'Edit Apps Version'),
						anchor('administrator/apps_version', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/apps_version/edit/' . $save_apps_version, 'Edit Apps Version')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_version');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_version');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Apps Versions
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('apps_version_update');

		$this->data['apps_version'] = $this->model_apps_version->find($id);

		$this->template->title('App Version Settings Update');
		$this->render('backend/standart/administrator/apps_version/apps_version_update', $this->data);
	}

	/**
	* Update Apps Versions
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('apps_version_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('android_ver', 'Android', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('ios_ver', 'IOS', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('is_active_ver', 'Versi Yang Aktif?', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'android_ver' => $this->input->post('android_ver'),
				'ios_ver' => $this->input->post('ios_ver'),
				'release_note' => $this->input->post('release_note'),
				'is_active_ver' => $this->input->post('is_active_ver'),
				'created_at' => $this->input->post('created_at'),
				'updated_at' => $this->input->post('updated_at'),
			];

			
			$save_apps_version = $this->model_apps_version->change($id, $save_data);

			if ($save_apps_version) {
				//export xml
				$this->export_xml();
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/apps_version', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/apps_version');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/apps_version');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Apps Versions
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apps_version_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'apps_version'), 'success');
        } else {
            set_message(cclang('error_delete', 'apps_version'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apps Versions
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apps_version_view');

		$this->data['apps_version'] = $this->model_apps_version->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('App Version Settings Detail');
		$this->render('backend/standart/administrator/apps_version/apps_version_view', $this->data);
	}
	
	/**
	* delete Apps Versions
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apps_version = $this->model_apps_version->find($id);

		
		
		return $this->model_apps_version->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apps_version_export');

		$this->model_apps_version->export('apps_version', 'apps_version');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apps_version_export');

		$this->model_apps_version->pdf('apps_version', 'apps_version');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apps_version_export');

		$table = $title = 'apps_version';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apps_version->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function export_xml(){
		$data_version = $this->mymodel->withquery("select * from apps_version where is_active_ver = '1'","row");
      //$xml = "<root_contact>";
$xml = '<rss version="2.0" xmlns:sparkle="http://www.andymatuschak.org/xml-namespaces/sparkle">
    <channel>
      <title>Labscib App</title>
      <item>
          <title>Version '.$data_version->android_ver.'</title>
          <description>'.$data_version->release_note.'</description>
          <pubDate>'.formatTanggal($data_version->created_at).' '.date('H:i:s', strtotime($data_version->created_at)).' +0007</pubDate>
          <enclosure url="https://play.google.com/store/apps/details?id=com.labschool.labscib" sparkle:version="'.$data_version->android_ver.'" sparkle:os="android" />
      </item>
      <item>
        <title>Version '.$data_version->ios_ver.'</title>
        <description>'.$data_version->release_note.'</description>
        <pubDate>'.formatTanggal($data_version->created_at).' '.date('H:i:s', strtotime($data_version->created_at)).' +0007</pubDate>
        <enclosure url="https://apps.apple.com/id/app/labscib-app/id1666453482" sparkle:version="'.$data_version->ios_ver.'" sparkle:os="iOS" />
        <sparkle:minimumSystemVersion>12.0.0</sparkle:minimumSystemVersion>
      </item>
    </channel>
</rss>';
      //$xml .= "</root_contact>";

      $sxe = new SimpleXMLElement($xml);
      $dom = new DOMDocument('1,0');
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($sxe->asXML());

      //echo $dom->saveXML();

      $dom->save('../../../../labscib_ver.xml');
	}
	
}


/* End of file apps_version.php */
/* Location: ./application/controllers/administrator/Apps Version.php */