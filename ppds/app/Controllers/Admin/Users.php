<?php

namespace App\Controllers\Admin;

use App\Libraries\Notif;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\TahapModel;
use App\Models\SupervisorModel;
use App\Controllers\BaseController;
use App\Models\StasePpdsModel;
use App\Models\StaseModel;
use App\Models\NotifModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Users extends BaseController
{
    protected $user_model;
    protected $role_model;
    protected $tahap_model;
    protected $stase_ppds_model;
    protected $stase_model;
    protected $spv_model;
    protected $notif_model;
    protected $notif;
    public function __construct()
    {
        $this->user_model = new UserModel();
        $this->role_model = new RoleModel();
        $this->tahap_model = new TahapModel();
        $this->stase_ppds_model = new StasePpdsModel();
        $this->stase_model = new StaseModel();
        $this->spv_model = new SuperVisorModel();
        $this->notif_model = new NotifModel();
        $this->notif = new Notif();
    }

    public function view()
    {
        $data['title'] = 'Daftar User';
        $data['query'] = $this->user_model->getAll();
        $data['page_header'] = 'Users list';
        return view('admin/users/index', $data);
    }

    public function newUsersList()
    {
        $data['title'] = 'Daftar User';
        $data['query'] = $this->user_model->getNewUsers();
        $data['page_header'] = 'Users list';
        return view('admin/users/index', $data);
    }
    public function allppds()
    {
        $data['title'] = 'Semua Residen';
        $data['query'] = $this->user_model->getPpds();
        $data['page_header'] = 'Semua Residen';
        return view('admin/users/residen', $data);
    }

    public function show()
    {
        $data = [
            'title' => 'Tambah Pengguna Baru',
            'page_header' => 'Tambah Pengguna Baru',
            'validation' => \Config\Services::validation(),
            'role' => $this->role_model->findAll(),
            'spv' => $this->user_model->getSpv(),
            'stase' => $this->stase_model->getAllStase(),
        ];
        return view('admin/users/add', $data);
    }

    public function post()
    {
        $username = $this->request->getVar('username');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $role = $this->request->getVar('role');
        $supervisor = $this->request->getVar('spv');
        $stase = $this->request->getVar('stase');

        if ($role == 4) {
            $rules = [
                'username' => [
                    'rules' => 'required|is_unique[ci_users.username]',
                    'errors' => [
                        'required' => 'username harus diisi',
                        'is_unique' => 'username sudah terpakai'
                    ]
                ],
                'email' => [
                    'rules' => 'required|is_unique[ci_users.email]|valid_email',
                    'errors' => [
                        'required' => 'email harus diisi',
                        'is_unique' => 'email sudah terpakai',
                        'valid_email' => 'email tidak valid'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'password harus diisi',
                    ]
                ],
                'role' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'role harus diisi',
                    ]
                ],
                'spv' => [
                    'rules' => ['required'],
                    'errors' => [
                        'required' => 'supervisor harus diisi',
                    ]
                ]
            ];
        } elseif ($role == 3) {
            $rules = [
                'username' => [
                    'rules' => 'required|is_unique[ci_users.username]',
                    'errors' => [
                        'required' => 'username harus diisi',
                        'is_unique' => 'username sudah terpakai'
                    ]
                ],
                'email' => [
                    'rules' => 'required|is_unique[ci_users.email]|valid_email',
                    'errors' => [
                        'required' => 'email harus diisi',
                        'is_unique' => 'email sudah terpakai',
                        'valid_email' => 'email tidak valid'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'password harus diisi',
                    ]
                ],
                'role' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'role harus diisi',
                    ]
                ],
                'stase' => [
                    'rules' => ['required'],
                    'errors' => [
                        'required' => 'stase harus diisi',
                    ]
                ]
            ];
        }

        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return redirect()->back()->withInput()->with('validation', $validation);
        }

        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'spv' => $supervisor,
            'stase' => $stase,
            'aktif' => 1,
        ];

        // dd($data);

        $is_supervisor_exist = $this->spv_model->getSpecificSpv($supervisor);

        if ($data['role'] == 4) {
            if ($is_supervisor_exist) {
                if ($this->user_model->insert($data)) {
                    date_default_timezone_set('Asia/Jakarta');
                    $ppds_id = $this->user_model->getInsertID();
                    $dataForTahap = [
                        'id_user' => $ppds_id,
                        'id_tahap' => 1,
                        'tanggal_mulai' => date("Y-m-d")
                    ];
                    $this->tahap_model->insert($dataForTahap);

                    $dataForStase = [
                        'id_user' => $ppds_id,
                        'id_stase' => 1,
                        'tanggal_mulai' => date("Y-m-d")
                    ];
                    $this->stase_ppds_model->insert($dataForStase);
                    return redirect()->to(base_url('/admin/users'))->with('success', 'Pengguna baru berhasil ditambahkan!');
                }
            } else {
                return redirect()->back()->withInput()->with('danger', 'Data supervisor tidak ditemukan!');
            }
        } else {
            if ($this->user_model->insert($data)) {
                return redirect()->to(base_url('/admin/users'))->with('success', 'Pengguna baru berhasil ditambahkan!');
            }
        }
    }

    public function delete($user_id)
    {
        // $result = $this->user_model->delete($user_id);
        if ($this->user_model->delete($user_id)) {
            // return redirect()->to(base_url('admin/users'))->with('success', 'Pengguna berhasil dihapus!');
            return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
        } else {
            return redirect()->back()->with('danger', 'Terjadi kesalahan saat menghapus data');
        }
    }

    public function detail($id_ppds)
    {
        // $data = [
        //     'title' => 'Detail User',
        //     'page_header' => 'Detail User',
        //     'data_user' => $this->user_model->userProfile($id_ppds),
        //     'validation' => \Config\Services::validation(),
        // ];

        // // dd($data);
        // return view('admin/users/detail', $data);

        session();
        $data = [
            'data_user' => $this->user_model->userProfile($id_ppds),
            'title' => 'Detail User',
            'page_header' => 'Detail User',
            'validation' => \Config\Services::validation(),
        ];

        // dd($data);

        return view('user/profile', $data);
    }

    public function lobby()
    {
        $data = [
            'title' => 'Lobby PPDS',
            'page_header' => 'Lobby PPDS',
            'query' => $this->user_model->getPpdsWithoutStase(),
        ];

        // dd($data);

        return view('admin/ppds/lobby', $data);
    }

    public function postPpdsStase()
    {
        $data = [
            'id' => $this->request->getVar('id_stase_ppds'),
            'id_stase' => $this->request->getVar('id_stase'),
            'id_user' => $this->request->getVar('id_ppds'),
        ];

        if ($this->stase_ppds_model->changePpdsStase($data)) {
            // $this->notif_model->insert();
            return redirect()->back()->with('success', 'Stase PPDS berhasil diubah');
        }
    }

    public function ppdsPerStase()
    {
        $data = [
            'menu_id' => 'menu_ppds',
            'menu_class' => 'stase_saya',
            'title' => 'PPDS Stase Saya',
            'page_header' => 'PPDS Stase Saya',
            'query' => $this->user_model->getPpdsByStase(),
        ];

        return view('admin/ppds/index', $data);
    }

    public function ppdsPerSpv()
    {
        $data = [
            'menu_id' => 'menu_ppds',
            'menu_class' => 'bimbingan_saya',
            'title' => 'PPDS Bimbingan Saya',
            'page_header' => 'PPDS Bimbingan Saya',
            'query' => $this->user_model->getPpdsBySpv(),
        ];

        return view('admin/ppds/index', $data);

        // dd($data);
    }

    public function staseBerjalan()
    {
      
        $data = [
            'menu_id' => 'staseBerjalan',
            'title' => 'Stase Aktif',
            'page_header' => 'Stase yang sedang Aktif',
            'query' => $this->user_model->getPpdsByTahap(0),
        ];

        // dd($data);

        // dd($this->user_model->getPpdsByTahap($id_tahap));
        return view('admin/ppds/index', $data);
    }

    public function ppds($id_tahap = 0)
    {
        unset($_SESSION['stase']);

        // if(isset($_SESSION['stase'])) {
        //    echo $_SESSION['stase'];
        // } else {
        //     echo "ga ada sesi";
        // }

        if($id_tahap > 0 ) {
            $menu_id = 'arsip-pdps';
        } else {
            $menu_id = 'staseBerjalan';
        }

        $data = [
            'tahap' => $id_tahap,
            'menu_id' => $menu_id,
            'menu_class' => 'tahap-' . $id_tahap,
            'title' => ($id_tahap != 0 ? 'Arsip PPDS' : 'PPDS'),
            'page_header' => ($id_tahap != 0 ? 'Arsip PPDS Tahap ' . $id_tahap : 'Daftar Semua PPDS'),
            'stase' => $this->stase_model->getStaseByTahap($id_tahap),
        ];

        // dd($data);

        // dd($this->user_model->getPpdsByTahap($id_tahap));
        return view('admin/ppds/ppds_pertahap_ajx', $data);
    }

    public function get_ppds_pertahap_json($id_tahap = 0)
    {

        $records['data'] = $this->user_model->get_ppds_pertahap($id_tahap);

       //  dd($records['data']);
        $url = (session('role') == 1 ? 'admin': 'supervisor');
        
		$data = array();	
		foreach ($records['data']  as $row) 
		{  	
            $keterangan = ($row['keterangan'] ? '<button type="button" data-toggle="tooltip" data-placement="top" title="Selesai dengan catatan" class="btn btn-flat btn-outline-danger btn-xs catatan"><span class="ti-alert"></span></button>': '');

			$data[]= array(
				$row['nama_lengkap'],
				$row['stase'],
				$row['tanggal_mulai'],
				$row['tanggal_selesai'],
			
                '<a href="' . base_url( $url . '/ppds/' . $row['id_ppds'] ) . '" class="btn btn-flat btn-outline-success btn-xs"><span class="ti-eye"></span></a>',			
                $keterangan,			
			);
		}
		$records['data'] = $data;
		echo json_encode($records);	
      
    }

    public function get_ppds_filter_json() {
        $stase = $this->request->getVar('stase');
        $session = session();
        $session->set('stase', $stase);
    }

    public function supervisor()
    {
        $data = [
            'menu_id' => 'menu_spv',
            'menu_class' => 'spv',
            'title' => 'Supervisor',
            'page_header' => 'Daftar Supervisor',
            'query' => $this->user_model->getAllSupervisor(),
        ];
        // dd($this->user_model->getAllSupervisor());
        return view('admin/spv/index', $data);
    }

    public function detailSupervisor($id_spv)
    {
        $data = [
            'title' => 'Supervisor',
            'page_header' => 'Detail Supervisor',
            'spv' => $this->user_model->detailSpv($id_spv),
        ];
        // dd($this->user_model->detailSpv($id_spv));
        return view('admin/spv/detail', $data);
        // echo "detail spv";
    }

    public function detailppds($id_ppds)
    {
        $db = \Config\Database::connect();

        $current_tahap_ppds = $db->query(
            "SELECT id_tahap FROM tahap_ppds WHERE id = (
                SELECT MAX(id) FROM tahap_ppds WHERE id_user = $id_ppds
            )"
        )->getRowObject()->id_tahap;

        $jumlah_stase_ppds_on_current_tahap = $db->query(
            "SELECT COUNT(stase_ppds.id) AS jumlah_stase FROM stase_ppds
            LEFT JOIN stase ON stase.id = stase_ppds.id_stase
            WHERE stase_ppds.id_user = $id_ppds AND stase.id_tahap = $current_tahap_ppds"
            //  AND stase_ppds.tanggal_selesai != 0
        )->getRowObject()->jumlah_stase;

        $jumlah_stase_per_tahap = $db->query(
            "SELECT COUNT(id) AS jumlah_stase FROM stase WHERE id_tahap = $current_tahap_ppds"
        )->getRowObject()->jumlah_stase;

        if ($jumlah_stase_ppds_on_current_tahap == $jumlah_stase_per_tahap) {
            $tahap_selesai = true;
        } else {
            $tahap_selesai = false;
        }

        $jumlah_tahap_ppds = $db->query(
            "SELECT COUNT(tahap_ppds.id) AS jumlah_tahap FROM tahap_ppds
            LEFT JOIN tahap ON tahap.id = tahap_ppds.id_tahap
            WHERE tahap_ppds.id_user = $id_ppds AND tahap_ppds.tanggal_selesai != 0"
        )->getRowObject()->jumlah_tahap;

        if ($jumlah_tahap_ppds == 4) {
            $semua_tahap_selesai = true;
        } else {
            $semua_tahap_selesai = false;
        }

        // dd($jumlah_stase_ppds_on_current_tahap);

        $data = [
            'title' => 'Detail User',
            'page_header' => 'Detail User',
            'ppds' => $this->user_model->getUserById($id_ppds),
            'validation' => \Config\Services::validation(),
            'tahap' => $db->query("SELECT * FROM tahap")->getResultArray(),
            'tahap_selesai' => $tahap_selesai,
            'semua_tahap_selesai' => $semua_tahap_selesai,
            'idppds' => $id_ppds
        ];
        // dd($data);
        return view('admin/ppds/detail', $data);
    }

    // public function sendNotif($data)
    // {
    //     
    // }

    public function staseSelesai()
    {
        $id_ppds = $this->request->getVar('id_ppds');
        $tahap_ppds = $this->tahap_model->getPpdsTahap($id_ppds);
        $jumlah_stase_per_tahap = $this->stase_model->countStasePerTahap($tahap_ppds);
        $jumlah_ppds_stase = $this->stase_ppds_model->countPpdsStaseByTahap($id_ppds, $tahap_ppds);

        $info_ppds = $this->user_model->userProfile($id_ppds);
        $email_ppds = $info_ppds->email;

        // dd($this->request->getVar());

        $keterangan = $this->request->getVar('keterangan');

        if ($this->request->getVar('type') == 'stase') {
            if ($this->user_model->staseSelesai($id_ppds, $keterangan)) {
                date_default_timezone_set('Asia/Jakarta');
                $dataForStase = [
                    'id_user' => $id_ppds,
                    'id_stase' => 25,
                    'tanggal_mulai' => date("Y-m-d")
                ];

                if ($this->stase_ppds_model->insert($dataForStase)) {
                    $this->notif->send_mail($email_ppds, 'stase selesai', 'anda telah menyelesaikan stase');
                    // $this->naikTahapOrStaseMailer($id_ppds);
                    $this->notif->send_notif($id_ppds, 'stase selesai', 'ada telah menyelesaikan stase', '');
                    return redirect()->to(base_url('/admin/ppds/lobby'))->with('success', 'PPDS telah menyelesaikan stase');
                }
            }
        } elseif ($this->request->getVar('type') == 'tahap') {
            if ($tahap_ppds < 4) {
                if ($this->user_model->staseSelesai($id_ppds)) {
                    date_default_timezone_set('Asia/Jakarta');
                    $dataForStase = [
                        'id_user' => $id_ppds,
                        'id_stase' => 25,
                        'tanggal_mulai' => date("Y-m-d")
                    ];
                    // $this->naikTahapOrStaseMailer($id_ppds);
                    $this->notif->send_mail($email_ppds, 'stase selesai', 'anda telah menyelesaikan stase');

                    $this->stase_ppds_model->insert($dataForStase);
                    if ($this->user_model->tahapSelesai($id_ppds)) {
                        $dataForTahap = [
                            'id_user' => $id_ppds,
                            'id_tahap' => $tahap_ppds + 1,
                            'tanggal_mulai' => date("Y-m-d")
                        ];

                        // $this->naikTahapOrStaseMailer($id_ppds);

                        $this->notif->send_mail($email_ppds, 'stase selesai', 'anda telah menyelesaikan stase');

                        $this->tahap_model->insert($dataForTahap);

                        return redirect()->to(base_url('/admin/ppds/lobby'))->with('success', 'PPDS dinyatakan naik ke tahap selanjutnya');
                    }
                }
            } else {
                if ($this->user_model->tahapSelesai($id_ppds)) {
                    $this->user_model->staseSelesai($id_ppds);
                    $this->notif->send_mail($email_ppds, 'tahap selesai', 'anda telah menyelesaikan semua tahap');
                    return redirect()->back()->with('success', 'PPDS dinyatakan telah menyelesaikan semua tahap');
                }
            }
        }
    }

    public function naikTahapOrStaseMailer($id_ppds)
    {
        require_once "vendor/autoload.php";

        $info_ppds = $this->user_model->userProfile($id_ppds);
        $email_ppds = $info_ppds->email;

        //PHPMailer Object
        $mail = new PHPMailer(true); //Argument true in constructor enables exceptions

        //From email address and name
        $mail->From = "admin@miokard.solusidesain.net";
        // $mail->FromName = "Full Name";

        //To address and name
        $mail->addAddress($email_ppds); //Recipient name is optional

        //Address to which recipient will reply
        // $mail->addReplyTo("reply@yourdomain.com", "Reply");

        //CC and BCC
        // $mail->addCC("cc@example.com");
        // $mail->addBCC("bcc@example.com");

        //Send HTML or Plain Text email
        $mail->isHTML(true);

        $mail->Subject = "Naik Tahap";
        $mail->Body = "<i>Anda dinyatakan naik ke tahap selanjutnya</i>";
        $mail->AltBody = "Anda dinyatakan naik ke tahap selanjutnya";

        try {
            $mail->send();
            echo "Message has been sent successfully";
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }

    public function activate($id_ppds)
    {
        if ($this->user_model->aktivate($id_ppds)) {
            date_default_timezone_set('Asia/Jakarta');
            $ppds_id = $id_ppds;
            $dataForTahap = [
                'id_user' => $ppds_id,
                'id_tahap' => 1,
                'tanggal_mulai' => date("Y-m-d")
            ];
            $this->tahap_model->insert($dataForTahap);

            $dataForStase = [
                'id_user' => $ppds_id,
                'id_stase' => 1,
                'tanggal_mulai' => date("Y-m-d")
            ];
            $this->stase_ppds_model->insert($dataForStase);
            return redirect()->to(base_url('admin/new_users'))->with('success', 'Pengguna berhasil diaktifkan!');
        } else {
            return redirect()->back()->with('danger', 'Terjadi kesalahan!');
        }
    }
}
