<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'ci_users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'role', 'spv', 'stase', 'nama_lengkap', 'photo'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $db;
    protected $builder;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('ci_users');
    }

    function userProfile($id_user)
    {
        $query = $this->builder->getWhere(['id' => $id_user])->getRowObject();
        return $query;
    }

    public function getUserById($id_user)
    {
        // $query = $this->builder->getWhere(['id' => $id_user])->getRowObject();
        $query = $this->db->query(
            "SELECT *,ci_users.id AS id_ppds FROM ci_users 
            LEFT JOIN tahap_ppds ON tahap_ppds.id_user = ci_users.id
            LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
            LEFT JOIN stase ON stase.id = stase_ppds.id_stase
            LEFT JOIN tahap ON tahap.id = tahap_ppds.id_tahap
            WHERE ci_users.id = $id_user AND tahap_ppds.id = (SELECT MAX(id) FROM tahap_ppds WHERE id_user = $id_user) AND stase_ppds.id = (SELECT MAX(id) FROM stase_ppds WHERE id_user = $id_user)
            "
        )->getRowObject();
        return $query;
    }

    public function getAll()
    {
        $this->builder->select('*,role.role as nama_role,ci_users.id as id_ppds');
        $this->builder->join('role', 'role.id = ci_users.role');
        $query = $this->builder->get()->getResultArray();
        return $query;
    }

    public function getSpv()
    {
        $query = $this->builder->getWhere(['role' => 3])->getResultArray();
        return $query;
    }

    public function getCurrentUserData()
    {
        $id_user = session('user_id');
        // $this->builder->select('*');
        // $this->builder->join('stase_ppds', 'stase_ppds.id_user = ci_users.id');
        // $this->builder->join('stase', 'stase.id = stase_ppds.id_stase');
        // $this->builder->where('ci_users.id', $id_user);
        // $query = $this->builder->get()->getRowObject();
        $query = $this->db->query("SELECT * FROM ci_users 
        LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
        LEFT JOIN stase ON stase.id = stase_ppds.id_stase
        WHERE ci_users.id = $id_user AND stase_ppds.id = (SELECT MAX(id) FROM stase_ppds WHERE id_user = $id_user)")->getRowObject();
        return $query;
    }

    public function getPpds()
    {
        // return $this->builder->getWhere(['role' => 4])->getResultArray();
        $this->builder->select('*,ci_users.id as id_ppds,stase.stase as nama_stase');
        $this->builder->join('stase_ppds', 'stase_ppds.id_user = ci_users.id');
        $this->builder->join('stase', 'stase.id = stase_ppds.id_stase');
        // $this->builder->join('tahap_ppds', 'tahap_ppds.id_user = ci_users.id');
        // $this->builder->join('tahap', 'tahap.id = tahap_ppds.id_tahap');
        $this->builder->where('ci_users.role', 4);
        $query = $this->builder->get()->getResultArray();
        return $query;
    }

    public function getPpdsWithoutStase()
    {
        // return $this->builder->getWhere(['role' => 4])->getResultArray();
        // $this->builder->select('*,ci_users.id as id_ppds,stase.stase as nama_stase,stase_ppds.id as stase_ppds_id');
        // $this->builder->join('stase_ppds', 'stase_ppds.id_user = ci_users.id');
        // $this->builder->join('stase', 'stase.id = stase_ppds.id_stase');
        // $this->builder->join('tahap_ppds', 'tahap_ppds.id_user = ci_users.id');
        // $this->builder->join('tahap', 'tahap.id = tahap_ppds.id_tahap');
        // $this->builder->where('ci_users.role', 4);
        // $this->builder->where('stase_ppds.id_stase', 25);
        $query = $this->builder->get()->getResultArray();
        $query = $this->db->query(
            "SELECT *,ci_users.id as id_ppds,stase.stase as nama_stase,stase_ppds.id as stase_ppds_id FROM ci_users 
            LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
            LEFT JOIN tahap_ppds ON tahap_ppds.id_user = ci_users.id
            LEFT JOIN tahap ON tahap.id = tahap_ppds.id_tahap
            LEFT JOIN stase ON stase.id = stase_ppds.id_stase
            WHERE tahap_ppds.id = (SELECT MAX(id) FROM tahap_ppds WHERE id_user = ci_users.id) AND stase_ppds.id_stase = 25 
            "
        )->getResultArray();
        return $query;
    }

    public function getPpdsByTahap($tahap)
    {
        return $this->db->query("SELECT ci_users.nama_lengkap,ci_users.id AS id_ppds,stase_ppds.id_stase,stase.stase,stase_ppds.tanggal_mulai,stase_ppds.tanggal_selesai FROM ci_users
        LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
        LEFT JOIN stase ON stase.id = stase_ppds.id_stase
        WHERE stase.id_tahap = $tahap AND stase.id != 25")->getResultArray();
        // tahap_ppds.id = (SELECT MAX(id) FROM tahap_ppds WHERE id_user = ci_users.id) AND
    }

    public function getPpdsByStase()
    {
        $spv_stase = session('stase');

        $query = $this->db->query("SELECT *,ci_users.id AS id_ppds FROM ci_users 
        LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
        LEFT JOIN stase ON stase.id = stase_ppds.id_stase
        WHERE stase_ppds.id = (SELECT MAX(id) FROM stase_ppds WHERE id_user = ci_users.id) AND stase_ppds.id_stase = $spv_stase")->getResultArray();
        return $query;
    }

    public function getPpdsBySpv()
    {
        $spv_id = session('user_id');

        $query = $this->db->query("SELECT *,ci_users.id AS id_ppds FROM ci_users 
        LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
        LEFT JOIN stase ON stase.id = stase_ppds.id_stase
        WHERE stase_ppds.id = (SELECT MAX(id) FROM stase_ppds WHERE id_user = ci_users.id) AND ci_users.spv = $spv_id")->getResultArray();
        return $query;
    }

    public function staseSelesai($id_ppds)
    {
        $id_stase_ppds = $this->db->query("SELECT MAX(id) AS id FROM stase_ppds WHERE id_user = $id_ppds")->getRowObject()->id;
        date_default_timezone_set("Asia/Jakarta");
        $data = [
            'tanggal_selesai' => date('Y:m:d'),
        ];

        $query = $this->db->table('stase_ppds')->update($data, array('id' => $id_stase_ppds, 'id_user' => $id_ppds));
        return $query;
    }

    public function tahapSelesai($id_ppds)
    {
        $id_tahap_ppds = $this->db->query("SELECT MAX(id) AS id FROM tahap_ppds WHERE id_user = $id_ppds")->getRowObject()->id;
        date_default_timezone_set("Asia/Jakarta");
        $data = [
            'tanggal_selesai' => date('Y:m:d'),
        ];

        $query = $this->db->table('tahap_ppds')->update($data, array('id' => $id_tahap_ppds, 'id_user' => $id_ppds));
        return $query;
    }
}
