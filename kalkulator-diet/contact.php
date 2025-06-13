<?php include 'header.php'; ?>
<div class="container mt-5">
    <h1 class="mb-3">Hubungi Kami</h1>
    <p>Jika Anda memiliki pertanyaan atau butuh bantuan, silakan hubungi kami melalui:</p>
    <ul>
        <li>📧 Email: <a href="mailto:support@healthyreminder.com">support@healthyreminder.com</a></li>
        <li>📞 Telepon: +62 812-3456-7890</li>
        <li>📍 Alamat: Jl. Sehat No. 123, Jakarta, Indonesia</li>
    </ul>

    <h3>Kirim Pesan</h3>
    <form action="send_message.php" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Nama:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Pesan:</label>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim</button>
    </form>
</div>
<?php include 'footer.php'; ?>
