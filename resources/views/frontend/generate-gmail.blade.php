<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>-</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    @php
        $names = [
        "Aditya Pratama",
        "Bima Mahendra",
        "Cahyo Dwi Nugroho",
        "Daffa Ramadhan",
        "Eka Saputra",
        "Farhan Rizki",
        "Galang Prakoso",
        "Hafiz Alfarizi",
        "Ilham Pradipta",
        "Juna Erlangga",
        "Kelvin Arya Pradana",
        "Lucky Maheswara",
        "Miko Ardiansyah",
        "Nanda Wirawan",
        "Oskar Yanuar",
        "Putra Adinata",
        "Rizky Setiawan",
        "Satria Nugraha",
        "Tama Wiratama",
        "Umar Faisal",
        "Vito Adinata",
        "Wahyu Hidayat",
        "Xaverius Darren",
        "Yoga Firmansyah",
        "Zidan Ramzi",
        "Alvin Mahardhika",
        "Bagas Prakoso",
        "Cakra Pradipta",
        "Darius Ferdian",
        "Evan Kurniawan",
        "Fariz Naufal",
        "Genta Maheswara",
        "Haris Susanto",
        "Irwan Syahputra",
        "Jovan Alfariz",
        "Kevin Wijaya",
        "Leon Mahendra",
        "Mario Septian",
        "Niko Yudistira",
        "Ogi Prakoso",
        "Pandu Aryasena",
        "Qaisar Rizky",
        "Radit Kurniawan",
        "Samuel Prasetyo",
        "Theo Adiwira",
        "Umar Rizal",
        "Varel Santoso",
        "Wisnu Adiputra",
        "Xeno Pramana",
        "Yudha Ramadhan",
        "Zaki Ardana",
        "Abiyu Mahendra",
        "Bara Nugraha",
        "Carlo Febrian",
        "Dhanar Putra",
        "Elvano Rizki",
        "Farel Adrian",
        "Gibran Yudistira",
        "Hendra Wijaya",
        "Iqbal Ramadhan",
        "Jodi Septian",
        "Kenzo Aryaputra",
        "Luthfi Rahman",
        "Malik Ananda",
        "Naufal Ghani",
        "Odi Prasetyo",
        "Pramudya Setiawan",
        "Qomarudin Firman",
        "Reno Mahardika",
        "Surya Wicaksono",
        "Tegar Adiwangsa",
        "Umar Nugroho",
        "Vano Prayoga",
        "Wira Adinata",
        "Zafran Elkan",
        "Norman Kurniawan",
        "Rafli Pratama",
        "Sandi Wijaya",
        "Taufik Hidayat",
        "Yogi Saputra",
        "Zidan Prakoso",
        "Yusuf Ramadhan",
        "Agus Setiawan",

        // Perempuan
        "Ayu Maharani",
        "Bella Oktaviani",
        "Citra Ramadhani",
        "Dewi Aulia",
        "Eka Putri",
        "Fani Safitri",
        "Gina Kartika",
        "Hana Lestari",
        "Intan Novita",
        "Jessica Felicia",
        "Kirana Ayudia",
        "lola Salsabila",
        "Maya Sari",
        "Nina Salsabila",
        "Olga Prameswari",
        "Pia Rahmawati",
        "Lala Permata",
        "Mega Sari",
        "Nia Rosalia",
        "Olivia Mentari",
        "Putri Annisa",
        "Qory Rahmadhani",
        "Rani Kusuma",
        "Salsa Nabila",
        "Tiara Chandrawati",
        "Ulya Safira",
        "Vina Lestari",
        "Widya Puspita",
        "Xena Marissa",
        "Yuliana Putri",
        "Zara Adelia",
        "Amelia Prameswari",
        "Bilqis Fadila",
        "Clarissa Nirmala",
        "Dinda Ayuningtyas",
        "Erika Rachma",
        "Fira Ardiani",
        "Gladys Wulandari",
        "Hilya Nuraini",
        "Indri Purnamasari",
        "Jihan Andini",
        "Keira Anggraini",
        "Livia Amara",
        "Marsha Levina",
        "Nadya Febriana",
        "Ochi Rahayu",
        "Puspita Devi",
        "Qiana Rafani",
        "Raline Safira",
        "Syifa Zahra",
        "Tasya Maharani",
        "Ulfa Septiani",
        "Vania Oktavia",
        "Winda Prameswari",
        "Xyla Putri",
        "Yola Permatasari",
        "Ziva Azzahra",
        "Aisyah Nuraini",
        "Briliana Putri",
        "Celine Mareta",
        "Devina Maharani",
        "Elvina Lestari",
        "Feby Azzahra",
        "Ghea Pramudita",
        "Hani Permata",
        "Inara Aurel",
        "Jelita Sari",
        "Kanya Kartika",
        "Lesty Ayunda",
        "Mita Azzahra",
        "Novia Kurniasih",
        "Olin Prameswari",
        "Qonita Azzahra",
        "Rahayu Sintia",
        "Syifa Amalia",
        "Tika Nurjannah",
        "Umi Fatimah",
        "Vira Kurniasari",
        "Wanda Lestari",
        "Yulita Ayu"
    ];
    $email = strtolower(collect(str_replace(' ', '', $names))->random()).rand(100, 999).'@gmail.com';
    if (session('generate_gmail') == false) {
        $email = '';
    }
    @endphp
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="mb-4 text-center">Generate Gmail Akun</h4>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="gmailInput" placeholder="Gmail akun akan muncul di sini" 
                            value="{{ $email }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="copyBtn">Salin</button>
                        </div>
                        <div class="d-grid">
                            <form action="" method="get" class="text-center">
                                @csrf
                                <input type="hidden" name="generate_gmail" value="1">
                                <button class="btn btn-primary" type="submit" id="generateBtn">Generate Gmail Akun</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</body>
<script>
    $(document).ready(function() {
        $('#copyBtn').on('click', function() {
            const input = document.getElementById('gmailInput');
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            $(this).text('Disalin!');
            setTimeout(() => {
                $('#copyBtn').text('Salin');
            }, 1200);
        });
    });
</script>
</html>