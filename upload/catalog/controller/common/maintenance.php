<?php
class ControllerCommonMaintenance extends Controller {
	public function index() {
		if ($this->config->get('config_maintenance')) {
			$route = '';

			if (isset($this->request->get['route'])) {
				$part = explode('/', $this->request->get['route']);

				if (isset($part[0])) {
					$route .= $part[0];
				}
			}

			// Show site if logged in as admin
			$this->load->library('user');

			$this->user = new User($this->registry);

			if (($route != 'payment') && !$this->user->isLogged()) {
				return $this->forward('common/maintenance/info');
			}
		}
	}

	public function info() {
		$this->language->load('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['message'] = $this->language->get('text_message');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 503 Service Temporarily Unavailable');
		$this->response->addHeader('Retry-After: ' . gmdate('D, d M Y H:i:s T', time() + 60 * 60 * 24));

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/maintenance.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/maintenance.tpl';
		} else {
			$this->template = 'default/template/common/maintenance.tpl';
		}

		$this->children = array(
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
?>
