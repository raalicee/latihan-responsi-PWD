<?php
session_start();
include 'koneksi.php';
include 'searching.php';

if (!isset($_SESSION['user_id'])) { // Cek apakah user sudah login
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// mengambil data prodi dari db
$sql_prodi = "SELECT * FROM prodi";
$result_prodi = $koneksi->query($sql_prodi);
if (!$result_prodi) {
    die("Query prodi gagal: " . $koneksi->error . " | Query: " . $sql_prodi);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="home.css">
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <h3 class="navbar-brand">Dashboard Mahasiswa</h3>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="home.php">Home</a> </li>
                    <li class="nav-item">
                        <a class="nav-link" href="prodi.php">Prodi</a> </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link logout-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-custom-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-custom-error alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMahasiswaModal">
                Tambah Mahasiswa
            </button>
            <form action="home.php" method="GET" class="input-group w-25">
                <input type="text" class="form-control" placeholder="cari nama mahasiswa" aria-label="cari nama mahasiswa" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-outline-secondary" type="submit">Cari</button>
            </form>
        </div>

        <?php if ($is_search): ?> 
            <div class="search-results-info">
                Hasil Pencarian : <strong><?php echo htmlspecialchars($search_query); ?></strong><br>
                <a href="home.php">Tampilkan semua</a>
            </div>
        <?php endif; ?>
        

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Angkatan</th>
                        <th>Prodi</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_mahasiswa->num_rows > 0) {
                        while ($row = $result_mahasiswa->fetch_assoc()) { // ambil data mahasiswa yg mau ditampilkan
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['angkatan']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_prodi']) . "</td>";
                            echo "<td>";
                            echo "<a href='edit.php?nim=" . urlencode($row['nim']) . "' class='btn btn-primary btn-sm me-2'>Edit</a>";
                            echo "<a href='delete.php?nim=" . urlencode($row['nim']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Yakin ingin menghapus data ini?');\">Hapus</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Tidak ada data mahasiswa. Silakan tambahkan!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addMahasiswaModal" tabindex="-1" aria-labelledby="addMahasiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMahasiswaModalLabel">Tambah Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="inputdata.php" method="POST">
                        <input type="hidden" name="action" value="tambah_mahasiswa">
                        <div class="mb-3">
                            <label for="nim" class="form-label">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="angkatan" class="form-label">Angkatan</label>
                            <input type="number" class="form-control" id="angkatan" name="angkatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="prodi" class="form-label">Prodi</label>
                            <select class="form-select" id="prodi" name="prodi" required>
                                <option value="">Pilih Prodi</option>
                                <?php
                                if ($result_prodi->num_rows > 0) {
                                    $result_prodi->data_seek(0);
                                    while ($row_prodi = $result_prodi->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row_prodi['id_prodi']) . "'>" . htmlspecialchars($row_prodi['nama_prodi']) . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Tidak ada prodi</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>