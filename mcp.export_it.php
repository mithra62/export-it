<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * mithra62 - Export It
 *
 * @package		mithra62:Export_it
 * @author		Eric Lamb
 * @copyright	Copyright (c) 2011, mithra62, Eric Lamb.
 * @link		http://mithra62.com/projects/view/export-it/
 * @version		1.0
 * @filesource 	./system/expressionengine/third_party/export_it/
 */
 
 /**
 * Export It - CP Class
 *
 * Control Panel class
 *
 * @package 	mithra62:Export_it
 * @author		Eric Lamb
 * @filesource 	./system/expressionengine/third_party/export_it/mcp.export_it.php
 */
class Export_it_mcp 
{
	public $url_base = '';
	
	/**
	 * The name of the module; used for links and whatnots
	 * @var string
	 */
	private $mod_name = 'export_it';
	
	public function __construct()
	{
		$this->EE =& get_instance();
		
		//load EE stuff
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		$this->EE->load->model('export_it_settings_model', 'export_it_settings');
		$this->EE->load->library('export_it_lib');
		$this->EE->load->library('export_it_js');
		$this->EE->load->library('member_data');
		$this->EE->load->library('channel_data');  
		$this->EE->load->library('encrypt');
		$this->EE->load->library('Export_data/export_data');
		
		$this->EE->load->add_package_path(PATH_MOD.'mailinglist/'); 
		$this->EE->load->model('mailinglist_model');		

		$this->settings = $this->EE->export_it_lib->get_settings();		

		$this->query_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->mod_name.AMP.'method=';
		$this->url_base = BASE.AMP.$this->query_base;
		$this->EE->export_it_lib->set_url_base($this->url_base);
		
		$this->EE->cp->set_variable('url_base', $this->url_base);
		$this->EE->cp->set_variable('query_base', $this->query_base);	
		
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->mod_name, $this->EE->lang->line('export_it_module_name'));
		$this->EE->cp->set_right_nav($this->EE->export_it_lib->get_right_menu());	
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('export_it_module_name'));
		
		$this->errors = $this->EE->export_it_lib->error_check();
		$this->EE->cp->set_variable('errors', $this->errors);
		$this->EE->cp->set_variable('settings', $this->settings);
		
	}
	
	public function index()
	{
		$this->EE->functions->redirect($this->url_base.'members');
		exit;
	}
	
	public function members()
	{
		if(isset($_POST['export_members']))
		{
			$export_format = $this->EE->input->get_post('export_format');
			$group_id = $this->EE->input->get_post('group_id');
			$include_custom_fields = $this->EE->input->get_post('include_custom_fields');
			$complete_select = $this->EE->input->get_post('complete_select');
			$this->EE->export_data->export_members($export_format, $group_id, $include_custom_fields, $complete_select);
			exit;
		}
				
		$vars = array();
		$vars['member_groups_dropdown'] = $this->EE->member_data->get_member_groups();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('members');
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('members'));
		return $this->EE->load->view('members', $vars, TRUE);		
	}
	
	public function channel_entries()
	{
		if(isset($_POST['export_channel_entries']))
		{
			$export_format = $this->EE->input->get_post('export_format');
			$date_range = $this->EE->input->get_post('date_range');
			$channel_id = $this->EE->input->get_post('channel_id');
			$this->EE->export_data->export_channel_entries($export_format, $channel_id, $date_range);
			exit;			
		}
		
		$vars = array();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('channel_entries');
		$vars['comment_channels'] = $this->EE->export_it_lib->get_comment_channels();
		$vars['date_select'] = $this->EE->export_it_lib->get_date_select();
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('channel_entries'));
		return $this->EE->load->view('channel_entries', $vars, TRUE);			
	}
	
	public function comments()
	{
		if(isset($_POST['export_comments']))
		{
			$export_format = $this->EE->input->get_post('export_format');
			$date_range = $this->EE->input->get_post('date_range');
			$status = $this->EE->input->get_post('status');
			$channel_id = $this->EE->input->get_post('channel_id');
			$this->EE->export_data->export_comments($export_format, $date_range, $status, $channel_id);
			exit;			
		}
		
		$vars = array();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('comments');
		$vars['comment_channels'] = $this->EE->export_it_lib->get_comment_channels();
		$vars['date_select'] = $this->EE->export_it_lib->get_date_select();
		$vars['status_select'] = $this->EE->export_it_lib->get_status_select();
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('comments'));
		return $this->EE->load->view('comments', $vars, TRUE);		
	}
	
	public function mailing_list()
	{
		if(isset($_POST['export_mailing_list']))
		{
			$export_format = $this->EE->input->get_post('export_format');
			$exclude_duplicates = $this->EE->input->get_post('exclude_duplicates');
			$mailing_list = $this->EE->input->get_post('list_id');
			
			$this->EE->export_data->export_mailing_list($export_format, $exclude_duplicates, $mailing_list);
			exit;
		}
		$vars = array();
		$vars['export_format'] = $this->EE->export_it_lib->export_formats('mailing_list');
		$vars['mailing_lists'] = $this->EE->export_it_lib->get_mailing_lists();
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('mailing_list'));
		return $this->EE->load->view('mailing_list', $vars, TRUE);		
	}

	public function settings()
	{
		if(isset($_POST['go_settings']))
		{		
			if($this->EE->export_it_settings->update_settings($_POST))
			{	
				$this->EE->logger->log_action($this->EE->lang->line('log_settings_updated'));
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('settings_updated'));
				$this->EE->functions->redirect($this->url_base.'settings');		
				exit;			
			}
			else
			{
				$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('settings_update_fail'));
				$this->EE->functions->redirect($this->url_base.'settings');	
				exit;					
			}
		}
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('settings'));
		
		$this->EE->cp->add_js_script('ui', 'accordion'); 
		$this->EE->javascript->output($this->EE->export_it_js->get_accordian_css()); 		
		$this->EE->javascript->compile();	

		$this->settings['api_key'] = $this->EE->encrypt->decode($this->settings['api_key']);
		$vars = array();
		$vars['api_url'] = $this->EE->config->config['site_url'].'?ACT='.$this->EE->cp->fetch_action_id('Export_it', 'api').'&key='.$this->settings['api_key'];
		$vars['settings'] = $this->settings;
		return $this->EE->load->view('settings', $vars, TRUE);
	}
}