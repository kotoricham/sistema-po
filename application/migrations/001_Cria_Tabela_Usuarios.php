<?php
defined('BASEPATH')	OR	exit('No direct	script access allowed');

class Migration_Cria_tabela_usuarios extends CI_Migration {
	
	public function up()
	{
			$this->dbforge->add_field(array(
					'id' =>	array(
						'type' => 'INT',
						'constraint' =>	11,
						'unsigned' => TRUE,
						'auto_increment' =>	FALSE
					),
					'nome' => array(
						'type' => 'VARCHAR',
						'constraint' =>	'100',
					),
					'email' => array(
						'type' => 'VARCHAR',
						'constraint' =>	'100',
					),
					'senha' => array(
						'type' => 'VARCHAR',
						'constraint' =>	'100',
					),
			));
			$this->dbforge->add_key('id',	TRUE);
			$this->dbforge->create_table('tbl_usuario');
	}

	public function down()
	{
			$this->dbforge->drop_table('tbl_usuario');
	}
}