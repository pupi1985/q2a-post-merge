<?php

class qa_merge_admin {

	function option_default($option) {
		switch ($option) {
			case 'merge_question_merged':
				return 'Redirected from merged question ^post';
			default:
				return null;
		}
	}

	function admin_form(&$qa_content) {
		//      Process form input

		$ok = null;
		$error1 = null;
		$error2 = null;

		if (qa_clicked('merge_question_process')) {
			$merged = noah_pm_merge_do_merge();
			if ($merged === true) {
				$ok = 'Posts merged.';
			} else {
				$error1 = $merged[0];
				$error2 = $merged[1];
				$qa_content['error'] = "Error merging posts.";
			}
		}

		//      Create the form for display

		$fields = array();

		$fields[] = array(
			'label' => 'From',
			'tags' => 'NAME="merge_from" id="merge_from"',
			'type' => 'number',
			'error' => $error1,
		);
		$fields[] = array(
			'label' => 'To',
			'tags' => 'NAME="merge_to" id="merge_to"',
			'type' => 'number',
			'error' => $error2,
		);
		$fields[] = array(
			'value' => '<input type="button" onclick="mergePluginGetPosts()" value="Show"><div id="merge_from_out"></div><div id="merge_to_out"></div>',
			'type' => 'static',
		);
		$fields[] = array(
			'type' => 'blank',
		);
		$fields[] = array(
			'label' => 'Text to show when redirecting from merged question',
			'tags' => 'NAME="merge_question_merged" id="merge_question_merged"',
			'value' => qa_opt('merge_question_merged'),
		);


		return array(
			'ok' => $ok,
			'fields' => $fields,
			'buttons' => array(
				array(
					'label' => 'Merge',
					'tags' => 'NAME="merge_question_process"',
				),
			),
		);
	}

	public function init_queries($table_list) {
		$tablename = qa_db_add_table_prefix('postmeta');
		if (!in_array($tablename, $table_list)) {
			return
				'CREATE TABLE IF NOT EXISTS ^postmeta (
					meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					post_id bigint(20) unsigned NOT NULL,
					meta_key varchar(255) DEFAULT \'\',
					meta_value longtext,
					PRIMARY KEY (meta_id),
					KEY post_id (post_id),
					KEY meta_key (meta_key)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8';
		}
	}

}
