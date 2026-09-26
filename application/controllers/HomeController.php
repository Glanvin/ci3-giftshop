<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HomeController extends CI_Controller {

	// Default route for `/`: a static landing page with no data of its own.
	// Wrapped in the public header and shared footer like every other page.
	public function index()
	{
		$data = array(
			'title' => 'Capitol University Official Giftshop',
			'active' => 'home'
		);

		$this->load->view('templates/public_header', $data);
		$this->load->view('home', $data);
		$this->load->view('templates/footer', $data);
	}
}