<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model
{

    public function login($username, $password)
    {
        $user = $this->db->select('*')
            ->from('sikat_users')
            ->where('username', $username)
            ->where('password', "aes_encrypt('$password', 'spekta')", FALSE)
            ->get()
            ->row();
        return $user;
    }

    public function create(array $obj)
    {
        $data = Util::copyIfNotEmpty(['username','password','name','email','role'], $obj);
        $this->db->set('username', $data['username']);
        $this->db->set('password',"AES_ENCRYPT('{$data['password']}','spekta')",FALSE);
        $this->db->set('name', $data['name']);
        $this->db->set('email', $data['email']);
        $this->db->set('role', $data['role']);
        $this->db->insert('sikat_users');
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('sikat_users');
    }

    public function update(array $obj, $id)
    {
        $data = Util::copyIfNotEmpty(['username','password','name','email','role'], $obj);
        $data['id'] = $id;
        $this->db->set('id', $data['id']);
        $this->db->set('username', $data['username']);
        $this->db->set('password',"AES_ENCRYPT('{$data['password']}','spekta')",FALSE);
        $this->db->set('name', $data['name']);
        $this->db->set('email', $data['email']);
        $this->db->set('role', $data['role']);
        return $this->db->replace('sikat_users');
    }

    public function get($id) {
        $user = $this->db->select("id, username, AES_DECRYPT(password, 'spekta') as password, name, email, role", FALSE)
            ->from('sikat_users')
            ->where('id', $id)
            ->get()
            ->row();
        return $user;
    }

    public function all()
    {
        return $this->db->get('sikat_users')->result();
    }
    
}




