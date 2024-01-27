<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'ci_users';
    protected $primaryKey = 'id';
    protected $allowedFields =
    [
        'username',
        'email',
        'password',
        'role',
        'spv',
        'stase',
        'nama_lengkap',
        'photo',
        'usia',
        'jenis_kelamin',
        'alamat_asal',
        'alamat_domisili',
        'no_telp',
        'no_telp_drt',
        'nim',
        'status',
        'pembiayaan',
        'no_sip',
        'no_str',
        'no_bpjs',
        'no_rekening',
        'aktif'
    ];
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

    function tableName($table_name)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table($table_name);
        return $builder;
    }

    function userProfile($id_user)
    {
        $this->builder->select('*,ci_users.id as id_ppds');
        $query = $this->builder->getWhere(['id' => $id_user])->getRowObject();
        return $query;
    }

    public function getUserById($id_user)
    {
        // $query = $this->builder->getWhere(['id' => $id_user])->getRowObject();
        // $query = $this->db->query(
        //     "SELECT 
        //     cu.*,
        //     ci_users.id AS id_ppds,
        //     ci_users.nama_lengkap AS nama_lengkap,
        //     cu.nama_lengkap AS spv,
        //     stase.stase AS stase 
        //     FROM ci_users 
        //     LEFT JOIN tahap_ppds ON tahap_ppds.id_user = ci_users.id
        //     LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
        //     LEFT JOIN stase ON stase.id = stase_ppds.id_stase
        //     LEFT JOIN tahap ON tahap.id = tahap_ppds.id_tahap
        //     LEFT JOIN ci_users cu ON cu.id = ci_users.spv
        //     WHERE ci_users.id = $id_user AND tahap_ppds.id = (SELECT MAX(id) FROM tahap_ppds WHERE id_user = $id_user) AND stase_ppds.id = (SELECT MAX(id) FROM stase_ppds WHERE id_user = $id_user)
        //     "
        // )->getRowObject();

        $tahap_ppds_id = $this->tableName('tahap_ppds')->selectMax('id')
            ->where(['id_user' => $id_user])
            ->get()->getRowObject()->id;

        $stase_ppds_id = $this->tableName('stase_ppds')->selectMax('id')
            ->where(['id_user' => $id_user])
            ->get()->getRowObject()->id;

        $data_ppds = $this->tableName('ci_users')->select(
            'cu.*,
        ci_users.id AS id_ppds,
        ci_users.nama_lengkap AS nama_lengkap,
        cu.nama_lengkap AS spv,
        stase.stase AS stase,
        tahap.tahap,
        stase_ppds.id_stase,
        ci_users.jenis_kelamin AS jenis_kelamin_ppds'
        )
            ->join('tahap_ppds', 'tahap_ppds.id_user = ci_users.id')
            ->join('stase_ppds', 'stase_ppds.id_user = ci_users.id')
            ->join('stase', 'stase.id = stase_ppds.id_stase')
            ->join('tahap', 'tahap.id = tahap_ppds.id_tahap')
            ->join('ci_users cu', 'cu.id = ci_users.spv')
            ->where([
                'ci_users.id' => $id_user,
                'tahap_ppds.id' => $tahap_ppds_id,
                'stase_ppds.id' => $stase_ppds_id,
            ])
            ->get()
            ->getRowObject();

        // $query = $this->db->query(
        //     "SELECT 
        //        cu.*           
        //        FROM ci_users cu
        //        WHERE cu.id = $id_user
        //        "
        // )->getRowObject();

        return $data_ppds;
    }

    public function getAll()
    {
        $this->builder->select('*,role.role as nama_role,ci_users.id as id_ppds');
        $this->builder->join('role', 'role.id = ci_users.role');
        // $this->builder->where(['aktif' => 1]);
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
        return $this->builder->getWhere(['role' => 4])->getResultArray();
        // $this->builder->select('*,ci_users.id as id_ppds');
        // $this->builder->where('ci_users.role', 4);
        // $query = $this->builder->get()->getResultArray();
        // return $query;
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
        if ($tahap != 0) {
            return $this->db->query(
                "SELECT 
            stase_ppds.keterangan,
            ci_users.nama_lengkap,
            ci_users.id AS id_ppds,
            stase_ppds.id_stase,
            stase.stase,
            stase_ppds.tanggal_mulai,
            stase_ppds.tanggal_selesai 
            FROM ci_users
            LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
            LEFT JOIN stase ON stase.id = stase_ppds.id_stase
            WHERE ci_users.aktif = 1
            AND stase.id != 25
            AND stase.id_tahap = $tahap"
            )->getResultArray();
        } else {
            return $this->db->query(
                "SELECT 
                stase_ppds.keterangan,
                ci_users.nama_lengkap,
                ci_users.id AS id_ppds,
                stase_ppds.id_stase,
                tahap_ppds.id_tahap,
                stase.stase,
                stase_ppds.tanggal_mulai,
                stase_ppds.tanggal_selesai 
                FROM ci_users
                LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
                LEFT JOIN tahap_ppds ON tahap_ppds.id_user = ci_users.id
                LEFT JOIN stase ON stase.id = stase_ppds.id_stase
                WHERE ci_users.aktif = 1
                AND stase.id != 25
                AND stase_ppds.id_stase = (
                    SELECT id_stase 
                    FROM stase_ppds 
                    WHERE id = (
                        SELECT MAX(id) 
                        FROM stase_ppds 
                        WHERE id_user = ci_users.id ))
                AND tahap_ppds.id_tahap = (
                    SELECT id_tahap
                    FROM tahap_ppds
                    WHERE id = (
                        SELECT MAX(id)
                        FROM tahap_ppds
                        WHERE id_user = ci_users.id))"
            )->getResultArray();
        }


        // tahap_ppds.id = (SELECT MAX(id) FROM tahap_ppds WHERE id_user = ci_users.id) AND
    }

    public function get_ppds_pertahap($tahap) {

        if(isset($_SESSION['stase'])) {
            if($_SESSION['stase'] == 0) {
                $stase = '';
            } else {
                $stase = "AND stase.id = " . $_SESSION['stase'];
            }
         } else  {
            $stase = '';
        }        

        return $this->db->query(
            "SELECT 
        stase_ppds.keterangan,
        ci_users.nama_lengkap,
        ci_users.id AS id_ppds,
        stase_ppds.id_stase,
        stase.stase,
        stase_ppds.tanggal_mulai,
        stase_ppds.tanggal_selesai 
        FROM ci_users
        LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
        LEFT JOIN stase ON stase.id = stase_ppds.id_stase
        WHERE ci_users.aktif = 1
        AND stase.id != 25
        $stase
        AND stase.id_tahap = $tahap"
        )->getResultArray();
    }

    public function getAllSupervisor()
    {
        // $query = $this->builder->getWhere(['id' => 2])->getRowObject();
        $this->builder->select('*,ci_users.id AS id_spv');
        $this->builder->join('role', 'role.id=ci_users.role');
        $this->builder->join('stase', 'stase.id=ci_users.stase');
        // $this->builder->where('role.id', 3);
        return $this->builder->getWhere(['role.id' => 3, 'ci_users.aktif' => 1])->getResultArray();
    }

    public function detailSpv($id_spv)
    {
        $this->builder->select('*,ci_users.id AS id_spv');
        $this->builder->join('role', 'role.id=ci_users.role', 'left');
        $this->builder->join('stase', 'stase.id=ci_users.stase', 'left');
        // $this->builder->where('role.id', 3);
        return $this->builder->getWhere(
            [
                'role.id' => 3,
                'ci_users.id' => $id_spv
            ]
        )->getRowObject();
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

        $query = $this->db->query(
            "SELECT 
            *,
            ci_users.id AS id_ppds
            FROM ci_users 
            LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id 
            LEFT JOIN stase ON stase.id = stase_ppds.id_stase
            LEFT JOIN tahap_ppds ON tahap_ppds.id_user = ci_users.id
            WHERE ci_users.aktif = 1
            AND stase_ppds.id = (SELECT MAX(id) FROM stase_ppds 
                                    WHERE id_user = ci_users.id) 
            AND tahap_ppds.id = (SELECT MAX(id) FROM tahap_ppds
                                    WHERE id_user = ci_users.id)
            AND ci_users.spv = $spv_id"
        )->getResultArray();
        // dd($query);
        return $query;
    }

    public function staseSelesai($id_ppds, $keterangan = '')
    {
        $id_stase_ppds = $this->db->query("SELECT MAX(id) AS id FROM stase_ppds WHERE id_user = $id_ppds")->getRowObject()->id;
        date_default_timezone_set("Asia/Jakarta");
        $data = [
            'tanggal_selesai' => date('Y:m:d'),
            'keterangan' => $keterangan
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

    public function countUserByRole($id_role)
    {
        $this->builder->select('*');
        $this->builder->where(
            [
                'role' => $id_role,
                'aktif' => 1,
            ]
        );
        return $this->builder->countAllResults();
    }

    public function getNewUsers()
    {
        $this->builder->select('*,role.role as nama_role,ci_users.id as id_ppds');
        $this->builder->join('role', 'role.id = ci_users.role');
        $this->builder->where(['aktif' => 0]);
        $query = $this->builder->get()->getResultArray();
        return $query;
    }

    public function aktivate($id_ppds)
    {
        $this->builder->set('aktif', 1);
        $this->builder->where('id', $id_ppds);
        $this->builder->update();

        return true;
    }

    public function countNewUsers()
    {
        $this->builder->select('*');
        $this->builder->where('aktif', 0);
        return $this->builder->countAllResults();
    }

    public function jumlahPpdsBimbinganSaya($id_spv)
    {
        $this->builder->select("*");
        $this->builder->where(
            [
                'aktif' => 1,
                'spv' => $id_spv
            ]
        );
        return $this->builder->countAllResults();
    }

    public function jumlahPpdsStatseSaya($id_stase)
    {
        $this->tableName('stase_ppds')->selectMax('id')->where(['id_user']);

        return $this->db->query(
            "SELECT count(id_user) as jumlah FROM `ci_users` 
            LEFT JOIN stase_ppds ON stase_ppds.id_user = ci_users.id
            WHERE  stase_ppds.id_user = (
                SELECT id_user FROM stase_ppds  
                WHERE id = (
                SELECT MAX(id) FROM stase_ppds
                    WHERE id_user = ci_users.id
                )
                AND stase_ppds.id_stase = $id_stase
            )"
        )->getRowObject();
    }
}
